<?php

namespace Tests\Unit\Services;

use App\Models\StaffFileTimeEntry;
use App\Models\StaffMatterSession;
use App\Services\StaffDayCrmEventsService;
use App\Services\StaffFileTimeService;
use App\Services\StaffMatterSessionService;
use App\Services\StaffWorkloadService;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StaffMatterSessionServiceTest extends TestCase
{
    private StaffMatterSessionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        config(['app.timezone' => 'Australia/Melbourne']);
        $this->createSchema();
        $this->service = new StaffMatterSessionService(
            new StaffWorkloadService,
            new StaffDayCrmEventsService(new StaffWorkloadService),
        );
    }

    #[Test]
    public function heartbeat_creates_one_session_per_staff_record_and_day(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-15 09:00:00', 'Australia/Melbourne'));
        $this->insertStaff(1);
        $this->insertClient(10);
        $this->insertMatter(5, 10, 'JARN2504926-485_1');

        $first = $this->service->heartbeat(1, 10, 5, 30);
        Carbon::setTestNow(Carbon::parse('2026-09-15 09:01:00', 'Australia/Melbourne'));
        $second = $this->service->heartbeat(1, 10, 5, 90);

        $this->assertSame($first->id, $second->id);
        $this->assertSame(1, DB::table('staff_matter_sessions')->count());
        $this->assertSame(90, (int) $second->focused_seconds);
    }

    #[Test]
    public function stale_close_uses_last_heartbeat_not_now(): void
    {
        $beat = Carbon::parse('2026-09-15 10:00:00', 'Australia/Melbourne');
        Carbon::setTestNow($beat);
        $this->insertStaff(1);
        $this->insertClient(10);
        $this->insertMatter(5, 10, 'JARN2504926-485_1');

        $session = $this->service->heartbeat(1, 10, 5, 600);
        $this->service->promoteIfWritten($session->fresh());

        Carbon::setTestNow($beat->copy()->addMinutes(10));
        $this->service->closeStale(now());

        $closed = StaffMatterSession::query()->find($session->id);
        $this->assertSame(StaffMatterSession::STATUS_CLOSED, $closed->status);
        $this->assertTrue($closed->ended_at->equalTo($beat));
    }

    #[Test]
    public function idle_cut_reduces_focused_seconds(): void
    {
        $start = Carbon::parse('2026-09-15 11:00:00', 'Australia/Melbourne');
        Carbon::setTestNow($start);
        $this->insertStaff(1);
        $this->insertClient(10);

        $session = $this->service->heartbeat(1, 10, null, 0);
        $session->focused_seconds = 900;
        $session->last_heartbeat_at = $start->copy()->addMinutes(15);
        $session->save();

        $idleStart = $start->copy()->addMinutes(10);
        $updated = $this->service->idleCut(1, $session->fresh(), $idleStart);

        $this->assertSame(600, (int) $updated->focused_seconds);
        $this->assertSame(300, (int) $updated->idle_cut_seconds);
    }

    #[Test]
    public function promotion_from_note_inside_window_makes_recorded(): void
    {
        $start = Carbon::parse('2026-09-15 12:00:00', 'Australia/Melbourne');
        Carbon::setTestNow($start);
        $this->insertStaff(1);
        $this->insertClient(10);
        $this->insertMatter(5, 10, 'JARN2504926-485_1');

        $session = $this->service->heartbeat(1, 10, 5, 60);

        DB::table('notes')->insert([
            'id' => 1,
            'user_id' => 1,
            'client_id' => 10,
            'matter_id' => 5,
            'type' => 'client',
            'is_action' => 0,
            'assigned_to' => null,
            'task_group' => 'Call',
            'title' => 'Call note',
            'created_at' => $start->copy()->addMinute(),
            'updated_at' => $start->copy()->addMinute(),
        ]);

        Carbon::setTestNow($start->copy()->addMinutes(2));
        $promoted = $this->service->heartbeat(1, 10, 5, 120);

        $this->assertSame(StaffMatterSession::STATUS_RECORDED, $promoted->status);
        $this->assertFalse((bool) $promoted->is_reviewed_only);
    }

    #[Test]
    public function two_minutes_without_write_becomes_reviewed_only_recorded(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-15 12:30:00', 'Australia/Melbourne'));
        $this->insertStaff(1);
        $this->insertClient(10);
        $this->insertMatter(5, 10, 'JARN2504926-485_1');

        $session = $this->service->heartbeat(1, 10, 5, 130);
        $promoted = $this->service->promoteIfWritten($session);

        $this->assertSame(StaffMatterSession::STATUS_RECORDED, $promoted->status);
        $this->assertTrue((bool) $promoted->is_reviewed_only);
    }

    #[Test]
    public function close_recorded_writes_file_time_feed_row(): void
    {
        $start = Carbon::parse('2026-09-15 13:00:00', 'Australia/Melbourne');
        Carbon::setTestNow($start);
        $this->insertStaff(1);
        $this->insertClient(10);
        $this->insertMatter(5, 10, 'JARN2504926-485_1');

        $session = $this->service->heartbeat(1, 10, 5, 300);
        $this->service->promoteIfWritten($session->fresh());

        Carbon::setTestNow($start->copy()->addMinutes(5));
        $this->service->closeStale(now());

        $closed = StaffMatterSession::query()->find($session->id);
        $this->assertNotNull($closed->activities_log_id);
        $log = DB::table('activities_logs')->where('id', $closed->activities_log_id)->first();
        $this->assertSame('file_time', $log->activity_type);
        $this->assertStringContainsString('logged 5m', (string) $log->subject);
    }

    #[Test]
    public function close_accessed_writes_no_feed_row(): void
    {
        $start = Carbon::parse('2026-09-15 13:10:00', 'Australia/Melbourne');
        Carbon::setTestNow($start);
        $this->insertStaff(1);
        $this->insertClient(10);

        $session = $this->service->heartbeat(1, 10, null, 30);
        Carbon::setTestNow($start->copy()->addMinutes(5));
        $this->service->closeStale(now());

        $closed = StaffMatterSession::query()->find($session->id);
        $this->assertNull($closed->activities_log_id);
        $this->assertSame(0, DB::table('activities_logs')->count());
    }

    #[Test]
    public function split_minutes_sum_to_confirmed_minutes(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-15 14:00:00', 'Australia/Melbourne'));
        $this->insertStaff(1);
        $this->insertClient(10);
        $this->insertMatter(5, 10, 'JARN2504926-485_1');

        DB::table('staff_matter_sessions')->insert([
            'id' => 1,
            'staff_id' => 1,
            'client_id' => 10,
            'client_matter_id' => 5,
            'matter_key' => 5,
            'session_date' => '2026-09-15',
            'status' => StaffMatterSession::STATUS_CLOSED,
            'focused_seconds' => 600,
            'idle_cut_seconds' => 0,
            'confirmed_minutes' => 10,
            'event_count' => 3,
            'is_reviewed_only' => false,
            'started_at' => now()->subHour(),
            'last_heartbeat_at' => now(),
            'ended_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
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
                'title' => 'A',
                'created_at' => now()->subMinutes(50),
                'updated_at' => now()->subMinutes(50),
            ],
            [
                'id' => 2,
                'user_id' => 1,
                'client_id' => 10,
                'matter_id' => 5,
                'type' => 'client',
                'is_action' => 0,
                'assigned_to' => null,
                'task_group' => 'Call',
                'title' => 'B',
                'created_at' => now()->subMinutes(40),
                'updated_at' => now()->subMinutes(40),
            ],
            [
                'id' => 3,
                'user_id' => 1,
                'client_id' => 10,
                'matter_id' => 5,
                'type' => 'client',
                'is_action' => 0,
                'assigned_to' => null,
                'task_group' => 'Call',
                'title' => 'C',
                'created_at' => now()->subMinutes(30),
                'updated_at' => now()->subMinutes(30),
            ],
        ]);

        $board = $this->service->sessionsForBoard(1);
        $events = $board['auto'][0]['events'] ?? [];
        $sum = array_sum(array_column($events, 'minutes'));
        $this->assertSame(10, $sum);
        $this->assertSame(4, $events[0]['minutes']);
    }

    #[Test]
    public function auto_session_does_not_change_workload_or_manual_running_timer(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-15 15:00:00', 'Australia/Melbourne'));
        $this->insertStaff(1);
        $this->insertClient(10);
        $this->insertMatter(5, 10, 'JARN2504926-485_1');

        $fileTime = new StaffFileTimeService(new StaffWorkloadService);
        $manual = $fileTime->start(1, [
            'kind' => StaffFileTimeEntry::KIND_DRAFT,
            'title' => 'Letter',
            'client_matter_id' => 5,
        ]);

        $this->service->heartbeat(1, 10, 5, 120);

        $this->assertTrue((bool) $manual->fresh()->is_running);
        $this->assertSame(1, StaffFileTimeEntry::query()->where('is_running', true)->count());
        $this->assertSame(0, DB::table('activities_logs')->where('activity_type', 'file_time')->count());
    }

    #[Test]
    public function board_refs_use_client_id_and_matter_nickname(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-15 16:00:00', 'Australia/Melbourne'));
        $this->insertStaff(1);
        $this->insertClient(10);
        $this->insertMatter(5, 10, 'APC_8');

        $this->service->heartbeat(1, 10, 5, 30);

        $opened = $this->service->sessionsForBoard(1)['opened'] ?? [];
        $this->assertNotEmpty($opened);
        $this->assertSame('JANE0000010-APC_8', $opened[0]['ref']);
        $this->assertTrue($opened[0]['is_current']);
        $this->assertSame(
            route('clients.detail', [base64_encode(convert_uuencode('10')), 'APC_8', 'activityfeed']),
            $opened[0]['url']
        );

        $this->service->heartbeat(1, 10, 5, 120);
        $auto = $this->service->sessionsForBoard(1)['auto'] ?? [];
        $this->assertNotEmpty($auto);
        $this->assertSame('JANE0000010-APC_8', $auto[0]['ref']);
        $this->assertSame(
            route('clients.detail', [base64_encode(convert_uuencode('10')), 'APC_8', 'activityfeed']),
            $auto[0]['url']
        );
    }

    #[Test]
    public function opened_board_keeps_stale_accessed_files_but_marks_them_not_current(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-15 16:00:00', 'Australia/Melbourne'));
        $this->insertStaff(1);
        $this->insertClient(10);
        $this->insertMatter(5, 10, 'APC_8');

        $this->service->heartbeat(1, 10, 5, 30);
        Carbon::setTestNow(Carbon::parse('2026-09-15 16:05:00', 'Australia/Melbourne'));

        $opened = $this->service->sessionsForBoard(1)['opened'] ?? [];
        $this->assertNotEmpty($opened);
        $this->assertSame('JANE0000010-APC_8', $opened[0]['ref']);
        $this->assertFalse($opened[0]['is_current']);
    }

    #[Test]
    public function auto_board_uses_one_minute_floor_but_keeps_longer_actual_minutes(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-15 17:00:00', 'Australia/Melbourne'));
        $this->insertStaff(1);
        $this->insertClient(10);
        $this->insertMatter(5, 10, 'APC_8');

        DB::table('notes')->insert([
            'id' => 90,
            'user_id' => 1,
            'client_id' => 10,
            'matter_id' => 5,
            'type' => 'client',
            'is_action' => 0,
            'assigned_to' => null,
            'task_group' => 'Call',
            'title' => 'Quick note',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Short focus still floors to 1m once recorded.
        $this->service->heartbeat(1, 10, 5, 10);
        $short = $this->service->heartbeat(1, 10, 5, 20);
        $this->assertSame(StaffMatterSession::STATUS_RECORDED, $short->status);

        $autoShort = $this->service->sessionsForBoard(1)['auto'] ?? [];
        $this->assertNotEmpty($autoShort);
        $this->assertSame(1, (int) $autoShort[0]['confirmed_minutes']);
        $this->assertGreaterThanOrEqual(1, (int) $autoShort[0]['event_count']);
        $this->assertNotEmpty($autoShort[0]['events']);
        $this->assertSame(1, (int) ($autoShort[0]['events'][0]['minutes'] ?? 0));

        // Longer focus reports actual rounded minutes (not stuck at 1).
        $this->service->heartbeat(1, 10, 5, 125);
        $autoLong = $this->service->sessionsForBoard(1)['auto'] ?? [];
        $this->assertNotEmpty($autoLong);
        $this->assertSame(2, (int) $autoLong[0]['confirmed_minutes']);
    }

    private function createSchema(): void
    {
        foreach (['staff_matter_sessions', 'staff_file_time_entries', 'activities_logs', 'notes', 'client_matters', 'admins', 'staff'] as $table) {
            Schema::dropIfExists($table);
        }

        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
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
            $table->unsignedInteger('matter_id')->nullable();
            $table->unsignedInteger('assigned_to')->nullable();
            $table->string('type')->nullable();
            $table->tinyInteger('is_action')->default(0);
            $table->string('task_group')->nullable();
            $table->string('title')->nullable();
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

        Schema::create('staff_matter_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id');
            $table->unsignedInteger('client_matter_id')->nullable();
            $table->unsignedInteger('client_id');
            $table->unsignedInteger('matter_key')->default(0);
            $table->date('session_date');
            $table->string('status', 16)->default('accessed');
            $table->unsignedInteger('focused_seconds')->default(0);
            $table->unsignedInteger('idle_cut_seconds')->default(0);
            $table->unsignedSmallInteger('confirmed_minutes')->nullable();
            $table->unsignedSmallInteger('event_count')->nullable();
            $table->boolean('is_reviewed_only')->default(false);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('last_heartbeat_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->unsignedInteger('activities_log_id')->nullable();
            $table->timestamps();
            $table->unique(['staff_id', 'client_id', 'matter_key', 'session_date']);
        });
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
            'client_id' => 'JANE'.str_pad((string) $id, 7, '0', STR_PAD_LEFT),
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
