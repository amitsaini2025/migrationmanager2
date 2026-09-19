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

        $feedItem = collect($result['items'])->firstWhere('key', 'feed:10');
        $this->assertNotNull($feedItem);
        $this->assertSame(
            route('clients.detail', [base64_encode(convert_uuencode('1')), 'activityfeed']).'#activity_10',
            $feedItem['url']
        );
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

    #[Test]
    public function contact_note_without_matter_uses_client_ref_as_ref(): void
    {
        $today = Carbon::parse('2026-09-15 14:00:00', 'Australia/Melbourne');
        Carbon::setTestNow($today);

        DB::table('admins')->insert([
            'id' => 10,
            'type' => 'client',
            'client_id' => 'PRIY2616001',
            'first_name' => 'Priya',
            'last_name' => 'Singh',
            'created_at' => $today,
            'updated_at' => $today,
        ]);

        DB::table('notes')->insert([
            'id' => 50,
            'user_id' => 1,
            'client_id' => 10,
            'matter_id' => null,
            'type' => 'client',
            'is_action' => 0,
            'assigned_to' => null,
            'task_group' => 'In-Person',
            'title' => 'Matter Discussion',
            'description' => '<p>Discussed next steps</p>',
            'created_at' => $today,
            'updated_at' => $today,
        ]);

        DB::table('activities_logs')->insert([
            'id' => 501,
            'client_id' => 10,
            'created_by' => 1,
            'subject' => 'added In-person Notes',
            'description' => '<p>Discussed next steps</p>',
            'activity_type' => 'note',
            'task_group' => null,
            'task_status' => 0,
            'pin' => 0,
            'created_at' => $today,
            'updated_at' => $today,
        ]);

        $result = $this->service->forStaff(1, $today);
        $item = collect($result['items'])->firstWhere('title', 'Matter Discussion');

        $this->assertNotNull($item);
        $this->assertSame('PRIY2616001', $item['ref']);
        $this->assertSame(
            route('clients.detail', [base64_encode(convert_uuencode('10')), 'activityfeed']).'#activity_501',
            $item['url']
        );
    }

    #[Test]
    public function contact_notes_include_all_note_types(): void
    {
        $today = Carbon::parse('2026-09-15 15:00:00', 'Australia/Melbourne');
        Carbon::setTestNow($today);

        DB::table('notes')->insert([
            [
                'id' => 60,
                'user_id' => 1,
                'client_id' => 10,
                'matter_id' => null,
                'type' => 'client',
                'is_action' => 0,
                'assigned_to' => null,
                'task_group' => 'Email',
                'title' => 'Email follow-up',
                'created_at' => $today,
                'updated_at' => $today,
            ],
            [
                'id' => 61,
                'user_id' => 1,
                'client_id' => 10,
                'matter_id' => null,
                'type' => 'client',
                'is_action' => 0,
                'assigned_to' => null,
                'task_group' => 'Others',
                'title' => 'General update',
                'created_at' => $today,
                'updated_at' => $today,
            ],
            [
                'id' => 62,
                'user_id' => 1,
                'client_id' => 10,
                'matter_id' => null,
                'type' => 'client',
                'is_action' => 0,
                'assigned_to' => null,
                'task_group' => 'Attention',
                'title' => 'Needs review',
                'created_at' => $today,
                'updated_at' => $today,
            ],
            [
                'id' => 63,
                'user_id' => 1,
                'client_id' => 10,
                'matter_id' => null,
                'type' => 'client',
                'is_action' => 1,
                'assigned_to' => null,
                'task_group' => 'Email',
                'title' => 'Assigned action',
                'created_at' => $today,
                'updated_at' => $today,
            ],
        ]);

        $result = $this->service->forStaff(1, $today);
        $notes = collect($result['items'])->filter(fn (array $row): bool => str_starts_with((string) ($row['key'] ?? ''), 'note:'));

        $this->assertSame('Email note', $notes->firstWhere('title', 'Email follow-up')['kind'] ?? null);
        $this->assertSame('Other note', $notes->firstWhere('title', 'General update')['kind'] ?? null);
        $this->assertSame('Attention note', $notes->firstWhere('title', 'Needs review')['kind'] ?? null);
        $this->assertNull($notes->firstWhere('title', 'Assigned action'));
    }

    #[Test]
    public function contact_note_includes_hidden_body_for_show_more(): void
    {
        $today = Carbon::parse('2026-09-15 16:00:00', 'Australia/Melbourne');
        Carbon::setTestNow($today);

        DB::table('notes')->insert([
            'id' => 70,
            'user_id' => 1,
            'client_id' => 10,
            'matter_id' => null,
            'type' => 'client',
            'is_action' => 0,
            'assigned_to' => null,
            'task_group' => 'Attention',
            'title' => 'Matter Discussion',
            'description' => '<p>Test attention. Pls ignore.</p>',
            'created_at' => $today,
            'updated_at' => $today,
        ]);

        $result = $this->service->forStaff(1, $today);
        $item = collect($result['items'])->firstWhere('key', 'note:70');

        $this->assertNotNull($item);
        $this->assertSame('Test attention. Pls ignore.', $item['body'] ?? null);
        $this->assertArrayNotHasKey('body_preview', $item);
        $this->assertArrayNotHasKey('body_expandable', $item);
    }

    #[Test]
    public function contact_note_with_matter_uses_client_and_matter_ref(): void
    {
        $today = Carbon::parse('2026-09-15 14:30:00', 'Australia/Melbourne');
        Carbon::setTestNow($today);

        DB::table('admins')->insert([
            'id' => 11,
            'type' => 'client',
            'client_id' => 'MANP2616002',
            'first_name' => 'Manpreet',
            'last_name' => 'Kaur',
            'created_at' => $today,
            'updated_at' => $today,
        ]);

        DB::table('client_matters')->insert([
            'id' => 7,
            'client_unique_matter_no' => 'ART_1',
            'created_at' => $today,
            'updated_at' => $today,
        ]);

        DB::table('notes')->insert([
            'id' => 51,
            'user_id' => 1,
            'client_id' => 11,
            'matter_id' => 7,
            'type' => 'client',
            'is_action' => 0,
            'assigned_to' => null,
            'task_group' => 'Call',
            'title' => 'Matter Discussion',
            'description' => 'Called about docs',
            'created_at' => $today,
            'updated_at' => $today,
        ]);

        DB::table('activities_logs')->insert([
            'id' => 511,
            'client_id' => 11,
            'created_by' => 1,
            'subject' => 'added Call Notes - ART_1',
            'description' => 'Called about docs',
            'activity_type' => 'note',
            'task_group' => null,
            'task_status' => 0,
            'pin' => 0,
            'created_at' => $today,
            'updated_at' => $today,
        ]);

        $result = $this->service->forStaff(1, $today);
        $item = collect($result['items'])->firstWhere('key', 'note:51');

        $this->assertNotNull($item);
        $this->assertSame('MANP2616002-ART_1', $item['ref']);
        $this->assertSame(
            route('clients.detail', [base64_encode(convert_uuencode('11')), 'ART_1', 'activityfeed']).'#activity_511',
            $item['url']
        );
    }

    #[Test]
    public function for_staff_respects_limit_and_reports_more(): void
    {
        $today = Carbon::parse('2026-09-15 14:00:00', 'Australia/Melbourne');
        Carbon::setTestNow($today);

        $rows = [];
        for ($i = 1; $i <= 55; $i++) {
            $rows[] = [
                'id' => $i,
                'user_id' => 1,
                'subject' => 'Mail '.$i,
                'mail_body_type' => 'sent',
                'conversion_type' => null,
                'created_at' => $today->copy()->subSeconds($i),
                'updated_at' => $today,
            ];
        }
        DB::table('email_logs')->insert($rows);

        $capped = $this->service->forStaff(1, $today, 50);
        $this->assertSame(55, $capped['total']);
        $this->assertCount(50, $capped['items']);
        $this->assertSame(5, $capped['more']);

        $expanded = $this->service->forStaff(1, $today, 55);
        $this->assertSame(55, $expanded['total']);
        $this->assertCount(55, $expanded['items']);
        $this->assertSame(0, $expanded['more']);
    }

    #[Test]
    public function activity_counts_are_uncapped_and_scoped_to_this_staff_today(): void
    {
        $today = Carbon::parse('2026-09-15 14:00:00', 'Australia/Melbourne');
        $yesterday = $today->copy()->subDay();
        Carbon::setTestNow($today);

        DB::table('activities_logs')->insert([
            [
                'id' => 1,
                'client_id' => 1,
                'created_by' => 1,
                'subject' => 'Checklist sent to client',
                'activity_type' => 'note',
                'task_status' => 0,
                'pin' => 0,
                'created_at' => $today,
                'updated_at' => $today,
            ],
            [
                'id' => 2,
                'client_id' => 1,
                'created_by' => 1,
                'subject' => 'Document Checklist sent to client',
                'activity_type' => 'note',
                'task_status' => 0,
                'pin' => 0,
                'created_at' => $today,
                'updated_at' => $today,
            ],
            [
                'id' => 3,
                'client_id' => 1,
                'created_by' => 2,
                'subject' => 'Checklist sent to client',
                'activity_type' => 'note',
                'task_status' => 0,
                'pin' => 0,
                'created_at' => $today,
                'updated_at' => $today,
            ],
            [
                'id' => 4,
                'client_id' => 1,
                'created_by' => 1,
                'subject' => 'completed action for Visa',
                'activity_type' => 'note',
                'task_status' => 1,
                'pin' => 0,
                'created_at' => $today,
                'updated_at' => $today,
            ],
            [
                'id' => 5,
                'client_id' => 1,
                'created_by' => 1,
                'subject' => 'Updated action for Visa',
                'activity_type' => 'note',
                'task_status' => 1,
                'pin' => 0,
                'created_at' => $today,
                'updated_at' => $today,
            ],
            [
                'id' => 6,
                'client_id' => 1,
                'created_by' => 1,
                'subject' => 'Checklist sent to client',
                'activity_type' => 'note',
                'task_status' => 0,
                'pin' => 0,
                'created_at' => $yesterday,
                'updated_at' => $yesterday,
            ],
        ]);

        DB::table('documents')->insert([
            [
                'id' => 1,
                'created_by' => 1,
                'user_id' => null,
                'name' => 'Passport.pdf',
                'created_at' => $today,
                'updated_at' => $today,
            ],
            [
                'id' => 2,
                'created_by' => null,
                'user_id' => 1,
                'name' => 'Form.pdf',
                'created_at' => $today,
                'updated_at' => $today,
            ],
            [
                'id' => 3,
                'created_by' => 2,
                'user_id' => 2,
                'name' => 'Other.pdf',
                'created_at' => $today,
                'updated_at' => $today,
            ],
        ]);

        DB::table('sms_logs')->insert([
            [
                'id' => 1,
                'sender_id' => 1,
                'message_content' => 'Please call',
                'sent_at' => $today,
                'created_at' => $today,
                'updated_at' => $today,
            ],
            [
                'id' => 2,
                'sender_id' => 2,
                'message_content' => 'Other SMS',
                'sent_at' => $today,
                'created_at' => $today,
                'updated_at' => $today,
            ],
        ]);

        $counts = $this->service->activityCountsForStaff(1, $today);
        $list = $this->service->forStaff(1, $today, 50);

        $this->assertSame(2, $counts['checklists']);
        $this->assertSame(2, $counts['documents']);
        $this->assertSame(1, $counts['actions']);
        $this->assertSame(1, $counts['sms']);
        $this->assertSame('2026-09-15', $counts['date']);
        $this->assertNotContains('Checklist sent to client', collect($list['items'])->pluck('title')->all());
    }

    private function createSchema(): void
    {
        foreach (['email_logs', 'documents', 'booking_appointments', 'sms_logs', 'notes', 'activities_logs', 'client_matters', 'admins'] as $table) {
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
            $table->unsignedInteger('client_id')->nullable();
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
            $table->text('description')->nullable();
            $table->string('task_group')->nullable();
            $table->timestamps();
        });

        Schema::create('activities_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('client_id')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('sms_log_id')->nullable();
            $table->string('subject')->nullable();
            $table->text('description')->nullable();
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

        Schema::create('admins', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type')->nullable();
            $table->string('client_id')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->timestamps();
        });
    }
}
