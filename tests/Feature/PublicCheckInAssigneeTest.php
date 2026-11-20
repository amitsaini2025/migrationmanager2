<?php

namespace Tests\Feature;

use App\Http\Middleware\TrackStaffCrmActivity;
use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Admin;
use App\Models\CheckinLog;
use App\Models\FrontDeskCheckIn;
use App\Models\Staff;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PublicCheckInAssigneeTest extends TestCase
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
            'email' => 'public-checkin-actor@test.com',
            'password' => Hash::make('password'),
            'role' => 1,
            'status' => 1,
        ]);

        $this->allowlistedAssignee = Staff::query()->create([
            'first_name' => 'Arun',
            'last_name' => 'Test',
            'email' => 'arun.assignee@test.com',
            'password' => Hash::make('password'),
            'role' => 16,
            'status' => 1,
        ]);

        Staff::query()->create([
            'first_name' => 'Other',
            'last_name' => 'Staff',
            'email' => 'public-other.staff@test.com',
            'password' => Hash::make('password'),
            'role' => 16,
            'status' => 1,
        ]);

        config([
            'lead_migrate_invite.assignee_staff_keys' => ['arun', 'ajay'],
            'lead_migrate_invite.assignee_staff_aliases' => [],
        ]);
    }

    public function test_public_check_in_assignee_route_is_registered(): void
    {
        $this->assertTrue(Route::has('officevisits.public-checkin-assignee'));
        $this->assertTrue(Route::has('officevisits.migrate-enquiry-assignee'));
    }

    public function test_set_assignee_updates_a_public_waiting_visit_and_linked_lead(): void
    {
        $lead = Admin::query()->create([
            'type' => 'lead',
            'first_name' => 'Public',
            'last_name' => 'Lead',
            'email' => 'public.waiting@test.com',
            'phone' => '0499111222',
            'user_id' => null,
            'is_archived' => 0,
            'is_deleted' => null,
        ]);

        $visit = CheckinLog::query()->create([
            'client_id' => $lead->id,
            'user_id' => null,
            'visit_purpose' => 'General Enquiry',
            'contact_type' => 'Lead',
            'status' => 0,
            'date' => now()->toDateString(),
        ]);

        FrontDeskCheckIn::query()->create([
            'phone_normalized' => '0499111222',
            'lead_id' => $lead->id,
            'visit_reason' => 'general_enquiry',
            'metadata' => [
                'source' => 'public',
                'checkin_log_id' => $visit->id,
            ],
        ]);

        $this->actingAs($this->staff, 'admin')
            ->postJson(route('officevisits.public-checkin-assignee'), [
                'id' => $visit->id,
                'assignee_id' => $this->allowlistedAssignee->id,
            ])
            ->assertOk()
            ->assertJson(['status' => true]);

        $visit->refresh();
        $lead->refresh();

        $this->assertSame((int) $this->allowlistedAssignee->id, (int) $visit->user_id);
        $this->assertSame((int) $this->allowlistedAssignee->id, (int) $lead->user_id);
    }

    public function test_set_assignee_rejects_visits_that_are_not_public_check_ins(): void
    {
        $visit = CheckinLog::query()->create([
            'client_id' => null,
            'user_id' => $this->staff->id,
            'visit_purpose' => 'General Enquiry',
            'contact_type' => 'Walk-in',
            'status' => 0,
            'date' => now()->toDateString(),
        ]);

        $this->actingAs($this->staff, 'admin')
            ->postJson(route('officevisits.public-checkin-assignee'), [
                'id' => $visit->id,
                'assignee_id' => $this->allowlistedAssignee->id,
            ])
            ->assertStatus(422)
            ->assertJson(['status' => false]);

        $visit->refresh();
        $this->assertSame((int) $this->staff->id, (int) $visit->user_id);
    }

    public function test_set_assignee_rejects_staff_outside_the_named_list(): void
    {
        $other = Staff::query()->where('email', 'public-other.staff@test.com')->firstOrFail();

        $visit = CheckinLog::query()->create([
            'user_id' => null,
            'visit_purpose' => 'General Enquiry',
            'contact_type' => 'Walk-in',
            'status' => 0,
            'date' => now()->toDateString(),
        ]);

        FrontDeskCheckIn::query()->create([
            'phone_normalized' => '0499111333',
            'metadata' => [
                'source' => 'public',
                'checkin_log_id' => $visit->id,
            ],
        ]);

        $this->actingAs($this->staff, 'admin')
            ->postJson(route('officevisits.public-checkin-assignee'), [
                'id' => $visit->id,
                'assignee_id' => $other->id,
            ])
            ->assertStatus(422)
            ->assertJson(['status' => false]);

        $visit->refresh();
        $this->assertNull($visit->user_id);
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

        if (! Schema::hasTable('front_desk_check_ins')) {
            Schema::create('front_desk_check_ins', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('admin_id')->nullable();
                $table->string('phone_normalized')->nullable();
                $table->string('email')->nullable();
                $table->unsignedBigInteger('client_id')->nullable();
                $table->unsignedBigInteger('lead_id')->nullable();
                $table->string('visit_reason')->nullable();
                $table->text('visit_notes')->nullable();
                $table->json('metadata')->nullable();
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
