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
        $this->assertSame(10, $status['span_days']);
    }

    #[Test]
    public function open_four_week_window_is_advertising(): void
    {
        $status = LmtStatus::assess('t', '2026-10-01', '2026-10-29', $this->today);

        $this->assertSame(LmtStatus::ADVERTISING, $status['key']);
        $this->assertSame(29, $status['span_days']);
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
    public function inclusive_twenty_eight_days_is_long_enough(): void
    {
        $status = LmtStatus::assess(true, '2026-08-01', '2026-08-28', $this->today);

        $this->assertSame(LmtStatus::READY, $status['key']);
        $this->assertSame(28, $status['span_days']);
    }

    #[Test]
    public function twenty_seven_inclusive_days_is_too_short(): void
    {
        $status = LmtStatus::assess(true, '2026-08-01', '2026-08-27', $this->today);

        $this->assertSame(LmtStatus::TOO_SHORT, $status['key']);
        $this->assertSame(27, $status['span_days']);
    }

    #[Test]
    public function overlapping_advertisements_count_a_shared_day_once(): void
    {
        $status = LmtStatus::assess(true, null, null, $this->today, true, [
            ['publication' => 'Seek', 'opened_on' => '2026-08-01', 'closed_on' => '2026-08-14'],
            ['publication' => 'Indeed', 'opened_on' => '2026-08-14', 'closed_on' => '2026-08-28'],
        ]);

        $this->assertSame(LmtStatus::READY, $status['key']);
        $this->assertSame(28, $status['span_days']);
    }

    #[Test]
    public function a_gap_between_short_advertisements_is_too_short(): void
    {
        $status = LmtStatus::assess(true, null, null, $this->today, true, [
            ['publication' => 'Seek', 'opened_on' => '2026-08-01', 'closed_on' => '2026-08-14'],
            ['publication' => 'Indeed', 'opened_on' => '2026-08-15', 'closed_on' => '2026-08-28'],
        ]);

        $this->assertSame(LmtStatus::TOO_SHORT, $status['key']);
    }

    #[Test]
    public function one_long_advertisement_meets_the_length_when_the_other_is_short(): void
    {
        $status = LmtStatus::assess(true, null, null, $this->today, true, [
            ['publication' => 'Seek', 'opened_on' => '2026-08-01', 'closed_on' => '2026-08-28'],
            ['publication' => 'Indeed', 'opened_on' => '2026-09-01', 'closed_on' => '2026-09-05'],
        ]);

        $this->assertSame(LmtStatus::READY, $status['key']);
        $this->assertSame(28, $status['span_days']);
    }

    #[Test]
    public function an_open_advertisement_is_ready_once_twenty_eight_days_have_passed(): void
    {
        $status = LmtStatus::assess(true, null, null, $this->today, true, [
            ['publication' => 'Seek', 'opened_on' => '2026-09-01', 'closed_on' => null],
            ['publication' => 'Indeed', 'opened_on' => '2026-09-01', 'closed_on' => '2026-09-05'],
        ]);

        $this->assertSame(LmtStatus::READY, $status['key']);
    }

    #[Test]
    public function an_advertisement_that_closes_today_below_twenty_eight_days_is_too_short(): void
    {
        $status = LmtStatus::assess(true, null, null, $this->today, true, [
            ['publication' => 'Seek', 'opened_on' => '2026-10-01', 'closed_on' => '2026-10-09'],
            ['publication' => 'Indeed', 'opened_on' => '2026-10-01', 'closed_on' => '2026-10-09'],
        ]);

        $this->assertSame(LmtStatus::TOO_SHORT, $status['key']);
        $this->assertSame(9, $status['span_days']);
    }

    #[Test]
    public function an_open_advertisement_is_still_advertising_before_twenty_eight_days(): void
    {
        $status = LmtStatus::assess(true, null, null, $this->today, true, [
            ['publication' => 'Seek', 'opened_on' => '2026-10-01', 'closed_on' => null],
            ['publication' => 'Indeed', 'opened_on' => '2026-10-01', 'closed_on' => '2026-10-03'],
        ]);

        $this->assertSame(LmtStatus::ADVERTISING, $status['key']);
        $this->assertSame(9, $status['span_days']);
    }

    #[Test]
    public function the_earlier_open_date_outside_four_months_is_expired(): void
    {
        $status = LmtStatus::assess(true, null, null, $this->today, true, [
            ['publication' => 'Seek', 'opened_on' => '2026-06-01', 'closed_on' => '2026-06-28'],
            ['publication' => 'Indeed', 'opened_on' => '2026-08-01', 'closed_on' => '2026-08-10'],
        ]);

        $this->assertSame(LmtStatus::EXPIRED, $status['key']);
    }

    #[Test]
    public function one_advertisement_keeps_the_matter_incomplete_once_advertisements_are_in_use(): void
    {
        $status = LmtStatus::assess(true, '2026-08-01', '2026-08-28', $this->today, true, [
            ['publication' => 'Seek', 'opened_on' => '2026-08-01', 'closed_on' => '2026-08-28'],
        ]);

        $this->assertSame(LmtStatus::INCOMPLETE, $status['key']);
    }

    #[Test]
    public function not_required_wins_over_the_advertisements(): void
    {
        $status = LmtStatus::assess(false, null, null, $this->today, true, [
            ['publication' => 'Seek', 'opened_on' => '2026-08-01', 'closed_on' => '2026-08-28'],
            ['publication' => 'Indeed', 'opened_on' => '2026-08-01', 'closed_on' => '2026-08-28'],
        ]);

        $this->assertSame(LmtStatus::NOT_REQUIRED, $status['key']);
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
