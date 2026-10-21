<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\ClientAddress;
use App\Models\ClientContact;
use App\Models\ClientEmail;
use App\Models\ClientMatter;
use App\Models\Company;
use App\Models\CompanyDirector;
use App\Models\CompanyNomination;
use App\Support\EmployerSpreadsheetImport\CompanyContactBackfillPlanner;
use App\Support\EmployerSpreadsheetImport\CompanyContactRow;
use App\Support\EmployerSpreadsheetImport\CompanyContactWorkbook;
use Illuminate\Support\Facades\DB;

class CompanyContactBackfillService
{
    public function __construct(private CompanyContactWorkbook $workbook) {}

    /**
     * @return array<string, mixed>
     */
    public function plan(string $path): array
    {
        $rows = $this->workbook->read($path);

        return CompanyContactBackfillPlanner::plan($rows, $this->catalog($rows));
    }

    /**
     * @param  array<string, mixed>  $plan
     */
    public function commit(array $plan): void
    {
        DB::transaction(function () use ($plan): void {
            foreach ($plan['actions'] as $action) {
                if ($action['type'] !== 'fill_company') {
                    continue;
                }
                $this->fillCompany($action);
            }
        });
    }

    /**
     * @param  list<CompanyContactRow>  $rows
     * @return array<string, mixed>
     */
    private function catalog(array $rows): array
    {
        $companies = Company::query()
            ->with([
                'tradingNames:id,company_id,trading_name',
                'directors:id,company_id,director_name,director_dob,director_role,director_client_id',
                'directors.directorClient:id,first_name,last_name,is_company',
                'admin:id,phone,email,user_id,is_company,client_id',
            ])
            ->get();

        $adminIds = $companies->pluck('admin_id')->filter()->map(fn ($id): int => (int) $id)->all();
        $addressIds = ClientAddress::query()->whereIn('client_id', $adminIds)->distinct()->pluck('client_id')->flip();
        $contactPhones = ClientContact::query()->whereIn('client_id', $adminIds)->whereNotNull('phone')->pluck('phone', 'client_id');
        $contactEmails = ClientEmail::query()->whereIn('client_id', $adminIds)->whereNotNull('email')->pluck('email', 'client_id');

        $snapshots = [];
        foreach ($companies as $company) {
            if ($company->admin === null || ! $company->admin->is_company) {
                continue;
            }
            $names = array_values(array_filter([
                $company->company_name,
                $company->trading_name,
                ...$company->tradingNames->pluck('trading_name')->all(),
            ]));
            $directors = [];
            foreach ($company->directors as $director) {
                $linked = $director->directorClient;
                $name = trim((string) ($director->director_name ?: trim(($linked->first_name ?? '').' '.($linked->last_name ?? ''))));
                if ($name === '') {
                    continue;
                }
                $directors[] = [
                    'name' => $name,
                    'has_dob' => $director->director_dob !== null,
                    'has_role' => trim((string) $director->director_role) !== '',
                    'client_id' => $director->director_client_id ? (int) $director->director_client_id : null,
                ];
            }

            $email = $company->admin->email;
            $extraEmail = $contactEmails[$company->admin_id] ?? null;
            if (Admin::isInternalPlaceholderEmail($email) && ! Admin::isInternalPlaceholderEmail($extraEmail)) {
                $email = $extraEmail;
            }
            if (Admin::isInternalPlaceholderEmail($email)) {
                $email = null;
            }

            $snapshots[(int) $company->id] = [
                'id' => (int) $company->id,
                'admin_id' => (int) $company->admin_id,
                'legal_name' => $company->company_name,
                'names' => $names,
                'trading_name' => $company->trading_name ?: $company->tradingNames->first()?->trading_name,
                'abn' => $company->ABN_number,
                'acn' => $company->ACN,
                'company_type' => $company->company_type,
                'industry' => $company->main_business_activity,
                'operating_since' => $company->business_operating_since?->format('Y-m-d'),
                'phone' => $company->admin->phone ?: ($contactPhones[$company->admin_id] ?? null),
                'email' => $email,
                'has_address' => isset($addressIds[$company->admin_id]),
                'directors' => $directors,
                'contact_person_id' => $company->contact_person_id,
                'contact_person_position' => $company->contact_person_position,
            ];
        }

        return [
            'matters' => $this->matterIndex($rows, $snapshots),
            'companies' => $snapshots,
            'email_owners' => $this->emailOwners($rows),
            'phone_owners' => $this->phoneOwners($rows),
            'people_by_email' => $this->peopleByEmail($rows),
            'people_by_phone' => $this->peopleByPhone($rows),
        ];
    }

    /**
     * @param  list<CompanyContactRow>  $rows
     * @param  array<int, array<string, mixed>>  $companies
     * @return array<string, int>
     */
    private function matterIndex(array $rows, array $companies): array
    {
        $matterIds = [];
        $compactIds = [];
        foreach ($rows as $row) {
            $exact = CompanyContactBackfillPlanner::matterKey($row->matterId);
            $compact = CompanyContactBackfillPlanner::compactKey($row->matterId);
            if ($exact !== '') {
                $matterIds[$exact] = $exact;
            }
            if ($compact !== '') {
                $compactIds[$compact] = $compact;
            }
        }

        $matters = ClientMatter::query()
            ->where(function ($query) use ($matterIds): void {
                $ids = array_values($matterIds);
                $query->whereIn('client_unique_matter_no', $ids)
                    ->orWhereIn('department_reference', $ids)
                    ->orWhereIn('other_reference', $ids);
            })
            ->get(['id', 'client_id', 'client_unique_matter_no', 'department_reference', 'other_reference']);

        $admins = Admin::query()
            ->whereIn('id', $matters->pluck('client_id')->filter()->unique()->all())
            ->get(['id', 'is_company'])
            ->keyBy('id');
        $companyByAdmin = [];
        foreach ($companies as $company) {
            $companyByAdmin[$company['admin_id']] = $company['id'];
        }
        $nominationCompany = CompanyNomination::query()
            ->whereIn('client_matter_id', $matters->pluck('id')->all())
            ->pluck('company_id', 'client_matter_id');

        $index = [];
        foreach ($matters as $matter) {
            $admin = $admins->get($matter->client_id);
            $companyId = ($admin && $admin->is_company) ? ($companyByAdmin[(int) $admin->id] ?? null) : null;
            if ($companyId === null) {
                $companyId = isset($nominationCompany[$matter->id]) ? (int) $nominationCompany[$matter->id] : null;
            }
            if ($companyId === null || ! isset($companies[$companyId])) {
                continue;
            }
            foreach ([$matter->client_unique_matter_no, $matter->department_reference, $matter->other_reference] as $key) {
                $this->putMatterKey($index, (string) $key, $companyId);
                $this->putMatterKey($index, CompanyContactBackfillPlanner::compactKey((string) $key), $companyId);
            }
        }

        $clientAdmins = Admin::query()
            ->where('is_company', true)
            ->whereIn('client_id', array_values(array_unique([...array_values($matterIds), ...array_values($compactIds)])))
            ->get(['id', 'client_id']);
        foreach ($clientAdmins as $admin) {
            $companyId = $companyByAdmin[(int) $admin->id] ?? null;
            if ($companyId === null) {
                continue;
            }
            $this->putMatterKey($index, (string) $admin->client_id, (int) $companyId);
            $this->putMatterKey($index, CompanyContactBackfillPlanner::compactKey((string) $admin->client_id), (int) $companyId);
        }

        return $index;
    }

    /**
     * @param  array<string, int>  $index
     */
    private function putMatterKey(array &$index, string $key, int $companyId): void
    {
        $key = CompanyContactBackfillPlanner::matterKey($key);
        if ($key === '') {
            return;
        }
        if (isset($index[$key]) && $index[$key] !== $companyId) {
            $index[$key] = 0;

            return;
        }
        if (! isset($index[$key])) {
            $index[$key] = $companyId;
        }
    }

    /**
     * @param  list<CompanyContactRow>  $rows
     * @return array<string, int>
     */
    private function emailOwners(array $rows): array
    {
        $emails = [];
        foreach ($rows as $row) {
            if ($row->businessEmail !== null) {
                $emails[] = $row->businessEmail;
            }
        }
        $emails = array_values(array_unique($emails));
        if ($emails === []) {
            return [];
        }

        $owners = [];
        Admin::query()
            ->whereIn('type', ['client', 'lead'])
            ->whereRaw('LOWER(TRIM(COALESCE(email, \'\'))) in ('.implode(',', array_fill(0, count($emails), '?')).')', $emails)
            ->get(['id', 'email'])
            ->each(function (Admin $admin) use (&$owners): void {
                $owners[strtolower(trim((string) $admin->email))] = (int) $admin->id;
            });
        ClientEmail::query()
            ->whereRaw('LOWER(TRIM(COALESCE(email, \'\'))) in ('.implode(',', array_fill(0, count($emails), '?')).')', $emails)
            ->get(['client_id', 'email'])
            ->each(function (ClientEmail $email) use (&$owners): void {
                $key = strtolower(trim((string) $email->email));
                $owners[$key] ??= (int) $email->client_id;
            });

        return $owners;
    }

    /**
     * @param  list<CompanyContactRow>  $rows
     * @return array<string, int>
     */
    private function phoneOwners(array $rows): array
    {
        $owners = [];
        $phones = [];
        foreach ($rows as $row) {
            foreach ([$row->businessMobile, $row->businessPhone, $row->matterPhone] as $phone) {
                if ($phone !== null) {
                    $phones[$phone] = $phone;
                }
            }
        }
        foreach ($phones as $phone) {
            $owner = Admin::findClientOrLeadByNormalizedPhone($phone);
            if ($owner) {
                $owners[$phone] = (int) $owner->id;
            }
        }

        return $owners;
    }

    /**
     * @param  list<CompanyContactRow>  $rows
     * @return array<string, int>
     */
    private function peopleByEmail(array $rows): array
    {
        $emails = [];
        foreach ($rows as $row) {
            foreach ($row->contacts as $contact) {
                if (! empty($contact['email'])) {
                    $emails[] = $contact['email'];
                }
            }
        }
        $emails = array_values(array_unique($emails));
        if ($emails === []) {
            return [];
        }

        $people = [];
        foreach ($emails as $email) {
            $person = Admin::findClientOrLeadByNormalizedEmail($email);
            if ($person && ! $person->is_company) {
                $people[$email] = (int) $person->id;
            }
        }

        return $people;
    }

    /**
     * @param  list<CompanyContactRow>  $rows
     * @return array<string, int>
     */
    private function peopleByPhone(array $rows): array
    {
        $people = [];
        $phones = [];
        foreach ($rows as $row) {
            foreach ($row->contacts as $contact) {
                if (! empty($contact['phone'])) {
                    $phones[$contact['phone']] = $contact['phone'];
                }
            }
        }
        foreach ($phones as $phone) {
            $person = Admin::findClientOrLeadByNormalizedPhone($phone);
            if ($person && ! $person->is_company) {
                $people[$phone] = (int) $person->id;
            }
        }

        return $people;
    }

    /**
     * @param  array<string, mixed>  $action
     */
    private function fillCompany(array $action): void
    {
        $company = Company::query()->find($action['company_id']);
        $admin = Admin::query()->find($action['admin_id']);
        if ($company === null || $admin === null || ! $admin->is_company) {
            return;
        }

        $changes = $action['changes'];
        foreach (['ABN_number', 'ACN', 'company_type', 'main_business_activity', 'business_operating_since'] as $column) {
            if (isset($changes[$column]) && blank($company->{$column})) {
                $company->{$column} = $changes[$column];
            }
        }
        if (isset($changes['trading_name']) && blank($company->trading_name) && $company->tradingNames()->doesntExist()) {
            $company->trading_name = $changes['trading_name'];
            $company->has_trading_name = true;
            $company->tradingNames()->create([
                'trading_name' => $changes['trading_name'],
                'is_primary' => true,
                'sort_order' => 0,
            ]);
        }
        if (isset($changes['contact_person_id']) && blank($company->contact_person_id)) {
            $person = Admin::query()->whereKey($changes['contact_person_id'])->where('is_company', false)->first();
            if ($person) {
                $company->contact_person_id = $person->id;
            }
        }
        if (isset($changes['contact_person_position']) && blank($company->contact_person_position)) {
            $company->contact_person_position = $changes['contact_person_position'];
        }
        $company->save();

        if (isset($changes['phone']) && blank($admin->phone) && ! Admin::phoneIsTaken($changes['phone'], $admin->id)) {
            $admin->phone = $changes['phone'];
            $admin->contact_type = $admin->contact_type ?: 'Mobile';
            $admin->country_code = $admin->country_code ?: '61';
            ClientContact::query()->firstOrCreate(
                ['client_id' => $admin->id, 'phone' => $changes['phone']],
                [
                    'admin_id' => $admin->user_id ?: $admin->id,
                    'contact_type' => 'Mobile',
                    'country_code' => '61',
                    'is_verified' => false,
                ],
            );
        }
        if (isset($changes['email']) && Admin::isInternalPlaceholderEmail($admin->email) && ! Admin::emailIsTaken($changes['email'], $admin->id)) {
            $admin->email = $changes['email'];
            $admin->email_type = $admin->email_type ?: 'Business';
            ClientEmail::query()->firstOrCreate(
                ['client_id' => $admin->id, 'email' => $changes['email']],
                [
                    'admin_id' => $admin->user_id ?: $admin->id,
                    'email_type' => 'Business',
                    'is_verified' => false,
                ],
            );
        }
        $admin->save();

        if (isset($changes['address']) && ! ClientAddress::query()->where('client_id', $admin->id)->exists()) {
            $address = $changes['address'];
            $line = implode(', ', array_filter([
                $address['address_line_1'] ?? null,
                $address['address_line_2'] ?? null,
                $address['suburb'] ?? null,
                $address['state'] ?? null,
                $address['zip'] ?? null,
                $address['country'] ?? null,
            ]));
            ClientAddress::query()->create([
                'admin_id' => $admin->user_id ?: $admin->id,
                'client_id' => $admin->id,
                'address' => $line,
                'address_line_1' => $address['address_line_1'] ?? null,
                'address_line_2' => $address['address_line_2'] ?? null,
                'suburb' => $address['suburb'] ?? null,
                'state' => $address['state'] ?? null,
                'zip' => $address['zip'] ?? null,
                'country' => $address['country'] ?? null,
                'is_current' => 1,
            ]);
            ClientAddress::syncIsCurrentForClient((int) $admin->id);
        }

        $this->fillDirectors($company, $changes['directors'] ?? [], $changes['director_patches'] ?? []);
    }

    /**
     * @param  list<array<string, mixed>>  $create
     * @param  list<array<string, mixed>>  $patches
     */
    private function fillDirectors(Company $company, array $create, array $patches): void
    {
        $existing = $company->directors()->with('directorClient:id,first_name,last_name')->get();
        $byName = [];
        foreach ($existing as $director) {
            $linked = $director->directorClient;
            $name = trim((string) ($director->director_name ?: trim(($linked->first_name ?? '').' '.($linked->last_name ?? ''))));
            $byName[CompanyContactBackfillPlanner::personKey($name)] = $director;
        }

        foreach ($patches as $patch) {
            $director = $byName[CompanyContactBackfillPlanner::personKey((string) $patch['name'])] ?? null;
            if (! $director instanceof CompanyDirector) {
                continue;
            }
            if ($director->director_dob === null && ! empty($patch['dob'])) {
                $director->director_dob = $patch['dob'];
            }
            if (blank($director->director_role) && ! empty($patch['role'])) {
                $director->director_role = $patch['role'];
            }
            if ($director->director_client_id === null && ! empty($patch['client_id'])) {
                $person = Admin::query()->whereKey($patch['client_id'])->where('is_company', false)->first();
                if ($person) {
                    $director->director_client_id = $person->id;
                    $director->director_name = null;
                }
            }
            $director->save();
        }

        $sort = (int) $existing->max('sort_order') + 1;
        $hasPrimary = $existing->contains(fn (CompanyDirector $director): bool => (bool) $director->is_primary);
        foreach ($create as $index => $director) {
            $key = CompanyContactBackfillPlanner::personKey((string) $director['name']);
            if (isset($byName[$key])) {
                continue;
            }
            $person = null;
            if (! empty($director['client_id'])) {
                $person = Admin::query()->whereKey($director['client_id'])->where('is_company', false)->first();
            }
            $created = $company->directors()->create([
                'director_client_id' => $person?->id,
                'director_name' => $person ? null : $director['name'],
                'director_dob' => $director['dob'] ?? null,
                'director_role' => $director['role'] ?? null,
                'is_primary' => ! $hasPrimary && $index === 0,
                'sort_order' => $sort++,
            ]);
            $byName[$key] = $created;
            if ($created->is_primary) {
                $hasPrimary = true;
            }
        }
    }
}
