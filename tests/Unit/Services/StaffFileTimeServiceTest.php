<?php

namespace Tests\Unit\Services;

use App\Models\StaffFileTimeEntry;
use App\Services\StaffDayCrmEventsService;
use App\Services\StaffDayHoursService;
use App\Services\StaffFileTimeService;
use App\Services\StaffMatterSessionService;
use App\Services\StaffWorkloadService;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StaffFileTimeServiceTest extends TestCase
{
    private StaffFileTimeService $service;

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.timezone' => 'Australia/Melbourne']);
        $this->createSchema();
        $this->service = new StaffFileTimeService(new StaffWorkloadService);
    }

    #[Test]
    public function done_with_matter_creates_file_time_feed_row_without_task_status(): void
    {
        $today = Carbon::parse('2026-09-15 10:00:00', 'Australia/Melbourne');
        Carbon::setTestNow($today);

        $this->insertStaff(1);
        $this->insertClient(10);
        $this->insertMatter(5, 10, 'JARN2504926-485_1');

        $entry = $this->service->start(1, [
            'kind' => StaffFileTimeEntry::KIND_DRAFT,
            'title' => 'Nomination letter',
            'client_matter_id' => 5,
        ]);

        $done = $this->service->done(1, $entry, 11);

        $this->assertSame(StaffFileTimeEntry::STATUS_DONE, $done->status);
        $this->assertSame(11, $done->confirmed_minutes);
        $this->assertNotNull($done->activities_log_id);

        $log = DB::table('activities_logs')->where('id', $done->activities_log_id)->first();
        $this->assertNotNull($log);
        $this->assertSame('file_time', $log->activity_type);
        $this->assertSame(0, (int) $log->task_status);
        $this->assertSame(1, (int) $log->created_by);
        $this->assertSame(10, (int) $log->client_id);
        $this->assertStringContainsString('logged 11m', (string) $log->subject);
        $this->assertStringNotContainsString('completed action for', (string) $log->subject);

        $workload = (new StaffWorkloadService)->getDashboardWorkload(1, $today);
        $this->assertSame(0, $workload['completed_excl_call']['total']);
        $this->assertSame(0, $workload['call_completed']['total']);
        $this->assertSame(0, $workload['updated']['total']);
    }

    #[Test]
    public function done_admin_creates_no_feed_row(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-15 11:00:00', 'Australia/Melbourne'));
        $this->insertStaff(1);

        $entry = $this->service->start(1, [
            'kind' => StaffFileTimeEntry::KIND_OTHER,
            'title' => 'Mailbox skim',
            'admin' => true,
        ]);

        $done = $this->service->done(1, $entry, 15);

        $this->assertNull($done->activities_log_id);
        $this->assertSame(0, DB::table('activities_logs')->count());
    }

    #[Test]
    public function log_completed_creates_done_entry_and_feed_row(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-15 12:00:00', 'Australia/Melbourne'));
        $this->insertStaff(1);
        $this->insertClient(10);
        $this->insertMatter(5, 10, 'JARN2504926-485_1');

        $done = $this->service->logCompleted(1, [
            'kind' => StaffFileTimeEntry::KIND_IMMI,
            'title' => 'Immi portal check',
            'confirmed_minutes' => 8,
            'client_matter_id' => 5,
        ]);

        $this->assertSame(StaffFileTimeEntry::STATUS_DONE, $done->status);
        $this->assertFalse((bool) $done->is_running);
        $this->assertSame(8, $done->confirmed_minutes);
        $this->assertSame(480, (int) $done->clock_seconds);
        $this->assertNotNull($done->activities_log_id);

        $log = DB::table('activities_logs')->where('id', $done->activities_log_id)->first();
        $this->assertNotNull($log);
        $this->assertSame('file_time', $log->activity_type);
        $this->assertStringContainsString('logged 8m', (string) $log->subject);
    }

    #[Test]
    public function log_completed_admin_skips_feed(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-15 12:30:00', 'Australia/Melbourne'));
        $this->insertStaff(1);

        $done = $this->service->logCompleted(1, [
            'kind' => StaffFileTimeEntry::KIND_OTHER,
            'title' => 'Teams chat',
            'confirmed_minutes' => 5,
            'admin' => true,
        ]);

        $this->assertSame(StaffFileTimeEntry::STATUS_DONE, $done->status);
        $this->assertNull($done->activities_log_id);
        $this->assertSame(0, DB::table('activities_logs')->count());
    }

    #[Test]
    public function copy_summary_includes_manual_logs(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-15 14:00:00', 'Australia/Melbourne'));
        $this->insertStaff(1);
        $this->insertClient(10);
        $this->insertMatter(5, 10, 'JARN2504926-485_1');

        $this->service->logCompleted(1, [
            'kind' => StaffFileTimeEntry::KIND_IMMI,
            'title' => 'Immi portal check',
            'confirmed_minutes' => 8,
            'client_matter_id' => 5,
        ]);
        $this->service->logCompleted(1, [
            'kind' => StaffFileTimeEntry::KIND_OTHER,
            'title' => 'Mailbox skim',
            'confirmed_minutes' => 12,
            'admin' => true,
        ]);

        $crmEvents = $this->createMock(StaffDayCrmEventsService::class);
        $crmEvents->method('forStaff')->willReturn(['items' => [], 'more' => 0]);

        $hours = $this->createMock(StaffDayHoursService::class);
        $hours->method('forStaff')->willReturn(['label' => '3h 20m']);

        $summary = $this->service->copySummary(1, $crmEvents, $hours);

        $this->assertStringContainsString('— Manual logs —', $summary['text']);
        $this->assertStringContainsString('JARN2504926-485_1 · Immi/portal · Immi portal check · 8m', $summary['text']);
        $this->assertStringContainsString('— Admin / no file —', $summary['text']);
        $this->assertStringContainsString('Admin · other · Mailbox skim · 12m', $summary['text']);
        $this->assertCount(1, $summary['overlay']);
        $this->assertCount(1, $summary['admin']);
        $this->assertSame(0, $summary['crm_more']);
        $this->assertSame(0, $summary['crm_total']);
    }

    #[Test]
    public function copy_summary_includes_truncated_crm_more_line(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-15 14:00:00', 'Australia/Melbourne'));
        $this->insertStaff(1);

        $crmEvents = $this->createMock(StaffDayCrmEventsService::class);
        $crmEvents->method('forStaff')->willReturn([
            'items' => [
                [
                    'key' => 'email:1',
                    'kind' => 'Email out',
                    'title' => 'Hello',
                    'ref' => 'REF_1',
                    'time' => '2:00 pm',
                ],
            ],
            'more' => 37,
            'total' => 38,
        ]);

        $hours = $this->createMock(StaffDayHoursService::class);
        $hours->method('forStaff')->willReturn(['label' => '1h']);

        $summary = $this->service->copySummary(1, $crmEvents, $hours);

        $this->assertSame(37, $summary['crm_more']);
        $this->assertSame(38, $summary['crm_total']);
        $this->assertStringContainsString('… and 37 more', $summary['text']);
    }

    #[Test]
    public function copy_summary_appends_duration_after_clock_time_for_crm_events(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-15 16:00:00', 'Australia/Melbourne'));
        $this->insertStaff(1);
        $this->insertClient(10);
        $this->insertMatter(5, 10, 'JARN2504926-485_1');

        $this->service->logCompleted(1, [
            'kind' => StaffFileTimeEntry::KIND_IMMI,
            'title' => 'Immi portal check',
            'confirmed_minutes' => 18,
            'client_matter_id' => 5,
        ]);

        $crmEvents = $this->createMock(StaffDayCrmEventsService::class);
        $crmEvents->method('forStaff')->willReturn([
            'items' => [
                [
                    'key' => 'note:99',
                    'kind' => 'Call note',
                    'title' => 'Matter Discussion',
                    'ref' => 'JARN2504926-485_1',
                    'time' => '3:03 pm',
                    'client_id' => 10,
                    'client_matter_id' => 5,
                ],
            ],
            'more' => 0,
        ]);

        $hours = $this->createMock(StaffDayHoursService::class);
        $hours->method('forStaff')->willReturn(['label' => '2h 10m']);

        $summary = $this->service->copySummary(1, $crmEvents, $hours);

        $this->assertStringContainsString(
            'JARN2504926-485_1 · Call note · Matter Discussion · 3:03 pm · 18m',
            $summary['text']
        );
    }

    #[Test]
    public function copy_summary_prefers_auto_event_minutes_over_manual_log_fallback(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-15 16:30:00', 'Australia/Melbourne'));
        $this->insertStaff(1);

        $crmEvents = $this->createMock(StaffDayCrmEventsService::class);
        $crmEvents->method('forStaff')->willReturn([
            'items' => [
                [
                    'key' => 'note:1',
                    'kind' => 'In-person note',
                    'title' => 'Matter Discussion',
                    'ref' => 'Somnath',
                    'time' => '4:08 pm',
                    'client_id' => 22,
                    'client_matter_id' => null,
                ],
            ],
            'more' => 0,
        ]);

        $hours = $this->createMock(StaffDayHoursService::class);
        $hours->method('forStaff')->willReturn(['label' => '1h']);

        $matterSessions = $this->createMock(StaffMatterSessionService::class);
        $matterSessions->method('sessionsForBoard')->willReturn([
            'auto' => [
                [
                    'client_id' => 22,
                    'client_matter_id' => null,
                    'confirmed_minutes' => 25,
                    'event_count' => 1,
                    'is_reviewed_only' => false,
                    'ref' => 'Somnath',
                ],
            ],
            'opened' => [],
            'event_minutes' => ['note:1' => 25],
        ]);

        $summary = $this->service->copySummary(1, $crmEvents, $hours, $matterSessions);

        $this->assertStringContainsString(
            'Somnath · In-person note · Matter Discussion · 4:08 pm · 25m',
            $summary['text']
        );
    }

    #[Test]
    public function copy_summary_still_open_includes_opened_sessions_with_url(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-15 17:15:00', 'Australia/Melbourne'));
        $this->insertStaff(1);

        $crmEvents = $this->createMock(StaffDayCrmEventsService::class);
        $crmEvents->method('forStaff')->willReturn(['items' => [], 'more' => 0]);

        $hours = $this->createMock(StaffDayHoursService::class);
        $hours->method('forStaff')->willReturn(['label' => '1h']);

        $url = route('clients.detail', [base64_encode(convert_uuencode('10')), 'APC_8']);
        $matterSessions = $this->createMock(StaffMatterSessionService::class);
        $matterSessions->method('sessionsForBoard')->willReturn([
            'auto' => [],
            'opened' => [
                [
                    'id' => 9,
                    'ref' => 'JANE0000010-APC_8',
                    'url' => $url,
                    'client_id' => 10,
                    'client_matter_id' => 5,
                    'focused_seconds' => 45,
                    'minutes' => 1,
                ],
            ],
            'event_minutes' => [],
        ]);

        $summary = $this->service->copySummary(1, $crmEvents, $hours, $matterSessions);

        $this->assertStringContainsString('— Still open —', $summary['text']);
        $this->assertStringContainsString('JANE0000010-APC_8', $summary['text']);
        $this->assertCount(1, $summary['still_open']);
        $this->assertSame('JANE0000010-APC_8', $summary['still_open'][0]['ref']);
        $this->assertSame($url, $summary['still_open'][0]['url']);
    }

    #[Test]
    public function attach_minutes_to_crm_events_uses_manual_logs_when_no_auto_split(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-15 17:00:00', 'Australia/Melbourne'));
        $this->insertStaff(1);
        $this->insertClient(10);
        $this->insertMatter(5, 10, 'JARN2504926-485_1');

        $this->service->logCompleted(1, [
            'kind' => StaffFileTimeEntry::KIND_IMMI,
            'title' => 'Immi portal check',
            'confirmed_minutes' => 18,
            'client_matter_id' => 5,
        ]);

        $board = $this->service->boardForStaff(1);
        $enriched = $this->service->attachMinutesToCrmEvents(
            [
                'items' => [
                    [
                        'key' => 'note:99',
                        'kind' => 'Call note',
                        'title' => 'Matter Discussion',
                        'ref' => 'JARN2504926-485_1',
                        'time' => '3:03 pm',
                        'client_id' => 10,
                        'client_matter_id' => 5,
                    ],
                    [
                        'key' => 'note:100',
                        'kind' => 'Email',
                        'title' => 'Other matter',
                        'ref' => 'OTHER',
                        'time' => '2:00 pm',
                        'client_id' => 99,
                        'client_matter_id' => 88,
                    ],
                ],
                'more' => 0,
            ],
            ['auto' => [], 'opened' => [], 'event_minutes' => []],
            $board,
        );

        $this->assertSame(18, $enriched['items'][0]['minutes'] ?? null);
        $this->assertArrayNotHasKey('minutes', $enriched['items'][1]);
    }

    #[Test]
    public function attach_minutes_to_crm_events_prefers_auto_event_minutes(): void
    {
        $enriched = $this->service->attachMinutesToCrmEvents(
            [
                'items' => [
                    [
                        'key' => 'note:1',
                        'kind' => 'In-person note',
                        'title' => 'Matter Discussion',
                        'ref' => 'Somnath',
                        'time' => '4:08 pm',
                        'client_id' => 22,
                        'client_matter_id' => null,
                    ],
                ],
                'more' => 0,
            ],
            [
                'auto' => [
                    [
                        'client_id' => 22,
                        'client_matter_id' => null,
                        'confirmed_minutes' => 40,
                    ],
                ],
                'opened' => [],
                'event_minutes' => ['note:1' => 25],
            ],
            ['entries' => []],
        );

        $this->assertSame(25, $enriched['items'][0]['minutes'] ?? null);
    }

    #[Test]
    public function second_start_pauses_previous_running_timer(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-15 12:00:00', 'Australia/Melbourne'));
        $this->insertStaff(1);

        $first = $this->service->start(1, [
            'kind' => StaffFileTimeEntry::KIND_INTERNAL,
            'title' => 'Teams chat',
            'admin' => true,
        ]);
        $this->assertTrue((bool) $first->fresh()->is_running);

        $second = $this->service->start(1, [
            'kind' => StaffFileTimeEntry::KIND_DRAFT,
            'title' => 'Letter',
            'admin' => true,
        ]);

        $this->assertFalse((bool) $first->fresh()->is_running);
        $this->assertTrue((bool) $second->fresh()->is_running);
        $this->assertSame(1, StaffFileTimeEntry::query()->where('is_running', true)->count());
    }

    #[Test]
    public function reopen_continues_from_confirmed_minutes_and_done_updates_same_feed_row(): void
    {
        $today = Carbon::parse('2026-09-15 13:00:00', 'Australia/Melbourne');
        Carbon::setTestNow($today);

        $this->insertStaff(1);
        $this->insertClient(10);
        $this->insertMatter(5, 10, 'JARN2504926-485_1');

        $entry = $this->service->start(1, [
            'kind' => StaffFileTimeEntry::KIND_DRAFT,
            'title' => 'Nomination letter',
            'client_matter_id' => 5,
        ]);
        $done = $this->service->done(1, $entry->fresh(), 11);
        $feedId = $done->activities_log_id;

        Carbon::setTestNow($today->copy()->addMinutes(5));
        $reopened = $this->service->reopen(1, $done->fresh());

        $this->assertSame(StaffFileTimeEntry::STATUS_DOING, $reopened->status);
        $this->assertTrue((bool) $reopened->is_running);
        $this->assertSame(11 * 60, (int) $reopened->clock_seconds);
        $this->assertNull($reopened->confirmed_minutes);
        $this->assertSame($feedId, $reopened->activities_log_id);
        $this->assertFalse($this->service->serialize($reopened)['posted']);

        $again = $this->service->done(1, $reopened->fresh(), 18);

        $this->assertSame($feedId, $again->activities_log_id);
        $this->assertSame(1, DB::table('activities_logs')->count());
        $log = DB::table('activities_logs')->where('id', $feedId)->first();
        $this->assertStringContainsString('logged 18m', (string) $log->subject);
    }

    private function createSchema(): void
    {
        Schema::dropIfExists('staff_file_time_entries');
        Schema::dropIfExists('activities_logs');
        Schema::dropIfExists('client_matters');
        Schema::dropIfExists('notes');
        Schema::dropIfExists('admins');
        Schema::dropIfExists('staff');

        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->timestamps();
        });

        Schema::create('admins', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        Schema::create('client_matters', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('client_id')->nullable();
            $table->string('client_unique_matter_no')->nullable();
            $table->timestamps();
        });

        Schema::create('notes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('client_id')->nullable();
            $table->unsignedInteger('assigned_to')->nullable();
            $table->string('type')->nullable();
            $table->tinyInteger('is_action')->default(0);
            $table->string('status')->nullable();
            $table->string('task_group')->nullable();
            $table->timestamp('action_date')->nullable();
            $table->timestamps();
        });

        Schema::create('activities_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('client_id')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->string('subject')->nullable();
            $table->text('description')->nullable();
            $table->string('activity_type', 64)->nullable();
            $table->string('use_for', 64)->nullable();
            $table->string('task_group')->nullable();
            $table->integer('task_status')->default(0);
            $table->integer('pin')->default(0);
            $table->timestamps();
        });

        Schema::create('staff_file_time_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id');
            $table->unsignedInteger('client_matter_id')->nullable();
            $table->unsignedInteger('client_id')->nullable();
            $table->string('kind', 32);
            $table->string('title');
            $table->string('status', 16)->default('doing');
            $table->boolean('is_running')->default(false);
            $table->unsignedInteger('clock_seconds')->default(0);
            $table->unsignedSmallInteger('confirmed_minutes')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedInteger('activities_log_id')->nullable();
            $table->timestamps();
        });

        DB::statement(
            'CREATE UNIQUE INDEX staff_file_time_entries_one_running_per_staff
             ON staff_file_time_entries (staff_id)
             WHERE is_running = 1'
        );
    }

    private function insertStaff(int $id): void
    {
        DB::table('staff')->insert([
            'id' => $id,
            'first_name' => 'Test',
            'last_name' => 'Staff',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function insertClient(int $id): void
    {
        DB::table('admins')->insert([
            'id' => $id,
            'type' => 'client',
            'first_name' => 'Jane',
            'last_name' => 'Client',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function insertMatter(int $id, int $clientId, string $ref): void
    {
        DB::table('client_matters')->insert([
            'id' => $id,
            'client_id' => $clientId,
            'client_unique_matter_no' => $ref,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
