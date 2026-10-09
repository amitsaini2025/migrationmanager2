<?php

namespace Tests\Unit;

use App\Support\CrmSheets;
use App\Support\LmtStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LmtStatusTest extends TestCase
{
    private Carbon $today;

    protected function setUp(): void
    {
        parent::setUp();
        $this->today = Carbon::parse('2026-10-09')->startOfDay();
    }

    #[Test]
    public function empty_record_is_not_recorded(): void
    {
        $status = LmtStatus::assess(null, null, null, $this->today);

        $this->assertSame(LmtStatus::NOT_RECORDED, $status['key']);
        $this->assertNull($status['span_days']);
    }

    #[Test]
    public function explicit_no_is_not_required_even_when_dates_are_present(): void
    {
        $status = LmtStatus::assess(false, '2026-08-01', '2026-08-29', $this->today);

        $this->assertSame(LmtStatus::NOT_REQUIRED, $status['key']);
    }

    #[Test]
    public function missing_end_date_is_incomplete(): void
    {
        $status = LmtStatus::assess(true, '2026-10-01', null, $this->today);

        $this->assertSame(LmtStatus::INCOMPLETE, $status['key']);
    }

    #[Test]
    public function end_before_start_is_incomplete(): void
    {
        $status = LmtStatus::assess('1', '2026-10-01', '2026-09-01', $this->today);

        $this->assertSame(LmtStatus::INCOMPLETE, $status['key']);
    }

    #[Test]
    public function window_shorter_than_four_weeks_is_too_short(): void
    {
        $status = LmtStatus::assess(1, '2026-10-01', '2026-10-10', $this->today);

        $this->assertSame(LmtStatus::TOO_SHORT, $status['key']);
        $this->assertSame(9, $status['span_days']);
    }

    #[Test]
    public function open_four_week_window_is_advertising(): void
    {
        $status = LmtStatus::assess('t', '2026-10-01', '2026-10-29', $this->today);

        $this->assertSame(LmtStatus::ADVERTISING, $status['key']);
        $this->assertSame(28, $status['span_days']);
    }

    #[Test]
    public function future_window_has_not_started(): void
    {
        $status = LmtStatus::assess(true, '2026-11-01', '2026-11-29', $this->today);

        $this->assertSame(LmtStatus::NOT_STARTED, $status['key']);
    }

    #[Test]
    public function finished_window_inside_four_months_is_ready_to_lodge(): void
    {
        $status = LmtStatus::assess(true, '2026-08-01', '2026-08-29', $this->today);

        $this->assertSame(LmtStatus::READY, $status['key']);
    }

    #[Test]
    public function start_on_the_four_month_boundary_is_still_ready(): void
    {
        $status = LmtStatus::assess(true, '2026-06-09', '2026-07-07', $this->today);

        $this->assertSame(LmtStatus::READY, $status['key']);
    }

    #[Test]
    public function start_before_the_four_month_window_is_expired(): void
    {
        $status = LmtStatus::assess(true, '2026-06-08', '2026-07-06', $this->today);

        $this->assertSame(LmtStatus::EXPIRED, $status['key']);
    }

    #[Test]
    public function lmt_sheet_is_registered_beside_the_employer_sheet(): void
    {
        $definitions = CrmSheets::definitions();
        $keys = array_keys($definitions);
        $employerIndex = array_search('employer', $keys, true);
        $lmtIndex = array_search(CrmSheets::KEY_LMT, $keys, true);

        $this->assertNotFalse($employerIndex);
        $this->assertSame($employerIndex + 1, $lmtIndex);
        $this->assertSame('Labour Market Testing', $definitions[CrmSheets::KEY_LMT]);
        $this->assertTrue(Route::has('clients.sheets.lmt'));
        $this->assertTrue(Route::has('clients.sheets.lmt.store'));
    }
}
