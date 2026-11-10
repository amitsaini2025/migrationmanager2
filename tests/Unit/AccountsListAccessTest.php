<?php

namespace Tests\Unit;

use App\Models\ClientAccessGrant;
use App\Models\Staff;
use App\Services\CrmAccess\CrmAccessDeniedException;
use App\Services\CrmAccess\CrmAccessService;
use App\Support\AccountsListAccess;
use App\Support\StaffClientVisibility;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AccountsListAccessTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->ensureSchema();
        config([
            'crm_access.accountant_role_id' => 15,
            'crm_access.quick_grant_minutes' => 15,
            'crm_access.strict_allocation' => true,
            'crm_access.accounts_open_reason.code' => 'accounts_validation',
            'crm.super_admin_only_client_file_ids' => [],
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_accountant_role_is_recognised(): void
    {
        $this->assertTrue(AccountsListAccess::isAccountant(new Staff(['role' => 15])));
        $this->assertFalse(AccountsListAccess::isAccountant(new Staff(['role' => 13])));
        $this->assertFalse(AccountsListAccess::isAccountant(null));
    }

    public function test_accountant_may_use_a_normal_client_and_not_a_locked_file(): void
    {
        $this->insertClient(9, 'ABC123');
        $this->insertClient(10, 'LOCK1');
        config(['crm.super_admin_only_client_file_ids' => ['LOCK1']]);
        $accountant = $this->accountant();

        $this->assertTrue(AccountsListAccess::accountantMayUseClient(9, $accountant));
        $this->assertFalse(AccountsListAccess::accountantMayUseClient(10, $accountant));
        $this->assertFalse(AccountsListAccess::accountantMayUseClient(9, new Staff(['role' => 13, 'id' => 8])));
    }

    public function test_opening_from_the_list_starts_fifteen_minutes_and_does_not_extend_it(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-10 07:00:00', 'UTC'));
        $this->insertOffice();
        $this->insertClient(9, 'ABC123');
        $service = new CrmAccessService;

        $grant = $service->openAccountantClientFile($this->accountant(), 9);

        $this->assertNotNull($grant);
        $this->assertSame('accounts_validation', $grant->quick_reason_code);
        $this->assertSame('quick', $grant->grant_type);
        $this->assertSame(15, (int) $grant->starts_at->diffInMinutes($grant->ends_at));
        $originalEnd = $grant->ends_at->format('Y-m-d H:i:s');

        Carbon::setTestNow(Carbon::parse('2026-11-10 07:05:00', 'UTC'));
        $again = $service->openAccountantClientFile($this->accountant(), 9);

        $this->assertNull($again);
        $this->assertSame(1, ClientAccessGrant::query()->count());
        $this->assertSame($originalEnd, ClientAccessGrant::query()->first()->ends_at->format('Y-m-d H:i:s'));
    }

    public function test_a_new_open_after_the_window_starts_a_fresh_fifteen_minutes(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-10 07:00:00', 'UTC'));
        $this->insertOffice();
        $this->insertClient(9, 'ABC123');
        $service = new CrmAccessService;
        $service->openAccountantClientFile($this->accountant(), 9);

        Carbon::setTestNow(Carbon::parse('2026-11-10 07:16:00', 'UTC'));
        $next = $service->openAccountantClientFile($this->accountant(), 9);

        $this->assertNotNull($next);
        $this->assertSame(2, ClientAccessGrant::query()->count());
        $this->assertSame(15, (int) $next->starts_at->diffInMinutes($next->ends_at));
    }

    public function test_accountant_does_not_keep_standing_file_access_when_allocation_is_off(): void
    {
        config(['crm_access.strict_allocation' => false]);
        $this->insertOffice();
        $this->insertClient(9, 'ABC123');
        $accountant = $this->accountant();

        $this->assertFalse(StaffClientVisibility::canAccessClientOrLead(9, $accountant));

        $grant = (new CrmAccessService)->openAccountantClientFile($accountant, 9);

        $this->assertNotNull($grant);
        $this->assertTrue(StaffClientVisibility::canAccessClientOrLead(9, $accountant));
        $this->assertNull((new CrmAccessService)->openAccountantClientFile($accountant, 9));
        $this->assertSame(1, ClientAccessGrant::query()->count());
    }

    public function test_allocated_accountant_does_not_receive_another_grant(): void
    {
        $this->insertOffice();
        $this->insertClient(9, 'ABC123');
        DB::table('client_matters')->insert([
            'id' => 1,
            'client_id' => 9,
            'sel_person_responsible' => 5,
        ]);

        $grant = (new CrmAccessService)->openAccountantClientFile($this->accountant(), 9);

        $this->assertNull($grant);
        $this->assertSame(0, ClientAccessGrant::query()->count());
    }

    public function test_other_roles_and_locked_files_cannot_open_from_the_list(): void
    {
        $this->insertClient(10, 'LOCK1');
        config(['crm.super_admin_only_client_file_ids' => ['LOCK1']]);

        $service = new CrmAccessService;
        $other = new Staff;
        $other->id = 8;
        $other->role = 13;
        $other->office_id = 1;

        try {
            $service->openAccountantClientFile($other, 10);
            $this->fail('A non-accountant was allowed to open a client from the lists.');
        } catch (CrmAccessDeniedException $e) {
            $this->assertStringContainsString('accounts team', $e->getMessage());
        }

        try {
            $service->openAccountantClientFile($this->accountant(), 10);
            $this->fail('A locked client file was opened from the lists.');
        } catch (CrmAccessDeniedException $e) {
            $this->assertStringContainsString('super admin', $e->getMessage());
        }

        $this->assertSame(0, ClientAccessGrant::query()->count());
    }

    private function accountant(): Staff
    {
        $staff = new Staff;
        $staff->id = 5;
        $staff->role = 15;
        $staff->office_id = 1;
        $staff->status = 1;

        return $staff;
    }

    private function insertOffice(): void
    {
        DB::table('branches')->insert([
            'id' => 1,
            'office_name' => 'Melbourne',
        ]);
    }

    private function insertClient(int $id, string $fileId): void
    {
        DB::table('admins')->insert([
            'id' => $id,
            'type' => 'client',
            'client_id' => $fileId,
            'user_id' => null,
            'first_name' => 'Test',
            'last_name' => 'Client',
        ]);
    }

    private function ensureSchema(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->string('client_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->timestamps();
        });

        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('office_name')->nullable();
            $table->timestamps();
        });

        Schema::create('client_matters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('sel_migration_agent')->nullable();
            $table->unsignedBigInteger('sel_person_responsible')->nullable();
            $table->unsignedBigInteger('sel_person_assisting')->nullable();
        });

        Schema::create('client_access_grants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id');
            $table->unsignedBigInteger('admin_id');
            $table->string('record_type', 10);
            $table->string('grant_type', 20);
            $table->string('access_type', 20);
            $table->string('status', 20)->default('pending');
            $table->string('quick_reason_code', 50)->nullable();
            $table->text('requester_note')->nullable();
            $table->unsignedBigInteger('office_id')->nullable();
            $table->string('office_label_snapshot')->nullable();
            $table->unsignedBigInteger('team_id')->nullable();
            $table->string('team_label_snapshot')->nullable();
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('approved_by_staff_id')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->text('revoke_reason')->nullable();
            $table->timestamps();
        });
    }
}
