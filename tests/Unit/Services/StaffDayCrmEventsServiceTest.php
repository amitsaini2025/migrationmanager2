<?php

namespace Tests\Unit\Services;

use App\Services\StaffDayCrmEventsService;
use App\Services\StaffWorkloadService;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StaffDayCrmEventsServiceTest extends TestCase
{
    private StaffDayCrmEventsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        config(['app.timezone' => 'Australia/Melbourne']);
        $this->createSchema();
        $this->service = new StaffDayCrmEventsService(new StaffWorkloadService);
    }

    #[Test]
    public function returns_only_this_staff_events_and_excludes_system_generated_email(): void
    {
        $today = Carbon::parse('2026-09-15 14:00:00', 'Australia/Melbourne');
        Carbon::setTestNow($today);

        DB::table('email_logs')->insert([
            [
                'id' => 1,
                'user_id' => 1,
                'subject' => 'Staff sent mail',
                'mail_body_type' => 'sent',
                'conversion_type' => null,
                'created_at' => $today,
                'updated_at' => $today,
            ],
            [
                'id' => 2,
                'user_id' => 1,
                'subject' => 'System receipt',
                'mail_body_type' => 'sent',
                'conversion_type' => 'system_generated',
                'created_at' => $today,
                'updated_at' => $today,
            ],
            [
                'id' => 3,
                'user_id' => 2,
                'subject' => 'Other staff',
                'mail_body_type' => 'sent',
                'conversion_type' => null,
                'created_at' => $today,
                'updated_at' => $today,
            ],
        ]);

        DB::table('activities_logs')->insert([
            'id' => 10,
            'client_id' => 1,
            'created_by' => 1,
            'subject' => 'completed action for X',
            'activity_type' => 'note',
            'task_group' => 'Checklist',
            'task_status' => 1,
            'pin' => 0,
            'created_at' => $today,
            'updated_at' => $today,
        ]);

        $result = $this->service->forStaff(1, $today);
        $kinds = collect($result['items'])->pluck('kind')->all();
        $titles = collect($result['items'])->pluck('title')->all();

        $this->assertContains('Email out', $kinds);
        $this->assertContains('Action completed', $kinds);
        $this->assertContains('Staff sent mail', $titles);
        $this->assertNotContains('System receipt', $titles);
        $this->assertNotContains('Other staff', $titles);
    }

    #[Test]
    public function skips_feed_email_when_email_logs_already_cover_sends(): void
    {
        $today = Carbon::parse('2026-09-15 14:00:00', 'Australia/Melbourne');
        Carbon::setTestNow($today);

        DB::table('email_logs')->insert([
            'id' => 1,
            'user_id' => 1,
            'subject' => 'Staff sent mail',
            'mail_body_type' => 'sent',
            'conversion_type' => null,
            'created_at' => $today,
            'updated_at' => $today,
        ]);

        DB::table('activities_logs')->insert([
            'id' => 11,
            'client_id' => 1,
            'created_by' => 1,
            'subject' => 'uploaded email: Staff sent mail',
            'activity_type' => 'email',
            'task_group' => null,
            'task_status' => 0,
            'pin' => 0,
            'created_at' => $today,
            'updated_at' => $today,
        ]);

        $result = $this->service->forStaff(1, $today);
        $emailTitles = collect($result['items'])
            ->filter(fn (array $row): bool => str_contains((string) $row['kind'], 'Email'))
            ->pluck('title')
            ->all();

        $this->assertSame(['Staff sent mail'], $emailTitles);
    }

    #[Test]
    public function includes_sms_events_using_message_content_column(): void
    {
        $today = Carbon::parse('2026-09-15 14:00:00', 'Australia/Melbourne');
        Carbon::setTestNow($today);

        DB::table('sms_logs')->insert([
            [
                'id' => 1,
                'sender_id' => 1,
                'message_content' => 'Reminder to upload documents',
                'sent_at' => $today,
                'created_at' => $today,
                'updated_at' => $today,
            ],
            [
                'id' => 2,
                'sender_id' => 2,
                'message_content' => 'Other staff SMS',
                'sent_at' => $today,
                'created_at' => $today,
                'updated_at' => $today,
            ],
        ]);

        $result = $this->service->forStaff(1, $today);
        $smsItems = collect($result['items'])->where('kind', 'SMS');

        $this->assertCount(1, $smsItems);
        $this->assertSame('Reminder to upload documents', $smsItems->first()['title']);
    }

    #[Test]
    public function for_staff_on_record_scopes_to_client_and_matter_with_null_matter_notes(): void
    {
        $start = Carbon::parse('2026-09-15 09:00:00', 'Australia/Melbourne');
        $end = Carbon::parse('2026-09-15 17:00:00', 'Australia/Melbourne');

        DB::table('client_matters')->insert([
            ['id' => 5, 'client_unique_matter_no' => 'MAT-A', 'created_at' => $start, 'updated_at' => $start],
            ['id' => 6, 'client_unique_matter_no' => 'MAT-B', 'created_at' => $start, 'updated_at' => $start],
        ]);

        DB::table('notes')->insert([
            [
                'id' => 1,
                'user_id' => 1,
                'client_id' => 10,
                'matter_id' => 5,
                'type' => 'client',
                'is_action' => 0,
                'assigned_to' => null,
                'task_group' => 'Call',
                'title' => 'On matter A',
                'created_at' => $start->copy()->addHour(),
                'updated_at' => $start->copy()->addHour(),
            ],
            [
                'id' => 2,
                'user_id' => 1,
                'client_id' => 10,
                'matter_id' => null,
                'type' => 'client',
                'is_action' => 0,
                'assigned_to' => null,
                'task_group' => 'Call',
                'title' => 'Null matter note',
                'created_at' => $start->copy()->addHours(2),
                'updated_at' => $start->copy()->addHours(2),
            ],
            [
                'id' => 3,
                'user_id' => 1,
                'client_id' => 20,
                'matter_id' => 99,
                'type' => 'client',
                'is_action' => 0,
                'assigned_to' => null,
                'task_group' => 'Call',
                'title' => 'Other client',
                'created_at' => $start->copy()->addHours(3),
                'updated_at' => $start->copy()->addHours(3),
            ],
        ]);

        $events = $this->service->forStaffOnRecord(1, 10, 5, $start, $end);
        $titles = $events->pluck('title')->all();

        $this->assertContains('On matter A', $titles);
        $this->assertContains('Null matter note', $titles);
        $this->assertNotContains('Other client', $titles);
    }

    private function createSchema(): void
    {
        foreach (['email_logs', 'documents', 'booking_appointments', 'sms_logs', 'notes', 'activities_logs', 'client_matters'] as $table) {
            Schema::dropIfExists($table);
        }

        Schema::create('email_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->string('subject')->nullable();
            $table->string('mail_body_type')->nullable();
            $table->string('conversion_type')->nullable();
            $table->unsignedInteger('client_matter_id')->nullable();
            $table->timestamps();
        });

        Schema::create('documents', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->string('name')->nullable();
            $table->string('doc_name')->nullable();
            $table->unsignedInteger('client_matter_id')->nullable();
            $table->timestamps();
        });

        Schema::create('booking_appointments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('client_id')->nullable();
            $table->string('client_name')->nullable();
            $table->string('meeting_type')->nullable();
            $table->string('service_type')->nullable();
            $table->timestamps();
        });

        Schema::create('sms_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('sender_id')->nullable();
            $table->text('message_content')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        Schema::create('notes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('client_id')->nullable();
            $table->unsignedInteger('assigned_to')->nullable();
            $table->unsignedInteger('matter_id')->nullable();
            $table->string('type')->nullable();
            $table->tinyInteger('is_action')->default(0);
            $table->string('title')->nullable();
            $table->string('task_group')->nullable();
            $table->timestamps();
        });

        Schema::create('activities_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('client_id')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->string('subject')->nullable();
            $table->string('activity_type', 64)->nullable();
            $table->string('task_group')->nullable();
            $table->integer('task_status')->default(0);
            $table->integer('pin')->default(0);
            $table->timestamps();
        });

        Schema::create('client_matters', function (Blueprint $table) {
            $table->increments('id');
            $table->string('client_unique_matter_no')->nullable();
            $table->timestamps();
        });
    }
}
