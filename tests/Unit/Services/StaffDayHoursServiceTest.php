<?php

namespace Tests\Unit\Services;

use App\Http\Middleware\TrackStaffCrmActivity;
use App\Services\StaffDayHoursService;
use App\Services\StaffWorkloadService;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StaffDayHoursServiceTest extends TestCase
{
    private StaffDayHoursService $service;

    protected function setUp(): void
    {
        parent::setUp();
        config(['app.timezone' => 'Australia/Melbourne']);
        $this->createSchema();
        $this->service = new StaffDayHoursService(new StaffWorkloadService);
    }

    #[Test]
    public function hours_span_first_crm_presence_today_to_now_not_last_login(): void
    {
        $today = Carbon::parse('2026-09-15 15:00:00', 'Australia/Melbourne');
        Carbon::setTestNow($today);

        DB::table('staff_login_logs')->insert([
            [
                'id' => 1,
                'user_id' => 1,
                'message' => 'Logged in successfully',
                'created_at' => Carbon::parse('2026-09-14 08:00:00', 'Australia/Melbourne'),
                'updated_at' => Carbon::parse('2026-09-14 08:00:00', 'Australia/Melbourne'),
            ],
            [
                'id' => 2,
                'user_id' => 1,
                'message' => TrackStaffCrmActivity::ACTIVITY_MESSAGE,
                'created_at' => Carbon::parse('2026-09-15 09:00:00', 'Australia/Melbourne'),
                'updated_at' => Carbon::parse('2026-09-15 09:05:00', 'Australia/Melbourne'),
            ],
        ]);

        $hours = $this->service->forStaff(1, $today, true);

        $this->assertSame('today_presence', $hours['source']);
        $this->assertSame('6h 0m', $hours['label']);
        $this->assertSame(6 * 60, $hours['minutes']);
    }

    #[Test]
    public function hours_do_not_extend_to_now_without_a_matching_viewer(): void
    {
        $today = Carbon::parse('2026-09-15 15:00:00', 'Australia/Melbourne');
        Carbon::setTestNow($today);

        DB::table('staff_login_logs')->insert([
            'id' => 1,
            'user_id' => 1,
            'message' => TrackStaffCrmActivity::ACTIVITY_MESSAGE,
            'created_at' => Carbon::parse('2026-09-15 09:00:00', 'Australia/Melbourne'),
            'updated_at' => Carbon::parse('2026-09-15 09:05:00', 'Australia/Melbourne'),
        ]);

        $hours = $this->service->forStaff(1, $today);

        $this->assertSame(5, $hours['minutes']);
        $this->assertSame('5m', $hours['label']);
    }

    private function createSchema(): void
    {
        Schema::dropIfExists('staff_login_logs');
        Schema::dropIfExists('sessions');

        Schema::create('staff_login_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->string('message')->nullable();
            $table->timestamps();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->integer('last_activity')->nullable();
        });
    }
}
