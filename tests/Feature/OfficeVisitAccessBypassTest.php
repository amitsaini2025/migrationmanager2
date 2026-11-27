<?php

namespace Tests\Feature;

use App\Http\Middleware\TrackStaffCrmActivity;
use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Admin;
use App\Models\CheckinHistory;
use App\Models\CheckinLog;
use App\Models\Staff;
use App\Support\StaffClientVisibility;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class OfficeVisitAccessBypassTest extends TestCase
{
    protected Staff $restrictedStaff;

    protected Admin $unallocatedClient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            VerifyCsrfToken::class,
            TrackStaffCrmActivity::class,
        ]);

        $this->createSchema();

        config([
            'crm_access.strict_allocation' => true,
            'crm_access.exempt_role_ids' => [1, 17],
            'crm_access.exempt_staff_ids' => [],
            'crm.super_admin_only_client_file_ids' => [],
        ]);

        $this->restrictedStaff = Staff::query()->create([
            'first_name' => 'Desk',
            'last_name' => 'Operator',
            'email' => 'office-visit-bypass@test.com',
            'password' => Hash::make('password'),
            'role' => 13,
            'status' => 1,
        ]);

        $this->unallocatedClient = Admin::query()->create([
            'type' => 'client',
            'first_name' => 'Locked',
            'last_name' => 'Client',
            'email' => 'locked.officevisit@test.com',
            'client_id' => 'OVBYPASS1',
            'user_id' => 99999,
            'is_archived' => 0,
            'is_deleted' => null,
        ]);
    }

    public function test_office_visit_controller_does_not_gate_on_crm_record_access(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/CRM/OfficeVisitController.php'));

        $this->assertNotFalse($controller);
        $this->assertStringNotContainsString('ensureCrmRecordAccess', $controller);
        $this->assertStringNotContainsString('EnsuresCrmRecordAccess', $controller);
    }

    public function test_other_controllers_still_use_crm_record_access_gates(): void
    {
        $accounts = file_get_contents(app_path('Http/Controllers/CRM/ClientAccountsController.php'));

        $this->assertNotFalse($accounts);
        $this->assertStringContainsString('ensureCrmRecordAccess', $accounts);
    }

    public function test_unallocated_staff_can_complete_linked_office_visit_without_file_access(): void
    {
        $this->assertFalse(
            StaffClientVisibility::canAccessClientOrLead(
                (int) $this->unallocatedClient->id,
                $this->restrictedStaff
            )
        );

        $visit = CheckinLog::query()->create([
            'client_id' => $this->unallocatedClient->id,
            'user_id' => $this->restrictedStaff->id,
            'visit_purpose' => 'REG - test visit',
            'contact_type' => 'Client',
            'status' => 2,
            'date' => now()->toDateString(),
            'sesion_start' => now()->format('Y-m-d H:i'),
            'wait_time' => '00h:10m:00s',
        ]);

        $this->actingAs($this->restrictedStaff, 'admin')
            ->post('/complete_session', [
                'id' => $visit->id,
                'attendcountdata' => '00h:04m:00s',
            ])
            ->assertOk();

        $visit->refresh();
        $this->assertSame(1, (int) $visit->status);
        $this->assertSame('00h:04m:00s', $visit->attend_time);

        $this->assertTrue(
            CheckinHistory::query()
                ->where('checkin_id', $visit->id)
                ->where('subject', 'has completed session')
                ->exists()
        );

        $this->assertFalse(
            StaffClientVisibility::canAccessClientOrLead(
                (int) $this->unallocatedClient->id,
                $this->restrictedStaff
            )
        );
    }

    public function test_unallocated_staff_can_attend_linked_office_visit_without_file_access(): void
    {
        $visit = CheckinLog::query()->create([
            'client_id' => $this->unallocatedClient->id,
            'user_id' => $this->restrictedStaff->id,
            'visit_purpose' => 'REG - attend test',
            'contact_type' => 'Client',
            'status' => 0,
            'date' => now()->toDateString(),
            'wait_time' => null,
        ]);

        $this->actingAs($this->restrictedStaff, 'admin')
            ->post('/attend_session', [
                'id' => $visit->id,
                'waitcountdata' => '00h:12m:00s',
                'waitingtype' => 1,
            ])
            ->assertOk();

        $visit->refresh();
        $this->assertSame(2, (int) $visit->status);
        $this->assertSame('00h:12m:00s', $visit->wait_time);
        $this->assertNotEmpty($visit->sesion_start);
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
                $table->string('client_id')->nullable();
                $table->unsignedInteger('user_id')->nullable();
                $table->unsignedInteger('is_archived')->nullable();
                $table->unsignedInteger('is_deleted')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('admins', function (Blueprint $table) {
                if (! Schema::hasColumn('admins', 'client_id')) {
                    $table->string('client_id')->nullable();
                }
            });
        }

        if (! Schema::hasTable('client_matters')) {
            Schema::create('client_matters', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id')->nullable();
                $table->unsignedBigInteger('sel_migration_agent')->nullable();
                $table->unsignedBigInteger('sel_person_responsible')->nullable();
                $table->unsignedBigInteger('sel_person_assisting')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('client_access_grants')) {
            Schema::create('client_access_grants', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('staff_id')->nullable();
                $table->unsignedBigInteger('admin_id')->nullable();
                $table->string('record_type')->nullable();
                $table->string('grant_type')->nullable();
                $table->string('access_type')->nullable();
                $table->string('status')->nullable();
                $table->timestamp('requested_at')->nullable();
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('ends_at')->nullable();
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
                $table->string('sesion_start')->nullable();
                $table->string('sesion_end')->nullable();
                $table->string('wait_time')->nullable();
                $table->string('attend_time')->nullable();
                $table->unsignedInteger('wait_type')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('checkin_logs', function (Blueprint $table) {
                if (! Schema::hasColumn('checkin_logs', 'sesion_start')) {
                    $table->string('sesion_start')->nullable();
                }
                if (! Schema::hasColumn('checkin_logs', 'sesion_end')) {
                    $table->string('sesion_end')->nullable();
                }
                if (! Schema::hasColumn('checkin_logs', 'wait_time')) {
                    $table->string('wait_time')->nullable();
                }
                if (! Schema::hasColumn('checkin_logs', 'attend_time')) {
                    $table->string('attend_time')->nullable();
                }
                if (! Schema::hasColumn('checkin_logs', 'wait_type')) {
                    $table->unsignedInteger('wait_type')->nullable();
                }
            });
        }

        if (! Schema::hasTable('checkin_histories')) {
            Schema::create('checkin_histories', function (Blueprint $table) {
                $table->id();
                $table->string('subject')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('checkin_id')->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('activities_logs')) {
            Schema::create('activities_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->string('subject')->nullable();
                $table->text('description')->nullable();
                $table->string('activity_type')->nullable();
                $table->unsignedTinyInteger('task_status')->nullable();
                $table->unsignedTinyInteger('pin')->nullable();
                $table->timestamps();
            });
        }
    }
}
