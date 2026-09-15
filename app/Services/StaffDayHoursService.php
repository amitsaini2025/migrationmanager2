<?php

namespace App\Services;

use App\Http\Middleware\TrackStaffCrmActivity;
use App\Models\StaffLoginLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StaffDayHoursService
{
    public function __construct(
        protected StaffWorkloadService $workloadService,
    ) {}

    /**
     * Hours-in-CRM header for the Melbourne calendar day — not last-login duration.
     *
     * Span: first TrackStaffCrmActivity presence today → now (capped at end of day).
     * Session last_activity can extend the end if later. Login-log "Logged in" is ignored.
     *
     * @return array{label: string, minutes: int, seconds: int, source: string, date: string}
     */
    public function forStaff(int $staffId, ?Carbon $day = null): array
    {
        [$start, $end] = $this->workloadService->dayBounds($day);
        $tz = (string) config('app.timezone');
        $dateKey = $start->toDateString();
        $now = now($tz);

        $presence = StaffLoginLog::query()
            ->where('user_id', $staffId)
            ->where('message', TrackStaffCrmActivity::ACTIVITY_MESSAGE)
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at')
            ->first();

        if (! $presence) {
            return $this->payload(0, 'none', $dateKey);
        }

        $from = Carbon::parse($presence->created_at)->timezone($tz);
        $to = Carbon::parse($presence->updated_at ?? $presence->created_at)->timezone($tz);

        // Live "until now" is only for the staff viewing their own day — not when an admin
        // opens a colleague's summary (that would inflate hours to the current clock).
        $viewerId = Auth::guard('admin')->id();
        $extendToNow = $viewerId === null || (int) $viewerId === $staffId;

        if ($extendToNow && $now->betweenIncluded($start, $end) && $now->gt($to)) {
            $to = $now->copy();
        }

        if ($extendToNow && Schema::hasTable('sessions')) {
            $sessionLast = DB::table('sessions')
                ->where('user_id', $staffId)
                ->max('last_activity');
            if ($sessionLast) {
                $sessionAt = Carbon::createFromTimestamp((int) $sessionLast)->timezone($tz);
                if ($sessionAt->betweenIncluded($start, $end) && $sessionAt->gt($to)) {
                    $to = $sessionAt;
                }
            }
        }

        if ($to->gt($end)) {
            $to = $end->copy();
        }
        if ($to->lt($from)) {
            $to = $from->copy();
        }

        $seconds = max(0, $to->getTimestamp() - $from->getTimestamp());

        return $this->payload($seconds, 'today_presence', $dateKey);
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
