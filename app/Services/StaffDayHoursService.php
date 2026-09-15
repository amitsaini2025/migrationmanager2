<?php

namespace App\Services;

use App\Http\Middleware\TrackStaffCrmActivity;
use App\Models\StaffLoginLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StaffDayHoursService
{
    public function __construct(
        protected StaffWorkloadService $workloadService,
    ) {}

    /**
     * Hours-in-CRM header for today. Presence only — not an efficiency score.
     *
     * Formula: prefer today's TrackStaffCrmActivity row span (created_at → updated_at).
     * Fallback: earliest login / presence message today → latest updated_at / sessions.last_activity.
     *
     * @return array{label: string, minutes: int, seconds: int, source: string, date: string}
     */
    public function forStaff(int $staffId, ?Carbon $day = null): array
    {
        [$start, $end] = $this->workloadService->dayBounds($day);
        $dateKey = $start->toDateString();

        $presence = StaffLoginLog::query()
            ->where('user_id', $staffId)
            ->where('message', TrackStaffCrmActivity::ACTIVITY_MESSAGE)
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at')
            ->first();

        if ($presence) {
            $from = Carbon::parse($presence->created_at)->timezone((string) config('app.timezone'));
            $to = Carbon::parse($presence->updated_at ?? $presence->created_at)->timezone((string) config('app.timezone'));
            if ($to->lt($from)) {
                $to = $from->copy();
            }
            $seconds = max(0, (int) $from->diffInSeconds($to));

            return $this->payload($seconds, 'presence', $dateKey);
        }

        $loginLike = StaffLoginLog::query()
            ->where('user_id', $staffId)
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at')
            ->get(['created_at', 'updated_at', 'message']);

        if ($loginLike->isNotEmpty()) {
            $from = Carbon::parse($loginLike->first()->created_at);
            $to = Carbon::parse($loginLike->max('updated_at') ?? $loginLike->last()->created_at);
            $sessionLast = DB::table('sessions')
                ->where('user_id', $staffId)
                ->max('last_activity');
            if ($sessionLast) {
                $sessionAt = Carbon::createFromTimestamp((int) $sessionLast);
                if ($sessionAt->betweenIncluded($start, $end) && $sessionAt->gt($to)) {
                    $to = $sessionAt;
                }
            }
            $seconds = max(0, (int) $from->diffInSeconds($to));

            return $this->payload($seconds, 'login_logs', $dateKey);
        }

        return $this->payload(0, 'none', $dateKey);
    }

    /**
     * @return array{label: string, minutes: int, seconds: int, source: string, date: string}
     */
    protected function payload(int $seconds, string $source, string $dateKey): array
    {
        $minutes = (int) floor($seconds / 60);
        $h = (int) floor($minutes / 60);
        $m = $minutes % 60;

        if ($seconds <= 0) {
            $label = '—';
        } elseif ($h > 0) {
            $label = "{$h}h {$m}m";
        } else {
            $label = "{$m}m";
        }

        return [
            'label' => $label,
            'minutes' => $minutes,
            'seconds' => $seconds,
            'source' => $source,
            'date' => $dateKey,
        ];
    }
}
