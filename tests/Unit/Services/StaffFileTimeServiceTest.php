<?php

namespace Tests\Unit\Services;

use App\Models\StaffFileTimeEntry;
use App\Services\StaffFileTimeService;
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
