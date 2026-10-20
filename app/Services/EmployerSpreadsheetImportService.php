<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\ClientContact;
use App\Models\ClientEmail;
use App\Models\ClientMatter;
use App\Models\ClientPassportInformation;
use App\Models\Company;
use App\Models\CompanyNomination;
use App\Models\CompanySponsorship;
use App\Models\CompanyTradingName;
use App\Models\Matter;
use App\Models\Staff;
use App\Support\EmployerSpreadsheetImport\ImportPlanner;
use App\Support\EmployerSpreadsheetImport\MatterTypeCatalog;
use App\Support\EmployerSpreadsheetImport\SheetRow;
use App\Support\EmployerSpreadsheetImport\WorkbookReader;
use App\Support\WorkflowAssignment;
use App\Support\WorkflowStageChecklistSync;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmployerSpreadsheetImportService
{
    public function __construct(
        private WorkbookReader $reader,
        private ClientReferenceService $references,
    ) {}

    /**
     * @param  array<string, int>  $staffIds
     * @return array<string, mixed>
     */
    public function plan(string $directory, array $staffIds, ?int $fallbackStaffId, string $subclass186Stream): array
    {
        $rows = $this->reader->readDirectory($directory);
        $matterTypeIds = $this->matterTypeIds($rows, $subclass186Stream);

        return ImportPlanner::plan(
            $rows,
            $this->companyRecords(),
            $this->clientsByEmail(),
            $this->clientsByIdentity(),
            $this->departmentReferences(),
            $this->openMatterIds(array_values($matterTypeIds)),
            $matterTypeIds,
            $staffIds,
            $fallbackStaffId,
            $subclass186Stream,
        );
    }

    /**
     * @param  array<string, mixed>  $plan
     */
    public function commit(array $plan): void
    {
        DB::transaction(function () use ($plan): void {
            $companyIds = [];
            $companyAdminIds = [];
            $clientIds = [];

            foreach ($plan['actions'] as $action) {
                if ($action['type'] === 'create_company') {
                    $created = $this->createCompany($action);
                    $companyIds[$action['group_key']] = $created['company_id'];
                    $companyAdminIds[$action['group_key']] = $created['admin_id'];
                }
                if ($action['type'] === 'reject_client') {
                    $clientIds[$action['client_key']] = $action['admin_id'];
                }
                if ($action['type'] === 'create_client') {
                    $clientIds[$action['client_key']] = $this->createClient($action);
                }
            }

            foreach ($plan['actions'] as $action) {
                if ($action['type'] !== 'create_matter') {
                    continue;
                }

                $adminId = $action['for_company']
                    ? ($companyAdminIds[$action['group_key']] ?? null)
                    : ($clientIds[$action['client_key']] ?? null);
                if ($adminId === null || $action['staff_id'] === null) {
                    throw new \RuntimeException('Cannot create matter '.$action['matter_id'].' without a client and a staff member.');
                }

                $matter = $this->createMatter($action, (int) $adminId);
                if ($action['for_company'] && $action['kind'] === 'sponsorship') {
                    $this->createSponsorship($companyIds[$action['group_key']] ?? null, $action);
                }
                if (! $action['for_company']) {
                    $this->createNomination($companyIds[$action['group_key']] ?? null, (int) $adminId, $matter->id, $action);
                }
            }
        });
    }

    /**
     * @param  list<SheetRow>  $rows
     * @return array<string, int>
     */
    private function matterTypeIds(array $rows, string $subclass186Stream): array
    {
        $wanted = [];
        foreach ($rows as $row) {
            $mapped = MatterTypeCatalog::match($row->matterType, $row->role, $subclass186Stream);
            if ($mapped === null) {
                continue;
            }
            $wanted[$mapped['title'].'|'.($mapped['for_company'] ? '1' : '0')] = $mapped;
        }

        $ids = [];
        foreach ($wanted as $key => $mapped) {
            $id = Matter::query()
                ->where('title', $mapped['title'])
                ->where('is_for_company', $mapped['for_company'])
                ->value('id');
            if ($id) {
                $ids[$key] = (int) $id;
            }
        }

        return $ids;
    }

    /**
     * @return list<array{id: int, name: string}>
     */
    private function companyRecords(): array
    {
        $records = [];
        foreach (Company::query()->get(['id', 'company_name', 'trading_name']) as $company) {
            foreach ([$company->company_name, $company->trading_name] as $name) {
                if ($name) {
                    $records[] = ['id' => (int) $company->id, 'name' => $name];
                }
            }
        }
        foreach (CompanyTradingName::query()->get(['company_id', 'trading_name']) as $trading) {
            if ($trading->trading_name) {
                $records[] = ['id' => (int) $trading->company_id, 'name' => $trading->trading_name];
            }
        }

        return $records;
    }

    /**
     * @return array<string, array{id: int, is_company: bool}>
     */
    private function clientsByEmail(): array
    {
        $clients = [];
        Admin::query()
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->get(['id', 'email', 'is_company'])
            ->each(function (Admin $admin) use (&$clients): void {
                $email = strtolower(trim((string) $admin->email));
                if ($email !== '' && ! isset($clients[$email])) {
                    $clients[$email] = ['id' => (int) $admin->id, 'is_company' => (bool) $admin->is_company];
                }
            });

        DB::table('client_emails')
            ->join('admins', 'admins.id', '=', 'client_emails.client_id')
            ->whereNotNull('client_emails.email')
            ->get(['admins.id', 'admins.is_company', 'client_emails.email'])
            ->each(function (object $email) use (&$clients): void {
                $address = strtolower(trim((string) $email->email));
                if ($address === '' || isset($clients[$address])) {
                    return;
                }
                $clients[$address] = ['id' => (int) $email->id, 'is_company' => (bool) $email->is_company];
            });

        return $clients;
    }

    /**
     * @return array<string, int>
     */
    private function clientsByIdentity(): array
    {
        $clients = [];
        Admin::query()
            ->where(function ($query): void {
                $query->whereNull('is_deleted')->orWhere('is_deleted', 0);
            })
            ->where(function ($query): void {
                $query->where('is_company', false)->orWhereNull('is_company');
            })
            ->whereNotNull('dob')
            ->get(['id', 'first_name', 'last_name', 'dob'])
            ->each(function (Admin $admin) use (&$clients): void {
                $dob = $admin->dob ? Carbon::parse($admin->dob)->format('Y-m-d') : null;
                if ($dob === null) {
                    return;
                }
                $key = strtolower(trim((string) $admin->last_name)).'|'.strtolower(trim((string) $admin->first_name)).'|'.$dob;
                $clients[$key] ??= (int) $admin->id;
            });

        return $clients;
    }

    /**
     * @return array<string, true>
     */
    private function departmentReferences(): array
    {
        $references = [];
        foreach (ClientMatter::query()->whereNotNull('department_reference')->pluck('department_reference') as $reference) {
            $reference = trim((string) $reference);
            if ($reference !== '') {
                $references[$reference] = true;
            }
        }

        return $references;
    }

    /**
     * @param  list<int>  $matterTypeIds
     * @return array<string, list<int>>
     */
    private function openMatterIds(array $matterTypeIds): array
    {
        if ($matterTypeIds === []) {
            return [];
        }

        $grouped = [];
        ClientMatter::query()
            ->whereIn('sel_matter_id', $matterTypeIds)
            ->get(['id', 'client_id', 'sel_matter_id', 'department_reference'])
            ->each(function (ClientMatter $matter) use (&$grouped): void {
                if (trim((string) $matter->department_reference) !== '') {
                    return;
                }
                $key = $matter->client_id.':'.$matter->sel_matter_id;
                $grouped[$key][] = (int) $matter->id;
            });

        return $grouped;
    }

    /**
     * @param  array<string, mixed>  $action
     * @return array{admin_id: int, company_id: int}
     */
    private function createCompany(array $action): array
    {
        $reference = $this->references->generateClientReference($action['name']);
        $slug = Str::slug(Str::limit($action['name'], 40, ''));
        if ($slug === '') {
            $slug = 'company';
        }
        $email = 'company_import_'.$slug.'_'.strtolower(Str::random(6)).'@lead.internal';
        $staffId = (int) $action['staff_id'];
        $now = now();

        $adminId = DB::table('admins')->insertGetId([
            'user_id' => $staffId,
            'password' => Hash::make(Str::password(40)),
            'client_counter' => $reference['client_counter'],
            'client_id' => $reference['client_id'],
            'status' => 1,
            'type' => 'client',
            'is_archived' => 0,
            'is_deleted' => 0,
            'verified' => 0,
            'cp_status' => 0,
            'cp_code_verify' => 0,
            'australian_study' => 0,
            'specialist_education' => 0,
            'regional_study' => 0,
            'is_company' => 1,
            'first_name' => Str::limit($action['name'], 255, ''),
            'last_name' => null,
            'email' => $email,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $tradingNames = array_values(array_filter($action['trading_names']));
        $company = Company::query()->create([
            'admin_id' => $adminId,
            'company_name' => $action['name'],
            'trading_name' => $tradingNames[0] ?? null,
            'has_trading_name' => $tradingNames !== [],
            'company_type' => $action['company_type'],
        ]);
        foreach ($tradingNames as $index => $tradingName) {
            $company->tradingNames()->create([
                'trading_name' => $tradingName,
                'is_primary' => $index === 0,
                'sort_order' => $index,
            ]);
        }

        return ['admin_id' => (int) $adminId, 'company_id' => (int) $company->id];
    }

    /**
     * @param  array<string, mixed>  $action
     */
    private function createClient(array $action): int
    {
        $reference = $this->references->generateClientReference($action['first_name']);
        $staffId = (int) $action['staff_id'];
        $email = $action['email'];
        if ($email && Admin::emailIsTaken($email)) {
            $email = null;
        }
        $phone = $action['phone'];
        if ($phone && Admin::phoneIsTaken($phone)) {
            $phone = null;
        }
        $now = now();
        $marital = in_array($action['marital_status'], ['Never Married', 'Engaged', 'Married', 'De Facto', 'Separated', 'Divorced', 'Widowed'], true)
            ? $action['marital_status']
            : null;

        $adminId = DB::table('admins')->insertGetId([
            'user_id' => $staffId,
            'password' => Hash::make(Str::password(40)),
            'client_counter' => $reference['client_counter'],
            'client_id' => $reference['client_id'],
            'status' => 1,
            'type' => 'client',
            'is_archived' => 0,
            'is_deleted' => 0,
            'verified' => 0,
            'cp_status' => 0,
            'cp_code_verify' => 0,
            'australian_study' => 0,
            'specialist_education' => 0,
            'regional_study' => 0,
            'is_company' => 0,
            'first_name' => $action['first_name'],
            'last_name' => $action['last_name'],
            'dob' => $action['dob'],
            'marital_status' => $marital,
            'email' => $email,
            'phone' => $phone,
            'contact_type' => $phone ? 'Mobile' : null,
            'email_type' => $email ? 'Personal' : null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        if ($phone) {
            ClientContact::query()->create([
                'admin_id' => $staffId,
                'client_id' => $adminId,
                'contact_type' => 'Mobile',
                'phone' => $phone,
                'country_code' => '61',
                'is_verified' => false,
            ]);
        }
        if ($email) {
            ClientEmail::query()->create([
                'admin_id' => $staffId,
                'client_id' => $adminId,
                'email_type' => 'Personal',
                'email' => $email,
                'is_verified' => false,
            ]);
        }
        if ($action['passport_country'] || $action['passport_expiry']) {
            ClientPassportInformation::query()->create([
                'admin_id' => $staffId,
                'client_id' => $adminId,
                'passport_country' => $action['passport_country'],
                'passport_expiry_date' => $action['passport_expiry'],
            ]);
        }

        return (int) $adminId;
    }

    /**
     * @param  array<string, mixed>  $action
     */
    private function createMatter(array $action, int $adminId): ClientMatter
    {
        $staff = Staff::query()->findOrFail($action['staff_id']);
        $matterType = Matter::query()->findOrFail($action['sel_matter_id']);
        $count = ClientMatter::query()
            ->where('sel_matter_id', $matterType->id)
            ->where('client_id', $adminId)
            ->count();
        $prefix = $matterType->nick_name ?: 'Matter';
        $workflowId = WorkflowAssignment::resolveWorkflowIdForNewClientMatter($matterType);
        $createdAt = $action['engagement_on'] ? Carbon::parse($action['engagement_on'])->startOfDay() : now();

        $matter = new ClientMatter;
        $matter->user_id = $staff->id;
        $matter->client_id = $adminId;
        $matter->office_id = $staff->office_id;
        $matter->sel_migration_agent = $staff->is_migration_agent ? $staff->id : null;
        $matter->sel_person_responsible = $staff->id;
        $matter->sel_matter_id = $matterType->id;
        $matter->client_unique_matter_no = $prefix.'_'.($count + 1);
        $matter->workflow_id = $workflowId;
        $matter->workflow_stage_id = WorkflowAssignment::firstStageIdForWorkflow($workflowId);
        $matter->matter_status = $action['matter_status'];
        $matter->department_reference = $action['matter_id'];
        $matter->decision_note = 'Imported from employer spreadsheet. File status: '.($action['file_status'] ?? 'unknown').'. Old matter '.$action['matter_id'].'.';
        $matter->created_at = $createdAt;
        $matter->updated_at = now();
        $matter->save();

        WorkflowStageChecklistSync::ensureSeededForMatter($matter);

        return $matter;
    }

    /**
     * @param  array<string, mixed>  $action
     */
    private function createSponsorship(?int $companyId, array $action): void
    {
        if ($companyId === null) {
            return;
        }
        $marker = 'Old matter '.$action['matter_id'];
        $exists = CompanySponsorship::query()
            ->where('company_id', $companyId)
            ->where('previous_sponsorship_notes', $marker)
            ->exists();
        if ($exists) {
            return;
        }

        CompanySponsorship::query()->create([
            'company_id' => $companyId,
            'sponsorship_type' => Str::limit($action['matter_title'], 50, ''),
            'sponsorship_status' => Str::limit((string) ($action['file_status'] ?? ''), 50, ''),
            'previous_sponsorship_notes' => $marker,
            'sort_order' => (int) CompanySponsorship::query()->where('company_id', $companyId)->max('sort_order') + 1,
        ]);
    }

    /**
     * @param  array<string, mixed>  $action
     */
    private function createNomination(?int $companyId, int $adminId, int $clientMatterId, array $action): void
    {
        if ($companyId === null) {
            return;
        }
        $exists = CompanyNomination::query()->where('client_matter_id', $clientMatterId)->exists();
        if ($exists) {
            return;
        }

        CompanyNomination::query()->create([
            'company_id' => $companyId,
            'position_title' => $action['position'] ? Str::limit($action['position'], 255, '') : null,
            'anzsco_code' => $action['anzsco'] ? Str::limit(preg_replace('/\D+/', '', (string) $action['anzsco']) ?? '', 10, '') : null,
            'nominated_client_id' => $adminId,
            'nominated_person_name' => $action['person_name'] ? Str::limit($action['person_name'], 255, '') : null,
            'client_matter_id' => $clientMatterId,
            'status' => Str::limit((string) ($action['file_status'] ?? ''), 50, ''),
            'sort_order' => (int) CompanyNomination::query()->where('company_id', $companyId)->max('sort_order') + 1,
        ]);
    }
}
