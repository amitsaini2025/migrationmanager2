<?php

namespace App\Support\EmployerSpreadsheetImport;

/**
 * Decides which existing company a spreadsheet row belongs to, then which blank fields can be filled.
 * It never proposes a new company.
 */
final class CompanyContactBackfillPlanner
{
    /**
     * @param  list<CompanyContactRow>  $rows
     * @param  array{
     *     matters: array<string, int>,
     *     companies: array<int, array<string, mixed>>
     * }  $catalog
     * @return array<string, mixed>
     */
    public static function plan(array $rows, array $catalog): array
    {
        $grouped = [];
        $skipped = [];

        foreach ($rows as $row) {
            $decision = self::matchCompany($row, $catalog);
            if ($decision['company_id'] === null) {
                $skipped[] = [
                    'matter_id' => $row->matterId,
                    'business' => $row->business,
                    'reason' => $decision['reason'],
                ];

                continue;
            }

            $companyId = $decision['company_id'];
            if (! isset($grouped[$companyId])) {
                $grouped[$companyId] = [
                    'rows' => [],
                    'via' => $decision['via'],
                ];
            }
            $grouped[$companyId]['rows'][] = $row;
            if ($decision['via'] === 'matter') {
                $grouped[$companyId]['via'] = 'matter';
            }
        }

        $actions = [];
        $warnings = [];
        $unchanged = 0;
        foreach ($grouped as $companyId => $group) {
            $action = self::fillAction($catalog['companies'][$companyId], $group['rows'], $group['via'], $catalog);
            foreach ($action['warnings'] as $warning) {
                $warnings[] = $action['name'].': '.$warning;
            }
            if ($action['changes'] === []) {
                $unchanged++;

                continue;
            }
            $actions[] = $action;
        }

        $reasons = array_count_values(array_column($skipped, 'reason'));

        return [
            'rows' => count($rows),
            'matched_companies' => count($grouped),
            'matched_by_matter' => count(array_filter($grouped, fn (array $group): bool => $group['via'] === 'matter')),
            'matched_by_name' => count(array_filter($grouped, fn (array $group): bool => $group['via'] === 'name')),
            'companies_to_update' => count($actions),
            'companies_unchanged' => $unchanged,
            'not_found' => $reasons['not_found'] ?? 0,
            'conflicts' => $reasons['conflict'] ?? 0,
            'ambiguous' => $reasons['ambiguous'] ?? 0,
            'actions' => $actions,
            'warnings' => $warnings,
            'skipped' => $skipped,
        ];
    }

    /**
     * @param  array<string, mixed>  $catalog
     * @return array{company_id: ?int, via: ?string, reason: ?string}
     */
    private static function matchCompany(CompanyContactRow $row, array $catalog): array
    {
        $matterCompanyId = self::matterCompanyId($row->matterId, $catalog['matters']);
        if ($matterCompanyId === 0) {
            return ['company_id' => null, 'via' => null, 'reason' => 'ambiguous'];
        }

        $nameHits = self::nameHits($row, $catalog['companies']);
        if ($matterCompanyId !== null) {
            if ($nameHits !== [] && ! in_array($matterCompanyId, $nameHits, true)) {
                return ['company_id' => null, 'via' => null, 'reason' => 'conflict'];
            }

            return ['company_id' => $matterCompanyId, 'via' => 'matter', 'reason' => null];
        }

        if (count($nameHits) === 1) {
            return ['company_id' => $nameHits[0], 'via' => 'name', 'reason' => null];
        }
        if (count($nameHits) > 1) {
            return ['company_id' => null, 'via' => null, 'reason' => 'ambiguous'];
        }

        return ['company_id' => null, 'via' => null, 'reason' => 'not_found'];
    }

    /**
     * @param  array<string, int>  $matters
     */
    private static function matterCompanyId(string $matterId, array $matters): ?int
    {
        $exact = self::matterKey($matterId);
        $compact = self::compactKey($matterId);
        $exactId = $exact !== '' ? ($matters[$exact] ?? null) : null;
        $compactId = $compact !== '' && $compact !== $exact ? ($matters[$compact] ?? null) : null;

        if ($exactId === 0 || $compactId === 0) {
            return 0;
        }
        if ($exactId !== null && $compactId !== null && $exactId !== $compactId) {
            return 0;
        }

        return $exactId ?? $compactId;
    }

    /**
     * @param  array<int, array<string, mixed>>  $companies
     * @return list<int>
     */
    private static function nameHits(CompanyContactRow $row, array $companies): array
    {
        $hits = [];
        foreach ($row->matchNames() as $name) {
            $key = BusinessName::groupKey($name);
            if ($key === '') {
                continue;
            }
            foreach ($companies as $company) {
                foreach ($company['names'] as $candidate) {
                    $candidateKey = BusinessName::groupKey((string) $candidate);
                    if ($candidateKey !== '' && CompanyNameMatcher::sameBusiness($key, $candidateKey)) {
                        $hits[(int) $company['id']] = true;
                    }
                }
            }
        }

        return array_map('intval', array_keys($hits));
    }

    /**
     * @param  array<string, mixed>  $company
     * @param  list<CompanyContactRow>  $rows
     * @param  array<string, mixed>  $catalog
     * @return array<string, mixed>
     */
    private static function fillAction(array $company, array $rows, string $via, array $catalog): array
    {
        $changes = [];
        $warnings = [];

        $trading = self::firstText($rows, fn (CompanyContactRow $row): ?string => $row->contactTrading ?? $row->trading);
        if (self::blank($company['trading_name'] ?? null) && $trading !== null && ! self::sameText($trading, $company['legal_name'] ?? null)) {
            $changes['trading_name'] = $trading;
        }

        $abn = self::firstDigits($rows, fn (CompanyContactRow $row): ?string => $row->abn, 11);
        if (self::blank($company['abn'] ?? null) && $abn !== null) {
            $changes['ABN_number'] = $abn;
        } elseif (self::blank($company['abn'] ?? null) && self::firstText($rows, fn (CompanyContactRow $row): ?string => $row->abn) !== null) {
            $warnings[] = 'ABN was not 11 digits, so it was left blank.';
        }

        $acn = self::firstDigits($rows, fn (CompanyContactRow $row): ?string => $row->acn, 9);
        if ($acn === null && isset($changes['ABN_number'])) {
            $acn = substr($changes['ABN_number'], 2);
        }
        if (self::blank($company['acn'] ?? null) && $acn !== null) {
            $changes['ACN'] = $acn;
        }

        $structure = self::firstText($rows, fn (CompanyContactRow $row): ?string => $row->structure);
        $companyType = self::companyType($structure);
        if (self::blank($company['company_type'] ?? null) && $companyType !== null) {
            $changes['company_type'] = $companyType;
        } elseif (self::blank($company['company_type'] ?? null) && $structure !== null && $companyType === null) {
            $warnings[] = 'Business structure "'.$structure.'" is not a CRM business type.';
        }

        $industry = self::firstText($rows, fn (CompanyContactRow $row): ?string => $row->industry);
        if (self::blank($company['industry'] ?? null) && $industry !== null) {
            $changes['main_business_activity'] = $industry;
        }

        $start = self::firstText($rows, fn (CompanyContactRow $row): ?string => $row->startDate);
        if (self::blank($company['operating_since'] ?? null) && $start !== null) {
            $changes['business_operating_since'] = $start;
        }

        $phone = self::firstText($rows, fn (CompanyContactRow $row): ?string => $row->businessMobile ?? $row->businessPhone ?? $row->matterPhone);
        if (self::blank($company['phone'] ?? null) && $phone !== null && ! self::ownedByOther($catalog['phone_owners'] ?? [], $phone, (int) $company['admin_id'])) {
            $changes['phone'] = $phone;
        }

        $email = self::firstText($rows, fn (CompanyContactRow $row): ?string => $row->businessEmail);
        if (self::blank($company['email'] ?? null) && $email !== null && ! self::ownedByOther($catalog['email_owners'] ?? [], $email, (int) $company['admin_id'])) {
            $changes['email'] = $email;
        }

        $address = self::address($rows);
        if (! ($company['has_address'] ?? false) && $address !== null) {
            $changes['address'] = $address;
        }

        $directors = self::directors($rows, $company, $catalog);
        if ($directors['create'] !== []) {
            $changes['directors'] = $directors['create'];
        }
        if ($directors['patch'] !== []) {
            $changes['director_patches'] = $directors['patch'];
        }

        if (self::blank($company['contact_person_id'] ?? null) && $directors['contact_person_id'] !== null) {
            $changes['contact_person_id'] = $directors['contact_person_id'];
        }
        if (self::blank($company['contact_person_position'] ?? null) && $directors['contact_position'] !== null) {
            $changes['contact_person_position'] = $directors['contact_position'];
        }

        return [
            'type' => 'fill_company',
            'company_id' => (int) $company['id'],
            'admin_id' => (int) $company['admin_id'],
            'name' => (string) ($company['legal_name'] ?? ''),
            'via' => $via,
            'changes' => $changes,
            'warnings' => $warnings,
        ];
    }

    /**
     * @param  list<CompanyContactRow>  $rows
     * @param  array<string, mixed>  $company
     * @param  array<string, mixed>  $catalog
     * @return array{create: list<array<string, mixed>>, patch: list<array<string, mixed>>, contact_person_id: ?int, contact_position: ?string}
     */
    private static function directors(array $rows, array $company, array $catalog): array
    {
        $existing = [];
        foreach ($company['directors'] ?? [] as $director) {
            $existing[self::personKey((string) ($director['name'] ?? ''))] = $director;
        }

        $create = [];
        $patch = [];
        $contactPersonId = null;
        $contactPosition = null;
        $contact = self::chosenContact($rows);
        if ($contact === null) {
            return [
                'create' => [],
                'patch' => [],
                'contact_person_id' => null,
                'contact_position' => null,
            ];
        }

        $name = self::personName($contact['given'] ?? null, $contact['surname'] ?? null);
        $key = self::personKey((string) $name);
        $personId = self::personId($contact, $catalog);
        if ($personId !== null) {
            $contactPersonId = $personId;
        }
        if (! self::blank($contact['position'] ?? null)) {
            $contactPosition = $contact['position'];
        }

        if (isset($existing[$key])) {
            $current = $existing[$key];
            $dob = ($current['has_dob'] ?? false) ? null : ($contact['dob'] ?? null);
            $role = ($current['has_role'] ?? false) ? null : ($contact['position'] ?? null);
            $clientId = ($current['client_id'] ?? null) ? null : $personId;
            if ($dob !== null || $role !== null || $clientId !== null) {
                $patch[$key] = [
                    'name' => $name,
                    'dob' => $dob,
                    'role' => $role,
                    'client_id' => $clientId,
                ];
            }
        } else {
            $create[$key] = [
                'name' => $name,
                'dob' => $contact['dob'] ?? null,
                'role' => $contact['position'] ?? null,
                'client_id' => $personId,
            ];
        }

        return [
            'create' => array_values($create),
            'patch' => array_values($patch),
            'contact_person_id' => $contactPersonId,
            'contact_position' => $contactPosition,
        ];
    }

    /**
     * @param  array{email?: ?string, phone?: ?string}  $contact
     * @param  array<string, mixed>  $catalog
     */
    private static function personId(array $contact, array $catalog): ?int
    {
        $email = strtolower(trim((string) ($contact['email'] ?? '')));
        if ($email !== '' && isset($catalog['people_by_email'][$email])) {
            return (int) $catalog['people_by_email'][$email];
        }
        $phone = preg_replace('/\D+/', '', (string) ($contact['phone'] ?? '')) ?? '';
        if ($phone !== '' && isset($catalog['people_by_phone'][$phone])) {
            return (int) $catalog['people_by_phone'][$phone];
        }

        return null;
    }

    /**
     * @param  list<CompanyContactRow>  $rows
     * @return array<string, ?string>|null
     */
    private static function address(array $rows): ?array
    {
        foreach ($rows as $row) {
            if ($row->street1 === null && $row->suburb === null && $row->postcode === null) {
                continue;
            }

            return [
                'address_line_1' => $row->street1,
                'address_line_2' => $row->street2,
                'suburb' => $row->suburb,
                'state' => $row->state,
                'zip' => $row->postcode,
                'country' => $row->country,
            ];
        }

        return null;
    }

    private static function companyType(?string $structure): ?string
    {
        $key = strtolower(trim((string) $structure));
        if ($key === '') {
            return null;
        }
        if (str_contains($key, 'proprietary') || str_contains($key, 'pty')) {
            return 'Proprietary Company';
        }
        if (str_contains($key, 'public')) {
            return 'Public Company';
        }
        if (str_contains($key, 'sole')) {
            return 'Sole Trader';
        }
        if (str_contains($key, 'partner')) {
            return 'Partnership';
        }
        if (str_contains($key, 'not for profit') || str_contains($key, 'not-for-profit')) {
            return 'Not-for-Profit';
        }
        if (str_contains($key, 'trust')) {
            return 'Trustee';
        }

        return null;
    }

    /**
     * The company record stores one contact. Later contacts on the same row are ignored.
     *
     * @param  list<CompanyContactRow>  $rows
     * @return array{surname: ?string, given: ?string, position: ?string, type: ?string, dob: ?string, phone: ?string, email: ?string}|null
     */
    private static function chosenContact(array $rows): ?array
    {
        foreach ($rows as $row) {
            foreach ($row->contacts as $contact) {
                if (self::skippedContactType($contact['type'] ?? null)) {
                    continue;
                }
                if (self::personName($contact['given'] ?? null, $contact['surname'] ?? null) === null) {
                    continue;
                }

                return $contact;
            }
        }

        return null;
    }

    private static function skippedContactType(?string $type): bool
    {
        $key = strtolower(trim((string) $type));

        return in_array($key, ['supplier', 'applicant'], true);
    }

    private static function personName(?string $given, ?string $surname): ?string
    {
        $name = trim(trim((string) $given).' '.trim((string) $surname));

        return $name === '' ? null : $name;
    }

    public static function personKey(string $name): string
    {
        $name = strtolower($name);
        $name = preg_replace('/[^a-z0-9 ]+/', ' ', $name) ?? '';
        $name = preg_replace('/\s+/', ' ', $name) ?? '';

        return trim($name);
    }

    public static function matterKey(string $matterId): string
    {
        return strtoupper(trim($matterId));
    }

    public static function compactKey(string $matterId): string
    {
        return strtoupper(preg_replace('/[^A-Za-z0-9]+/', '', $matterId) ?? '');
    }

    /**
     * @param  array<string, int>  $owners
     */
    private static function ownedByOther(array $owners, string $value, int $adminId): bool
    {
        $owner = $owners[strtolower($value)] ?? $owners[$value] ?? null;

        return $owner !== null && (int) $owner !== $adminId;
    }

    /**
     * @param  list<CompanyContactRow>  $rows
     */
    private static function firstText(array $rows, callable $read): ?string
    {
        foreach ($rows as $row) {
            $value = $read($row);
            if (! self::blank($value)) {
                return trim((string) $value);
            }
        }

        return null;
    }

    /**
     * @param  list<CompanyContactRow>  $rows
     */
    private static function firstDigits(array $rows, callable $read, int $length): ?string
    {
        foreach ($rows as $row) {
            $digits = preg_replace('/\D+/', '', (string) $read($row)) ?? '';
            if (strlen($digits) === $length) {
                return $digits;
            }
        }

        return null;
    }

    private static function blank(mixed $value): bool
    {
        return trim((string) $value) === '';
    }

    private static function sameText(?string $left, ?string $right): bool
    {
        return BusinessName::groupKey($left) !== '' && BusinessName::groupKey($left) === BusinessName::groupKey($right);
    }
}
