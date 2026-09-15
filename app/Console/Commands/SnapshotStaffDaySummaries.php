<?php

namespace App\Console\Commands;

use App\Models\StaffDaySummary;
use App\Services\StaffDaySummaryService;
use App\Services\StaffMatterSessionService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SnapshotStaffDaySummaries extends Command
{
    protected $signature = 'my-day:snapshot-summaries {--date= : Melbourne Y-m-d to snapshot (default: today)}';

    protected $description = 'Save each active staff member\'s My day copy-summary for a Melbourne date';

    public function handle(
        StaffMatterSessionService $sessions,
        StaffDaySummaryService $summaries,
    ): int {
        $dateOption = $this->option('date');
        $day = is_string($dateOption) && $dateOption !== ''
            ? Carbon::parse($dateOption, (string) config('app.timezone'))->startOfDay()
            : null;

        $sessions->closeStale(now());
        $count = $summaries->snapshotActiveStaff(StaffDaySummary::SOURCE_SCHEDULE, $day);
        $this->info("Saved {$count} staff day summary snapshot(s).");

        return self::SUCCESS;
    }
}
