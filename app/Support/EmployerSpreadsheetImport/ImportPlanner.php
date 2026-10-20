<?php

namespace App\Support\EmployerSpreadsheetImport;

/**
 * Decides what the import would create or reject. A CRM match is rejected and left unchanged.
 */
final class ImportPlanner
{
    /**
     * @param  list<SheetRow>  $rows
     * @param  list<array{id: int, name: string}>  $companies
     * @param  array<string, array{id: int, is_company: bool}>  $clientsByEmail
     * @param  array<string, int>  $clientsByIdentity  "last|first|Y-m-d" => admin id
     * @param  array<string, true>  $departmentReferences
     * @param  array<string, list<int>>  $openMatterIds  "adminId:matterTypeId" => matter ids with an empty department reference
     * @param  array<string, int>  $matterTypeIds  "title|0or1" => matters.id
     * @param  array<string, int>  $staffIds  normalised responsible name => staff id
     * @return array{
     *     companies_to_create: int,
     *     companies_rejected: int,
     *     clients_to_create: int,
     *     clients_rejected: int,
     *     matters_to_create: int,
     *     matters_rejected: int,
     *     matters_skipped: int,
     *     blocking: list<string>,
     *     warnings: list<string>,
     *     actions: list<array<string, mixed>>
     * }
     */
    public static function plan(
        array $rows,
        array $companies,
        array $clientsByEmail,
        array $clientsByIdentity,
        array $departmentReferences,
        array $openMatterIds,
        array $matterTypeIds,
        array $staffIds,
        ?int $fallbackStaffId,
        string $subclass186Stream,
    ): array {
        $blocking = [];
        $warnings = [];
        $actions = [];
        $notesSeen = [];

        $groups = [];
        foreach ($rows as $row) {
            if ($row->business === null || trim($row->business) === '') {
                $warnings[] = $row->matterId.' has no business name and was skipped.';

                continue;
            }
            if (str_starts_with(strtolower(trim((string) $row->matterType)), 'family sponsorship')) {
                $warnings[] = $row->matterId.' is a family sponsorship and was skipped.';

                continue;
            }
            $key = BusinessName::groupKey($row->business);
            $groups[$key]['rows'][] = $row;
            $groups[$key]['names'][$row->business] = ($groups[$key]['names'][$row->business] ?? 0) + 1;
            if ($row->trading) {
                $groups[$key]['trading'][$row->trading] = true;
            }
        }

        $companyActions = [];
        foreach ($groups as $key => $group) {
            $display = self::displayName($group['names']);
            $match = CompanyNameMatcher::match($display, $companies);
            if ($match['id'] === null && ! $match['ambiguous']) {
                $tradingMatch = self::tradingMatch($group['trading'] ?? [], $companies);
                if ($tradingMatch['id'] !== null || $tradingMatch['ambiguous']) {
                    $match = $tradingMatch;
                }
            }

            if ($match['id'] !== null || $match['ambiguous']) {
                $companyActions[$key] = [
                    'type' => 'reject_company',
                    'group_key' => $key,
                    'name' => $display,
                    'company_id' => $match['id'],
                    'company_type' => null,
                    'trading_names' => [],
                    'staff_id' => null,
                ];
                $warnings[] = $display.' is already in the CRM and was rejected.';

                continue;
            }

            $companyActions[$key] = [
                'type' => 'create_company',
                'group_key' => $key,
                'name' => $display,
                'company_id' => null,
                'company_type' => BusinessName::isTrustee($display) ? 'Trustee' : null,
                'trading_names' => array_keys($group['trading'] ?? []),
                'staff_id' => self::staffForRows($group['rows'], $staffIds, $fallbackStaffId),
            ];
            if ($companyActions[$key]['staff_id'] === null) {
                $blocking[] = 'A business has no staff member to assign. Pass --fallback-staff, or the staff id for each responsible name.';
            }
        }

        foreach ($groups as $key => $group) {
            if (! isset($companyActions[$key])) {
                continue;
            }
            foreach ($group['rows'] as $row) {
                if ($row->role !== 'Sponsor') {
                    continue;
                }
                $actions[] = self::matterAction(
                    $row,
                    $companyActions[$key],
                    null,
                    $matterTypeIds,
                    $departmentReferences,
                    $openMatterIds,
                    $staffIds,
                    $fallbackStaffId,
                    $subclass186Stream,
                    $warnings,
                    $blocking,
                    $notesSeen,
                );
            }
        }

        $clientActions = [];
        foreach ($rows as $row) {
            if ($row->role !== 'Migrant') {
                continue;
            }
            $identity = self::clientIdentity($row, $clientsByEmail, $clientsByIdentity, $warnings);
            if ($identity['blocked']) {
                $blocking[] = $identity['message'];

                continue;
            }
            $clientKey = $identity['key'];
            if (! isset($clientActions[$clientKey])) {
                $clientActions[$clientKey] = $identity['action'];
            }
            if ($clientActions[$clientKey]['type'] === 'create_client' && $clientActions[$clientKey]['staff_id'] === null) {
                $clientActions[$clientKey]['staff_id'] = self::staffId($row->responsible, $staffIds, $fallbackStaffId);
                if ($clientActions[$clientKey]['staff_id'] === null) {
                    $blocking[] = 'A person has no staff member to assign. Pass --fallback-staff, or the staff id for each responsible name.';
                }
            }
            $companyKey = BusinessName::groupKey($row->business);
            $companyAction = $companyActions[$companyKey] ?? null;
            $actions[] = self::matterAction(
                $row,
                $companyAction,
                $clientActions[$clientKey],
                $matterTypeIds,
                $departmentReferences,
                $openMatterIds,
                $staffIds,
                $fallbackStaffId,
                $subclass186Stream,
                $warnings,
                $blocking,
                $notesSeen,
            );
        }

        $actions = array_values(array_filter($actions));
        $companyList = array_values($companyActions);
        $clientList = array_values($clientActions);

        return [
            'companies_to_create' => count(array_filter($companyList, fn (array $action): bool => $action['type'] === 'create_company')),
            'companies_rejected' => count(array_filter($companyList, fn (array $action): bool => $action['type'] === 'reject_company')),
            'clients_to_create' => count(array_filter($clientList, fn (array $action): bool => $action['type'] === 'create_client')),
            'clients_rejected' => count(array_filter($clientList, fn (array $action): bool => $action['type'] === 'reject_client')),
            'matters_to_create' => count(array_filter($actions, fn (array $action): bool => $action['type'] === 'create_matter')),
            'matters_rejected' => count(array_filter($actions, fn (array $action): bool => in_array($action['reason'] ?? '', ['already imported', 'company already in CRM', 'matter already exists'], true))),
            'matters_skipped' => count(array_filter($actions, fn (array $action): bool => $action['type'] === 'skip_matter' && ! in_array($action['reason'] ?? '', ['already imported', 'company already in CRM', 'matter already exists'], true))),
            'blocking' => array_values(array_unique($blocking)),
            'warnings' => array_values(array_unique($warnings)),
            'actions' => array_merge($companyList, $clientList, $actions),
        ];
    }

    /**
     * @param  array<string, int>  $names
     */
    private static function displayName(array $names): string
    {
        foreach (array_keys($names) as $name) {
            if (str_contains(strtolower($name), 'trentham')) {
                return $name;
            }
        }

        arsort($names);

        return (string) array_key_first($names);
    }

    /**
     * @param  array<string, true>  $tradingNames
     * @param  list<array{id: int, name: string}>  $companies
     * @return array{id: ?int, ambiguous: bool}
     */
    private static function tradingMatch(array $tradingNames, array $companies): array
    {
        foreach (array_keys($tradingNames) as $trading) {
            $match = CompanyNameMatcher::match($trading, $companies);
            if ($match['id'] !== null || $match['ambiguous']) {
                return $match;
            }
        }

        return ['id' => null, 'ambiguous' => false];
    }

    /**
     * @param  list<SheetRow>  $rows
     * @param  array<string, int>  $staffIds
     */
    private static function staffForRows(array $rows, array $staffIds, ?int $fallbackStaffId): ?int
    {
        foreach ($rows as $row) {
            $staffId = self::staffId($row->responsible, $staffIds, $fallbackStaffId);
            if ($staffId !== null) {
                return $staffId;
            }
        }

        return $fallbackStaffId;
    }

    /**
     * @param  array<string, int>  $staffIds
     */
    private static function staffId(?string $responsible, array $staffIds, ?int $fallbackStaffId): ?int
    {
        $key = strtolower(trim(preg_replace('/\s+/', ' ', (string) $responsible) ?? ''));
        if ($key !== '' && isset($staffIds[$key])) {
            return $staffIds[$key];
        }

        return $fallbackStaffId;
    }

    /**
     * @param  array<string, array{id: int, is_company: bool}>  $clientsByEmail
     * @param  array<string, int>  $clientsByIdentity
     * @param  list<string>  $warnings
     * @return array{blocked: bool, message: string, key: string, action: array<string, mixed>}
     */
    private static function clientIdentity(SheetRow $row, array $clientsByEmail, array $clientsByIdentity, array &$warnings): array
    {
        [$first, $last] = PersonName::split($row->surname, $row->given);
        foreach ($row->emails as $email) {
            if (! isset($clientsByEmail[$email])) {
                continue;
            }
            $existing = $clientsByEmail[$email];
            if ($existing['is_company']) {
                return [
                    'blocked' => true,
                    'message' => $row->matterId.' email '.$email.' belongs to a company record.',
                    'key' => '',
                    'action' => [],
                ];
            }

            return [
                'blocked' => false,
                'message' => '',
                'key' => 'email:'.$email,
                'action' => [
                    'type' => 'reject_client',
                    'client_key' => 'email:'.$email,
                    'admin_id' => $existing['id'],
                    'first_name' => $first,
                    'last_name' => $last,
                    'email' => $email,
                    'staff_id' => null,
                ],
            ];
        }

        if ($row->dob !== null && $last !== '.') {
            $identityKey = strtolower($last).'|'.strtolower($first).'|'.$row->dob;
            if (isset($clientsByIdentity[$identityKey])) {
                return [
                    'blocked' => false,
                    'message' => '',
                    'key' => 'identity:'.$identityKey,
                    'action' => [
                        'type' => 'reject_client',
                        'client_key' => 'identity:'.$identityKey,
                        'admin_id' => $clientsByIdentity[$identityKey],
                        'first_name' => $first,
                        'last_name' => $last,
                        'email' => $row->emails[0] ?? null,
                        'staff_id' => null,
                    ],
                ];
            }
        }

        if (count($row->emails) > 1) {
            $warnings[] = $row->matterId.' has more than one email. The first address is used.';
        }

        $key = 'new:'.strtolower($first.'|'.$last.'|'.($row->emails[0] ?? $row->matterId));

        return [
            'blocked' => false,
            'message' => '',
            'key' => $key,
            'action' => [
                'type' => 'create_client',
                'client_key' => $key,
                'admin_id' => null,
                'first_name' => $first,
                'last_name' => $last,
                'email' => $row->emails[0] ?? null,
                'phone' => $row->phone,
                'dob' => $row->dob,
                'marital_status' => $row->maritalStatus,
                'passport_country' => $row->passportCountry,
                'passport_expiry' => $row->passportExpiry,
                'staff_id' => self::staffId($row->responsible, [], null),
            ],
        ];
    }

    /**
     * @param  array<string, mixed>|null  $companyAction
     * @param  array<string, mixed>|null  $clientAction
     * @param  array<string, int>  $matterTypeIds
     * @param  array<string, true>  $departmentReferences
     * @param  array<string, list<int>>  $openMatterIds
     * @param  array<string, int>  $staffIds
     * @param  list<string>  $warnings
     * @param  list<string>  $blocking
     * @param  array<string, true>  $notesSeen
     * @return array<string, mixed>|null
     */
    private static function matterAction(
        SheetRow $row,
        ?array $companyAction,
        ?array $clientAction,
        array $matterTypeIds,
        array $departmentReferences,
        array $openMatterIds,
        array $staffIds,
        ?int $fallbackStaffId,
        string $subclass186Stream,
        array &$warnings,
        array &$blocking,
        array &$notesSeen,
    ): ?array {
        if ($row->matterId === '') {
            $warnings[] = 'A row for '.($row->business ?? 'unknown').' has no matter number and was skipped.';

            return null;
        }

        if (($companyAction['type'] ?? null) === 'reject_company' && $row->role === 'Sponsor') {
            $warnings[] = $row->matterId.' was rejected because '.$companyAction['name'].' is already in the CRM.';

            return [
                'type' => 'skip_matter',
                'matter_id' => $row->matterId,
                'reason' => 'company already in CRM',
            ];
        }

        if (isset($departmentReferences[$row->matterId])) {
            return [
                'type' => 'skip_matter',
                'matter_id' => $row->matterId,
                'reason' => 'already imported',
            ];
        }

        $mapped = MatterTypeCatalog::match($row->matterType, $row->role, $subclass186Stream);
        if ($mapped === null) {
            $warnings[] = $row->matterId.' matter type "'.($row->matterType ?? '').'" has no CRM matter type and was skipped.';

            return [
                'type' => 'skip_matter',
                'matter_id' => $row->matterId,
                'reason' => 'unmapped',
            ];
        }

        if ($mapped['note'] !== null && ! isset($notesSeen[$mapped['note']])) {
            $notesSeen[$mapped['note']] = true;
            $warnings[] = $mapped['note'];
        }

        $typeKey = $mapped['title'].'|'.($mapped['for_company'] ? '1' : '0');
        if (! isset($matterTypeIds[$typeKey])) {
            $blocking[] = 'CRM has no matter titled "'.$mapped['title'].'".';

            return [
                'type' => 'skip_matter',
                'matter_id' => $row->matterId,
                'reason' => 'missing matter type',
            ];
        }

        $staffId = $clientAction['staff_id'] ?? self::staffId($row->responsible, $staffIds, $fallbackStaffId);
        if ($clientAction !== null && $clientAction['type'] === 'create_client') {
            $clientAction['staff_id'] = $staffId;
        }

        $ownerAdminId = $row->role === 'Migrant' ? ($clientAction['admin_id'] ?? null) : null;
        $matterTypeId = $matterTypeIds[$typeKey];
        $openKey = ($ownerAdminId ?? '').':'.$matterTypeId;
        if ($ownerAdminId !== null && isset($openMatterIds[$openKey]) && $openMatterIds[$openKey] !== []) {
            $warnings[] = $row->matterId.' was rejected because this person already has that matter.';

            return [
                'type' => 'skip_matter',
                'matter_id' => $row->matterId,
                'reason' => 'matter already exists',
            ];
        }

        return [
            'type' => 'create_matter',
            'matter_id' => $row->matterId,
            'group_key' => ($companyAction['type'] ?? null) === 'reject_company' ? null : ($companyAction['group_key'] ?? null),
            'client_key' => $clientAction['client_key'] ?? null,
            'for_company' => $row->role === 'Sponsor',
            'sel_matter_id' => $matterTypeId,
            'kind' => $mapped['kind'],
            'matter_title' => $mapped['title'],
            'matter_status' => MatterTypeCatalog::isActiveFileStatus($row->fileStatus) ? 1 : 0,
            'file_status' => $row->fileStatus,
            'engagement_on' => $row->engagementOn,
            'staff_id' => $staffId,
            'position' => $row->position,
            'anzsco' => $row->anzsco,
            'person_name' => trim((($clientAction['first_name'] ?? $row->given) ?? '').' '.(($clientAction['last_name'] ?? $row->surname) ?? '')),
            'passport_country' => $row->passportCountry,
            'passport_expiry' => $row->passportExpiry,
        ];
    }
}
