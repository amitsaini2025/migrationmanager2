<?php

namespace Tests\Feature;

use App\Http\Middleware\TrackStaffCrmActivity;
use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Admin;
use App\Models\CheckinLog;
use App\Models\Staff;
use App\Support\LeadMigrateInviteChannels;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MigrateEnquiryAssigneeTest extends TestCase
{
    protected Staff $staff;

    protected Staff $allowlistedAssignee;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            VerifyCsrfToken::class,
            TrackStaffCrmActivity::class,
        ]);

        $this->createSchema();

        $this->staff = Staff::query()->create([
            'first_name' => 'Reception',
            'last_name' => 'User',
            'email' => 'migrate-assignee-actor@test.com',
            'password' => Hash::make('password'),
            'role' => 1,
            'status' => 1,
        ]);

        $this->allowlistedAssignee = Staff::query()->create([
            'first_name' => 'Ajay',
            'last_name' => 'Test',
            'email' => 'ajay.assignee@test.com',
            'password' => Hash::make('password'),
            'role' => 16,
            'status' => 1,
        ]);

        Staff::query()->create([
            'first_name' => 'Other',
            'last_name' => 'Staff',
            'email' => 'other.staff@test.com',
            'password' => Hash::make('password'),
            'role' => 16,
            'status' => 1,
        ]);

        config([
            'lead_migrate_invite.visit_purpose' => 'Migration Client Enquiry',
            'lead_migrate_invite.assignee_staff_keys' => ['ajay', 'arun'],
            'lead_migrate_invite.assignee_staff_aliases' => [
                'sayed' => ['sayed', 'syed'],
                'jsapreet' => ['jsapreet', 'jaspreet'],
            ],
        ]);
    }

    #[Test]
    public function migrate_enquiry_assignee_route_is_registered(): void
    {
        $this->assertTrue(Route::has('officevisits.migrate-enquiry-assignee'));
    }

    #[Test]
    public function resolve_migrate_assignee_staff_only_returns_allowlisted_active_staff(): void
    {
        $staff = LeadMigrateInviteChannels::resolveMigrateAssigneeStaff();

        $this->assertTrue($staff->contains(fn (Staff $row): bool => (int) $row->id === (int) $this->allowlistedAssignee->id));
        $this->assertFalse($staff->contains(fn (Staff $row): bool => strtolower((string) $row->first_name) === 'other'));
    }

    #[Test]
    public function set_assignee_updates_waiting_visit_and_linked_lead(): void
    {
        $lead = Admin::query()->create([
            'type' => 'lead',
            'first_name' => 'Migrate',
            'last_name' => 'Lead',
            'email' => 'migrate.waiting@test.com',
            'phone' => '0411222333',
            'user_id' => $this->staff->id,
            'is_archived' => 0,
            'is_deleted' => null,
        ]);

        $visit = CheckinLog::query()->create([
            'client_id' => $lead->id,
            'user_id' => $this->staff->id,
            'visit_purpose' => 'Migration Client Enquiry',
            'contact_type' => 'Lead',
            'status' => 0,
            'date' => now()->toDateString(),
        ]);

        $this->actingAs($this->staff, 'admin')
            ->postJson(route('officevisits.migrate-enquiry-assignee'), [
                'id' => $visit->id,
                'assignee_id' => $this->allowlistedAssignee->id,
            ])
            ->assertOk()
            ->assertJson([
                'status' => true,
            ]);

        $visit->refresh();
        $lead->refresh();

        $this->assertSame((int) $this->allowlistedAssignee->id, (int) $visit->user_id);
        $this->assertSame((int) $this->allowlistedAssignee->id, (int) $lead->user_id);
    }

    #[Test]
    public function set_assignee_rejects_non_migrate_enquiry_visits(): void
    {
        $lead = Admin::query()->create([
            'type' => 'lead',
            'first_name' => 'Other',
            'last_name' => 'Lead',
            'email' => 'other.waiting@test.com',
            'phone' => '0411222444',
            'user_id' => $this->staff->id,
            'is_archived' => 0,
            'is_deleted' => null,
        ]);

        $visit = CheckinLog::query()->create([
            'client_id' => $lead->id,
            'user_id' => $this->staff->id,
            'visit_purpose' => 'General enquiry',
            'contact_type' => 'Lead',
            'status' => 0,
            'date' => now()->toDateString(),
        ]);

        $this->actingAs($this->staff, 'admin')
            ->postJson(route('officevisits.migrate-enquiry-assignee'), [
                'id' => $visit->id,
                'assignee_id' => $this->allowlistedAssignee->id,
            ])
            ->assertStatus(422)
            ->assertJson([
                'status' => false,
            ]);

        $visit->refresh();
        $this->assertSame((int) $this->staff->id, (int) $visit->user_id);
    }

    #[Test]
    public function set_assignee_rejects_staff_outside_allowlist(): void
    {
        $other = Staff::query()->where('email', 'other.staff@test.com')->firstOrFail();

        $lead = Admin::query()->create([
            'type' => 'lead',
            'first_name' => 'Migrate',
            'last_name' => 'Lead2',
            'email' => 'migrate.waiting2@test.com',
            'phone' => '0411222555',
            'user_id' => $this->staff->id,
            'is_archived' => 0,
            'is_deleted' => null,
        ]);

        $visit = CheckinLog::query()->create([
            'client_id' => $lead->id,
            'user_id' => $this->staff->id,
            'visit_purpose' => 'Migration Client Enquiry',
            'contact_type' => 'Lead',
            'status' => 0,
            'date' => now()->toDateString(),
        ]);

        $this->actingAs($this->staff, 'admin')
            ->postJson(route('officevisits.migrate-enquiry-assignee'), [
                'id' => $visit->id,
                'assignee_id' => $other->id,
            ])
            ->assertStatus(422)
            ->assertJson([
                'status' => false,
            ]);
    }

    private function createSchema(): void
    {
        if (! Schema::hasTable('staff')) {
            Schema::create('staff', function (Blueprint $table) {
                $table->id();
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('email')->nullable();
                $table->string('password')->nullable();
                $table->unsignedInteger('role')->nullable();
                $table->unsignedInteger('status')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('admins')) {
            Schema::create('admins', function (Blueprint $table) {
                $table->id();
                $table->string('type')->nullable();
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->unsignedInteger('user_id')->nullable();
                $table->unsignedInteger('is_archived')->nullable();
                $table->unsignedInteger('is_deleted')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('checkin_logs')) {
            Schema::create('checkin_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('visit_purpose')->nullable();
                $table->unsignedBigInteger('office')->nullable();
                $table->string('contact_type')->nullable();
                $table->unsignedInteger('status')->nullable();
                $table->date('date')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->id();
                $table->string('notification_type')->nullable();
                $table->unsignedBigInteger('module_id')->nullable();
                $table->unsignedBigInteger('receiver_id')->nullable();
                $table->unsignedBigInteger('sender_id')->nullable();
                $table->string('url')->nullable();
                $table->text('message')->nullable();
                $table->unsignedTinyInteger('seen')->nullable();
                $table->unsignedTinyInteger('receiver_status')->nullable();
                $table->unsignedTinyInteger('sender_status')->nullable();
                $table->timestamps();
            });
        }
    }
}
