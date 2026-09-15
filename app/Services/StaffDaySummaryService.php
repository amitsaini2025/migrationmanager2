<?php

namespace App\Services;

use App\Models\Staff;
use App\Models\StaffDaySummary;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class StaffDaySummaryService
{
    public function __construct(
        protected StaffWorkloadService $workloadService,
        protected StaffFileTimeService $fileTime,
        protected StaffDayCrmEventsService $crmEvents,
        protected StaffDayHoursService $hours,
        protected StaffMatterSessionService $matterSessions,
    ) {}

    public function find(int $staffId, ?Carbon $day = null): ?StaffDaySummary
    {
        if (! Schema::hasTable('staff_day_summaries')) {
            return null;
        }

        [$start] = $this->workloadService->dayBounds($day);

        return StaffDaySummary::query()
            ->where('staff_id', $staffId)
            ->whereDate('summary_date', $start->toDateString())
            ->first();
    }

    /**
     * @param  array{text?: string}  $summary
     */
    public function upsert(int $staffId, array $summary, string $source, ?Carbon $day = null): StaffDaySummary
    {
        [$start] = $this->workloadService->dayBounds($day);
        $date = $start->toDateString();
        $body = (string) ($summary['text'] ?? '');

        $row = $this->find($staffId, $day);
        if ($row === null) {
            $row = new StaffDaySummary;
            $row->staff_id = $staffId;
            $row->summary_date = $date;
        }

        $row->body = $body;
        $row->source = $source;
        $row->saved_at = now();
        $row->save();

        return $row->fresh() ?? $row;
    }

    /**
     * @return array{text: string, stored: bool, source: string|null, saved_at: string|null}
     */
    public function payload(?StaffDaySummary $row): array
    {
        if ($row === null) {
            return [
                'text' => '',
                'stored' => false,
                'source' => null,
                'saved_at' => null,
            ];
        }

        $tz = (string) config('app.timezone');

        return [
            'text' => (string) $row->body,
            'stored' => true,
            'source' => (string) $row->source,
            'saved_at' => $row->saved_at?->timezone($tz)->toIso8601String(),
        ];
    }

    public function snapshotStaff(int $staffId, string $source, ?Carbon $day = null): StaffDaySummary
    {
        $summary = $this->fileTime->copySummary(
            $staffId,
            $this->crmEvents,
            $this->hours,
            $this->matterSessions,
            $day,
        );

        return $this->upsert($staffId, $summary, $source, $day);
    }

    public function snapshotActiveStaff(string $source = StaffDaySummary::SOURCE_SCHEDULE, ?Carbon $day = null): int
    {
        if (! Schema::hasTable('staff_day_summaries')) {
            return 0;
        }

        $count = 0;
        Staff::query()->active()->orderBy('id')->each(function (Staff $staff) use ($source, $day, &$count): void {
            $this->snapshotStaff((int) $staff->id, $source, $day);
            $count++;
        });

        return $count;
    }
}
