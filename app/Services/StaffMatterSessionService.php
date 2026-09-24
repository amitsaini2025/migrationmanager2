<?php

namespace App\Services;

use App\Models\ActivitiesLog;
use App\Models\StaffMatterSession;
use Carbon\Carbon;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class StaffMatterSessionService
{
    public const HEARTBEAT_STALE_SECONDS = 180;

    public const REVIEWED_ONLY_SECONDS = 120;

    public function __construct(
        protected StaffWorkloadService $workloadService,
        protected StaffDayCrmEventsService $crmEvents,
    ) {}

    public function heartbeat(int $staffId, int $clientId, ?int $matterId, int $focusedSeconds): StaffMatterSession
    {
        $session = $this->findOrCreateToday($staffId, $clientId, $matterId);
        $this->applyFocusedSeconds($session, $focusedSeconds);
        $session->last_heartbeat_at = now();
        $this->reopenIfClosed($session);
        $session->save();

        return $this->promoteIfWritten($session->fresh());
    }

    public function blur(int $staffId, int $clientId, ?int $matterId, int $focusedSeconds): StaffMatterSession
    {
        $session = $this->findOrCreateToday($staffId, $clientId, $matterId);
        $this->applyFocusedSeconds($session, $focusedSeconds);
        $session->last_heartbeat_at = now();
        $this->reopenIfClosed($session);
        $session->save();

        return $session->fresh();
    }

    public function idleCut(int $staffId, StaffMatterSession $session, Carbon $idleStartedAt): StaffMatterSession
    {
        $this->assertOwned($staffId, $session);

        if ($session->last_heartbeat_at === null) {
            return $session;
        }

        $lastBeat = $session->last_heartbeat_at;
        if ($idleStartedAt->greaterThan($lastBeat)) {
            return $session;
        }

        $removed = max(0, $lastBeat->getTimestamp() - $idleStartedAt->getTimestamp());
        if ($removed > 0) {
            $session->focused_seconds = max(0, (int) $session->focused_seconds - $removed);
            $session->idle_cut_seconds = (int) $session->idle_cut_seconds + $removed;
            $session->save();
        }

        return $session->fresh();
    }

    public function promoteIfWritten(StaffMatterSession $session): StaffMatterSession
    {
        if ($session->status !== StaffMatterSession::STATUS_ACCESSED) {
            return $session;
        }

        $events = $this->eventsForSession($session);
        if ($events->isNotEmpty()) {
            $session->status = StaffMatterSession::STATUS_RECORDED;
            $session->is_reviewed_only = false;
            $session->save();

            return $session->fresh();
        }

        if ((int) $session->focused_seconds >= self::REVIEWED_ONLY_SECONDS) {
            $session->status = StaffMatterSession::STATUS_RECORDED;
            $session->is_reviewed_only = true;
            $session->save();
        }

        return $session->fresh();
    }

    public function closeStale(Carbon $now): int
    {
        if (! Schema::hasTable('staff_matter_sessions')) {
            return 0;
        }

        $cutoff = $now->copy()->subSeconds(self::HEARTBEAT_STALE_SECONDS);
        $sessions = StaffMatterSession::query()
            ->where('status', '!=', StaffMatterSession::STATUS_CLOSED)
            ->where('last_heartbeat_at', '<', $cutoff)
            ->get();

        $closed = 0;
        foreach ($sessions as $session) {
            $this->closeSession($session, $session->last_heartbeat_at ?? $now);
            $closed++;
        }

        return $closed;
    }

    public function updateMinutes(int $staffId, StaffMatterSession $session, int $minutes): StaffMatterSession
    {
        $this->assertOwned($staffId, $session);

        if ($minutes < 1 || $minutes > 480) {
            throw ValidationException::withMessages(['confirmed_minutes' => 'Minutes must be between 1 and 480.']);
        }

        $session->confirmed_minutes = $minutes;
        $session->save();

        if ($session->activities_log_id && $session->isRecordedForBoard()) {
            $this->postToFeed($session->fresh());
        }

        return $session->fresh();
    }

    public function delete(int $staffId, StaffMatterSession $session): void
    {
        $this->assertOwned($staffId, $session);

        if ($session->activities_log_id !== null) {
            throw ValidationException::withMessages(['session' => 'Posted sessions cannot be deleted.']);
        }

        $session->delete();
    }

    /**
     * @param  Collection<int, array<string, mixed>>|null  $dayEvents
     * @return array{
     *     auto: list<array<string, mixed>>,
     *     opened: list<array<string, mixed>>,
     *     event_minutes: array<string, int>
     * }
     */
    public function sessionsForBoard(int $staffId, ?Carbon $day = null, ?Collection $dayEvents = null): array
    {
        if (! Schema::hasTable('staff_matter_sessions')) {
            return ['auto' => [], 'opened' => [], 'event_minutes' => []];
        }

        [$start, $end] = $this->workloadService->dayBounds($day);
        $sessionDate = $start->toDateString();

        $sessions = StaffMatterSession::query()
            ->with(['clientMatter', 'client'])
            ->where('staff_id', $staffId)
            ->whereDate('session_date', $sessionDate)
            ->orderByDesc('started_at')
            ->get();

        $auto = [];
        $opened = [];
        $eventMinutes = [];

        $needsEvents = $sessions->contains(
            fn (StaffMatterSession $session): bool => $session->status !== StaffMatterSession::STATUS_ACCESSED
                && $session->isRecordedForBoard()
        );
        if ($dayEvents === null && $needsEvents) {
            $dayEvents = $this->crmEvents->eventsForStaffWindow($staffId, $start, $end, true);
        }
        $dayEvents ??= collect();

        foreach ($sessions as $session) {
            $ref = $this->recordRef($session);

            if ($session->status === StaffMatterSession::STATUS_ACCESSED) {
                $opened[] = [
                    'id' => $session->id,
                    'ref' => $ref,
                    'url' => $this->recordUrl($session),
                    'client_id' => $session->client_id,
                    'client_matter_id' => $session->client_matter_id,
                    'focused_seconds' => (int) $session->focused_seconds,
                    'minutes' => max(0, (int) round(((int) $session->focused_seconds) / 60)),
                    'is_current' => $this->isCurrentlyOpen($session),
                    'last_heartbeat_at' => optional($session->last_heartbeat_at)?->toIso8601String(),
                ];

                continue;
            }

            if (! $session->isRecordedForBoard()) {
                continue;
            }

            $minutes = $this->resolvedMinutes($session);
            [$windowStart, $windowEnd] = $this->sessionEventWindow($session);
            $events = $this->crmEvents->filterEventsForRecord(
                $dayEvents,
                (int) $session->client_id,
                $session->client_matter_id !== null ? (int) $session->client_matter_id : null,
                $windowStart,
                $windowEnd,
            );
            $splitEvents = $this->splitMinutesAcrossEvents($events, $minutes);

            foreach ($splitEvents as $event) {
                if (isset($event['key'], $event['minutes']) && (int) $event['minutes'] > 0) {
                    $eventMinutes[(string) $event['key']] = (int) $event['minutes'];
                }
            }

            $auto[] = [
                'id' => $session->id,
                'ref' => $ref,
                'url' => $this->recordUrl($session),
                'client_id' => $session->client_id,
                'client_matter_id' => $session->client_matter_id,
                'status' => $session->status,
                'confirmed_minutes' => $minutes,
                'event_count' => (int) ($session->event_count ?? $events->count()),
                'is_reviewed_only' => (bool) $session->is_reviewed_only,
                'posted' => $session->activities_log_id !== null,
                'activities_log_id' => $session->activities_log_id,
                'events' => $splitEvents,
            ];
        }

        return [
            'auto' => $auto,
            'opened' => $opened,
            'event_minutes' => $eventMinutes,
        ];
    }

    public function postToFeed(StaffMatterSession $session): ActivitiesLog
    {
        $ref = $this->recordRef($session);
        $minutes = $this->resolvedMinutes($session);
        $events = $this->eventsForSession($session);
        $eventCount = (int) ($session->event_count ?? $events->count());

        if ($session->is_reviewed_only && $eventCount < 1) {
            $subject = "logged {$minutes}m on {$ref} · reviewed file";
        } else {
            $subject = "logged {$minutes}m on {$ref} · {$eventCount} activities";
        }

        $useFor = $session->client_matter_id ? 'matter' : null;

        $payload = [
            'client_id' => $session->client_id,
            'created_by' => $session->staff_id,
            'subject' => $subject,
            'description' => $subject,
            'activity_type' => StaffMatterSession::ACTIVITY_TYPE,
            'task_status' => 0,
            'pin' => 0,
        ];

        if ($useFor !== null && Schema::hasColumn('activities_logs', 'use_for')) {
            $payload['use_for'] = $useFor;
        }

        if ($session->activities_log_id) {
            $existing = ActivitiesLog::query()->find($session->activities_log_id);
            if ($existing) {
                $existing->fill($payload);
                $existing->save();

                return $existing;
            }
        }

        $log = ActivitiesLog::query()->create($payload);
        $session->activities_log_id = $log->id;
        $session->save();

        return $log;
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function eventsForSession(StaffMatterSession $session): Collection
    {
        [$start, $end] = $this->sessionEventWindow($session);

        return $this->crmEvents->forStaffOnRecord(
            (int) $session->staff_id,
            (int) $session->client_id,
            $session->client_matter_id !== null ? (int) $session->client_matter_id : null,
            $start,
            $end,
        );
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    protected function sessionEventWindow(StaffMatterSession $session): array
    {
        $start = $session->started_at ?? Carbon::parse($session->session_date)->startOfDay();
        $end = $session->last_heartbeat_at ?? now();

        return [$start, $end];
    }

    protected function closeSession(StaffMatterSession $session, Carbon $endedAt): void
    {
        $session = $this->promoteIfWritten($session->fresh());
        $wasRecorded = $session->status === StaffMatterSession::STATUS_RECORDED;

        $events = $this->eventsForSession($session);
        $session->ended_at = $endedAt;
        $session->status = StaffMatterSession::STATUS_CLOSED;

        if ($session->confirmed_minutes === null) {
            $minutes = max(0, (int) round(((int) $session->focused_seconds) / 60));
            $session->confirmed_minutes = ($wasRecorded && $minutes < 1) ? 1 : $minutes;
        }

        if ($wasRecorded) {
            $session->event_count = $session->is_reviewed_only
                ? max(1, (int) ($session->event_count ?? 1))
                : max($events->count(), 1);
        } else {
            $session->event_count = 0;
        }

        $session->save();

        if ($wasRecorded && (int) $session->confirmed_minutes >= 1) {
            $this->postToFeed($session->fresh());
        }
    }

    protected function findOrCreateToday(int $staffId, int $clientId, ?int $matterId): StaffMatterSession
    {
        [$start] = $this->workloadService->dayBounds(null);
        $sessionDate = $start->toDateString();
        $matterKey = (int) ($matterId ?? 0);

        $lastException = null;

        for ($attempt = 0; $attempt < 2; $attempt++) {
            try {
                return DB::transaction(function () use ($staffId, $clientId, $matterId, $sessionDate, $matterKey): StaffMatterSession {
                    $existing = StaffMatterSession::query()
                        ->where('staff_id', $staffId)
                        ->where('client_id', $clientId)
                        ->whereDate('session_date', $sessionDate)
                        ->where('matter_key', $matterKey)
                        ->lockForUpdate()
                        ->first();

                    if ($existing) {
                        return $existing;
                    }

                    $now = now();

                    return StaffMatterSession::query()->create([
                        'staff_id' => $staffId,
                        'client_id' => $clientId,
                        'client_matter_id' => $matterId,
                        'matter_key' => $matterKey,
                        'session_date' => $sessionDate,
                        'status' => StaffMatterSession::STATUS_ACCESSED,
                        'focused_seconds' => 0,
                        'idle_cut_seconds' => 0,
                        'started_at' => $now,
                        'last_heartbeat_at' => $now,
                    ]);
                });
            } catch (UniqueConstraintViolationException $e) {
                $lastException = $e;
            }
        }

        throw $lastException ?? new \RuntimeException('Failed to find or create staff matter session.');
    }

    public function isCurrentlyOpen(StaffMatterSession $session, ?Carbon $now = null): bool
    {
        if ($session->status === StaffMatterSession::STATUS_CLOSED) {
            return false;
        }

        $heartbeat = $session->last_heartbeat_at;
        if ($heartbeat === null) {
            return false;
        }

        $now = $now ?? now();

        return $heartbeat->gte($now->copy()->subSeconds(self::HEARTBEAT_STALE_SECONDS));
    }

    protected function applyFocusedSeconds(StaffMatterSession $session, int $focusedSeconds): void
    {
        $focusedSeconds = max(0, min(86400, $focusedSeconds));
        $session->focused_seconds = max((int) $session->focused_seconds, $focusedSeconds);
    }

    protected function reopenIfClosed(StaffMatterSession $session): void
    {
        if ($session->status !== StaffMatterSession::STATUS_CLOSED) {
            return;
        }

        $session->status = $session->isRecordedForBoard()
            ? StaffMatterSession::STATUS_RECORDED
            : StaffMatterSession::STATUS_ACCESSED;
        $session->ended_at = null;
    }

    protected function assertOwned(int $staffId, StaffMatterSession $session): void
    {
        if ((int) $session->staff_id !== $staffId) {
            throw ValidationException::withMessages(['session' => 'Not found.']);
        }
    }

    protected function recordRef(StaffMatterSession $session): string
    {
        $client = $session->client;
        $code = $client ? trim((string) ($client->client_id ?? '')) : '';
        $matterRef = trim((string) ($session->clientMatter?->client_unique_matter_no ?? ''));

        // Match client detail sidebar: {client_id}-{client_unique_matter_no}
        if ($code !== '' && $matterRef !== '') {
            return $code.'-'.$matterRef;
        }

        if ($matterRef !== '') {
            return $matterRef;
        }

        if ($code !== '') {
            return $code;
        }

        $name = $client ? trim(($client->first_name ?? '').' '.($client->last_name ?? '')) : '';

        return $name !== '' ? $name : 'Record #'.$session->client_id;
    }

    protected function recordUrl(StaffMatterSession $session): ?string
    {
        $clientId = $session->client_id !== null ? (int) $session->client_id : 0;
        if ($clientId < 1) {
            return null;
        }

        $encoded = base64_encode(convert_uuencode((string) $clientId));
        $matterRef = trim((string) ($session->clientMatter?->client_unique_matter_no ?? ''));

        if ($matterRef !== '') {
            return route('clients.detail', [$encoded, $matterRef, 'activityfeed']);
        }

        return route('clients.detail', [$encoded, 'activityfeed']);
    }

    /**
     * Confirmed minutes when set; otherwise focused seconds rounded to whole minutes.
     * Recorded sessions use a 1m floor so short CRM work never shows 0m, while longer
     * focus still reports the real rounded duration (2m, 5m, …).
     */
    protected function resolvedMinutes(StaffMatterSession $session): int
    {
        if ($session->confirmed_minutes !== null) {
            $minutes = max(0, (int) $session->confirmed_minutes);
        } else {
            $minutes = max(0, (int) round(((int) $session->focused_seconds) / 60));
        }

        if ($session->isRecordedForBoard() && $minutes < 1) {
            return 1;
        }

        return $minutes;
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $events
     * @return list<array<string, mixed>>
     */
    protected function splitMinutesAcrossEvents(Collection $events, int $minutes): array
    {
        $sorted = $events->sortBy([
            ['sort_at', 'asc'],
            ['key', 'asc'],
        ])->values();

        if ($sorted->isEmpty()) {
            if ($minutes > 0) {
                return [[
                    'key' => 'reviewed:0',
                    'kind' => 'Reviewed file',
                    'title' => 'Reviewed file',
                    'minutes' => $minutes,
                ]];
            }

            return [];
        }

        if ($minutes < 1) {
            $result = [];
            foreach ($sorted as $event) {
                $row = $event;
                unset($row['sort_at'], $row['client_id'], $row['client_matter_id']);
                $row['minutes'] = 0;
                $result[] = $row;
            }

            return $result;
        }

        $count = $sorted->count();
        $base = intdiv($minutes, $count);
        $remainder = $minutes % $count;
        $result = [];

        foreach ($sorted as $index => $event) {
            $eventMinutes = $base + ($index === 0 ? $remainder : 0);
            $row = $event;
            unset($row['sort_at'], $row['client_id'], $row['client_matter_id']);
            $row['minutes'] = $eventMinutes;
            $result[] = $row;
        }

        return $result;
    }
}
