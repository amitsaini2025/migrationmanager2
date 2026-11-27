<?php

namespace Tests\Feature;

use App\Http\Middleware\TrackStaffCrmActivity;
use App\Http\Middleware\VerifyCsrfToken;
use App\Models\CheckinHistory;
use App\Models\CheckinLog;
use App\Models\Staff;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class OfficeVisitCompleteSessionTest extends TestCase
{
    protected Staff $staff;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            VerifyCsrfToken::class,
            TrackStaffCrmActivity::class,
        ]);

        $this->createSchema();

        $this->staff = Staff::query()->create([
            'first_name' => 'Office',
            'last_name' => 'Attendee',
            'email' => 'office-complete-session@test.com',
            'password' => Hash::make('password'),
            'role' => 1,
            'status' => 1,
        ]);
    }

    public function test_complete_session_moves_attending_visit_to_completed_status(): void
    {
        $visit = CheckinLog::query()->create([
            'client_id' => null,
            'user_id' => $this->staff->id,
            'visit_purpose' => 'Paid app booked regarding his file',
            'contact_type' => 'Walk-in',
            'status' => 2,
            'date' => now()->toDateString(),
            'sesion_start' => now()->format('Y-m-d H:i'),
            'wait_time' => '00h:13m:25s',
        ]);

        // complete_session echoes JSON (legacy); assert via persisted state.
        $this->actingAs($this->staff, 'admin')
            ->post('/complete_session', [
                'id' => $visit->id,
                'attendcountdata' => '00h:05m:10s',
            ])
            ->assertOk();

        $visit->refresh();
        $this->assertSame(1, (int) $visit->status);
        $this->assertNotEmpty($visit->sesion_end);
        $this->assertSame('00h:05m:10s', $visit->attend_time);

        $this->assertTrue(
            CheckinHistory::query()
                ->where('checkin_id', $visit->id)
                ->where('subject', 'has completed session')
                ->exists()
        );
    }

    public function test_waiting_and_completed_action_markup_remain_distinct_from_mark_complete(): void
    {
        $blade = file_get_contents(resource_path('views/crm/officevisits/index.blade.php'));
        $this->assertNotFalse($blade);

        $this->assertStringContainsString('attendsessionforclient', $blade);
        $this->assertStringContainsString('markcompletesession', $blade);
        $this->assertStringContainsString('badge badge-secondary', $blade);
        $this->assertStringNotContainsString('class="btn btn-success completesession"', $blade);
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
