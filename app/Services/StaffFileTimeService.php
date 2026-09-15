<?php

namespace App\Services;

use App\Models\ActivitiesLog;
use App\Models\ClientMatter;
use App\Models\Staff;
use App\Models\StaffFileTimeEntry;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StaffFileTimeService
{
    public function __construct(
        protected StaffWorkloadService $workloadService,
    ) {}

    /**
     * @return array{
     *     entries: list<array<string, mixed>>,
     *     tally: array<string, mixed>,
     *     by_matter: list<array<string, mixed>>,
     *     date: string,
     *     timezone: string
     * }
     */
    public function boardForStaff(int $staffId, ?Carbon $day = null): array
    {
        [$start, $end] = $this->workloadService->dayBounds($day);
        $entries = StaffFileTimeEntry::query()
            ->with(['clientMatter:id,client_id,client_unique_matter_no,sel_matter_id'])
            ->where('staff_id', $staffId)
            ->whereBetween('created_at', [$start, $end])
            ->orderByDesc('updated_at')
            ->get();

        $mapped = $entries->map(fn (StaffFileTimeEntry $entry): array => $this->serialize($entry))->all();

        return [
            'entries' => $mapped,
            'tally' => $this->tally($entries),
            'by_matter' => $this->byMatter($entries),
            'date' => $start->toDateString(),
            'timezone' => (string) config('app.timezone'),
        ];
    }

    /**
     * @param  array{
     *     kind: string,
     *     title: string,
     *     client_matter_id?: int|null,
     *     admin?: bool
     * }  $data
     */
    public function start(int $staffId, array $data): StaffFileTimeEntry
    {
        $isAdmin = filter_var($data['admin'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $matterId = isset($data['client_matter_id']) ? (int) $data['client_matter_id'] : 0;
        $matterId = $matterId > 0 ? $matterId : null;

        if (! $isAdmin && $matterId === null) {
            throw ValidationException::withMessages([
                'client_matter_id' => 'Choose a matter or Admin / no file.',
            ]);
        }

        if ($isAdmin) {
            $matterId = null;
            $clientId = null;
        } else {
            $matter = ClientMatter::query()->find($matterId);
            if (! $matter) {
                throw ValidationException::withMessages([
                    'client_matter_id' => 'Matter not found.',
                ]);
            }
            $clientId = (int) $matter->client_id;
        }

        return DB::transaction(function () use ($staffId, $data, $matterId, $clientId) {
            $this->pauseAllRunning($staffId);

            return StaffFileTimeEntry::query()->create([
                'staff_id' => $staffId,
                'client_matter_id' => $matterId,
                'client_id' => $clientId,
                'kind' => $data['kind'],
                'title' => trim((string) $data['title']),
                'status' => StaffFileTimeEntry::STATUS_DOING,
                'is_running' => true,
                'clock_seconds' => 0,
                'started_at' => now(),
            ]);
        });
    }

    public function pause(int $staffId, StaffFileTimeEntry $entry, ?int $clockSeconds = null): StaffFileTimeEntry
    {
        $this->assertOwnedOpen($staffId, $entry);

        $entry->clock_seconds = $clockSeconds ?? $this->elapsedSeconds($entry);
        $entry->is_running = false;
        $entry->status = StaffFileTimeEntry::STATUS_DOING;
        $entry->save();

        return $entry->fresh();
    }

    public function park(int $staffId, StaffFileTimeEntry $entry, ?int $clockSeconds = null): StaffFileTimeEntry
    {
        $this->assertOwnedOpen($staffId, $entry);

        $entry->clock_seconds = $clockSeconds ?? $this->elapsedSeconds($entry);
        $entry->is_running = false;
        $entry->status = StaffFileTimeEntry::STATUS_PARKED;
        $entry->save();

        return $entry->fresh();
    }

    public function resume(int $staffId, StaffFileTimeEntry $entry): StaffFileTimeEntry
    {
        $this->assertOwnedOpen($staffId, $entry);

        return DB::transaction(function () use ($staffId, $entry) {
            $this->pauseAllRunning($staffId, $entry->id);

            $entry->status = StaffFileTimeEntry::STATUS_DOING;
            $entry->is_running = true;
            $entry->started_at = now();
            $entry->save();

            return $entry->fresh();
        });
    }

    public function done(int $staffId, StaffFileTimeEntry $entry, int $confirmedMinutes, ?int $clockSeconds = null): StaffFileTimeEntry
    {
        $this->assertOwnedOpen($staffId, $entry);

        if ($confirmedMinutes < 1 || $confirmedMinutes > 480) {
            throw ValidationException::withMessages([
                'confirmed_minutes' => 'Confirmed minutes must be between 1 and 480.',
            ]);
        }

        return DB::transaction(function () use ($staffId, $entry, $confirmedMinutes, $clockSeconds) {
            $entry->clock_seconds = $clockSeconds ?? $this->elapsedSeconds($entry);
            $entry->confirmed_minutes = $confirmedMinutes;
            $entry->status = StaffFileTimeEntry::STATUS_DONE;
            $entry->is_running = false;
            $entry->completed_at = now();

            if ($entry->client_matter_id && $entry->client_id) {
                $log = $this->postToMatterFeed($entry, $staffId);
                $entry->activities_log_id = $log->id;
            }

            $entry->save();

            return $entry->fresh(['clientMatter']);
        });
    }

    public function reopen(int $staffId, StaffFileTimeEntry $entry): StaffFileTimeEntry
    {
        if ((int) $entry->staff_id !== $staffId) {
            throw ValidationException::withMessages(['entry' => 'Not found.']);
        }

        if ($entry->status !== StaffFileTimeEntry::STATUS_DONE) {
            throw ValidationException::withMessages(['entry' => 'Only done entries can be reopened.']);
        }

        [$start, $end] = $this->workloadService->dayBounds();
        if ($entry->completed_at === null
            || $entry->completed_at->lt($start)
            || $entry->completed_at->gt($end)) {
            throw ValidationException::withMessages(['entry' => 'Reopen is only allowed same day.']);
        }

        return DB::transaction(function () use ($staffId, $entry) {
            $this->pauseAllRunning($staffId, $entry->id);

            $fromConfirmed = $entry->confirmed_minutes !== null
                ? ((int) $entry->confirmed_minutes) * 60
                : 0;
            $entry->clock_seconds = max((int) $entry->clock_seconds, $fromConfirmed);
            $entry->status = StaffFileTimeEntry::STATUS_DOING;
            $entry->confirmed_minutes = null;
            $entry->completed_at = null;
            $entry->is_running = true;
            $entry->started_at = now();
            // Keep activities_log_id; Done updates that feed row. Start a new block for a separate day entry.
            $entry->save();

            return $entry->fresh();
        });
    }

    /**
     * @param  array{title?: string, client_matter_id?: int|null, admin?: bool}  $data
     */
    public function updateOpen(int $staffId, StaffFileTimeEntry $entry, array $data): StaffFileTimeEntry
    {
        $this->assertOwnedOpen($staffId, $entry);

        if (array_key_exists('title', $data) && trim((string) $data['title']) !== '') {
            $entry->title = trim((string) $data['title']);
        }

        if (filter_var($data['admin'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
            $entry->client_matter_id = null;
            $entry->client_id = null;
        } elseif (array_key_exists('client_matter_id', $data) && $data['client_matter_id']) {
            $matter = ClientMatter::query()->find((int) $data['client_matter_id']);
            if (! $matter) {
                throw ValidationException::withMessages([
                    'client_matter_id' => 'Matter not found.',
                ]);
            }
            $entry->client_matter_id = $matter->id;
            $entry->client_id = (int) $matter->client_id;
        }

        $entry->save();

        return $entry->fresh(['clientMatter']);
    }

    public function deleteOpen(int $staffId, StaffFileTimeEntry $entry): void
    {
        if ((int) $entry->staff_id !== $staffId) {
            throw ValidationException::withMessages(['entry' => 'Not found.']);
        }

        if ($entry->status === StaffFileTimeEntry::STATUS_DONE || $entry->isPosted()) {
            throw ValidationException::withMessages([
                'entry' => 'Done or posted entries cannot be deleted.',
            ]);
        }

        $entry->delete();
    }

    /**
     * @return array{
     *     hours_label: string,
     *     crm_events: list<array<string, mixed>>,
     *     overlay: list<array<string, mixed>>,
     *     admin: list<array<string, mixed>>,
     *     auto: list<string>,
     *     opened: list<string>,
     *     still_open: list<array<string, mixed>>,
     *     text: string
     * }
     */
    public function copySummary(
        int $staffId,
        StaffDayCrmEventsService $crmEvents,
        StaffDayHoursService $hours,
        ?StaffMatterSessionService $matterSessions = null,
        ?Carbon $day = null,
    ): array {
        $staff = Staff::query()->find($staffId);
        $name = $staff
            ? trim(($staff->first_name ?? '').' '.($staff->last_name ?? ''))
            : 'Staff';
        [$start] = $this->workloadService->dayBounds($day);
        $dateLabel = $start->format('l, j M Y');

        $hoursPayload = $hours->forStaff($staffId, $day);
        $events = $crmEvents->forStaff($staffId, $day);
        $board = $this->boardForStaff($staffId, $day);

        $sessionsPayload = ['auto' => [], 'opened' => [], 'event_minutes' => []];
        if ($matterSessions !== null) {
            $sessionsPayload = $matterSessions->sessionsForBoard($staffId, $day);
        }
        $eventMinutes = $sessionsPayload['event_minutes'] ?? [];

        $overlayDone = [];
        $adminDone = [];
        $stillOpen = [];
        foreach ($board['entries'] as $entry) {
            if ($entry['status'] === StaffFileTimeEntry::STATUS_DONE) {
                $line = [
                    'ref' => $entry['matter_no'] ?? 'Admin',
                    'kind' => $entry['kind'],
                    'title' => $entry['title'],
                    'minutes' => $entry['confirmed_minutes'],
                ];
                if ($entry['is_admin']) {
                    $adminDone[] = $line;
                } else {
                    $overlayDone[] = $line;
                }
            } else {
                $stillOpen[] = [
                    'ref' => $entry['matter_no'] ?? 'Admin',
                    'kind' => $entry['kind'],
                    'title' => $entry['title'],
                    'status' => $entry['status'],
                ];
            }
        }

        $lines = [
            "{$name} — {$dateLabel}",
            'Hours in CRM: '.($hoursPayload['label'] ?? '—'),
            '',
            '— Already in CRM —',
        ];

        if ($events['items'] === []) {
            $lines[] = '(none)';
        } else {
            foreach ($events['items'] as $item) {
                $ref = $item['ref'] ?: '—';
                $line = "{$ref} · {$item['kind']} · {$item['title']} · {$item['time']}";
                $mins = $eventMinutes[$item['key'] ?? ''] ?? null;
                if ($mins) {
                    $line .= " · {$mins}m";
                }
                $lines[] = $line;
            }
            if (($events['more'] ?? 0) > 0) {
                $lines[] = '… and '.$events['more'].' more';
            }
        }

        $lines[] = '';
        $lines[] = '— Time on files (overlay) —';
        if ($overlayDone === []) {
            $lines[] = '(none)';
        } else {
            foreach ($overlayDone as $item) {
                $lines[] = "{$item['ref']} · {$item['kind']} · {$item['title']} · {$item['minutes']}m";
            }
        }

        $lines[] = '';
        $lines[] = '— Admin / no file —';
        if ($adminDone === []) {
            $lines[] = '(none)';
        } else {
            foreach ($adminDone as $item) {
                $lines[] = "{$item['ref']} · {$item['kind']} · {$item['title']} · {$item['minutes']}m";
            }
        }

        $lines[] = '';
        $lines[] = '— Time on files (auto) —';
        $autoLines = [];
        $openedRefs = [];
        foreach ($sessionsPayload['auto'] as $row) {
            $eventLabel = ! empty($row['is_reviewed_only'])
                ? 'reviewed file'
                : (($row['event_count'] ?? 0).' activities');
            $autoLines[] = "{$row['ref']} · {$row['confirmed_minutes']}m · {$eventLabel}";
        }
        foreach ($sessionsPayload['opened'] as $row) {
            $mins = (int) ($row['minutes'] ?? max(0, (int) round(((int) ($row['focused_seconds'] ?? 0)) / 60)));
            $openedRefs[] = $mins > 0
                ? ((string) ($row['ref'] ?? '—'))." · {$mins}m (open)"
                : (string) ($row['ref'] ?? '—');
        }
        if ($autoLines === []) {
            $lines[] = '(none)';
        } else {
            array_push($lines, ...$autoLines);
        }

        $lines[] = '';
        $lines[] = '— Files opened —';
        if ($openedRefs === []) {
            $lines[] = '(none)';
        } else {
            array_push($lines, ...$openedRefs);
        }

        $lines[] = '';
        $lines[] = '— Still open —';
        if ($stillOpen === []) {
            $lines[] = '(none)';
        } else {
            foreach ($stillOpen as $item) {
                $lines[] = "{$item['ref']} · {$item['kind']} · {$item['title']} · {$item['status']}";
            }
        }

        return [
            'hours_label' => $hoursPayload['label'] ?? '—',
            'crm_events' => $events['items'],
            'overlay' => $overlayDone,
            'admin' => $adminDone,
            'auto' => $autoLines,
            'opened' => $openedRefs,
            'still_open' => $stillOpen,
            'text' => implode("\n", $lines),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function serialize(StaffFileTimeEntry $entry): array
    {
        $matterNo = $entry->clientMatter?->client_unique_matter_no;

        return [
            'id' => $entry->id,
            'kind' => $entry->kind,
            'kind_label' => $entry->kindLabel(),
            'title' => $entry->title,
            'status' => $entry->status,
            'is_running' => (bool) $entry->is_running,
            'clock_seconds' => (int) $entry->clock_seconds,
            'confirmed_minutes' => $entry->confirmed_minutes,
            'client_matter_id' => $entry->client_matter_id,
            'client_id' => $entry->client_id,
            'matter_no' => $matterNo,
            'is_admin' => $entry->isAdmin(),
            'posted' => $entry->status === StaffFileTimeEntry::STATUS_DONE && $entry->isPosted(),
            'activities_log_id' => $entry->activities_log_id,
            'started_at' => optional($entry->started_at)?->toIso8601String(),
            'completed_at' => optional($entry->completed_at)?->toIso8601String(),
            'updated_at' => optional($entry->updated_at)?->toIso8601String(),
        ];
    }

    protected function postToMatterFeed(StaffFileTimeEntry $entry, int $staffId): ActivitiesLog
    {
        $matter = ClientMatter::query()->find($entry->client_matter_id);
        $ref = $matter?->client_unique_matter_no ?? ('matter #'.$entry->client_matter_id);
        $minutes = (int) $entry->confirmed_minutes;
        $kindLabel = $entry->kindLabel();
        $payload = [
            'client_id' => $entry->client_id,
            'created_by' => $staffId,
            'subject' => "logged {$minutes}m {$kindLabel} on {$ref}",
            'description' => trim($entry->title.' · '.$kindLabel.' · '.$minutes.'m'),
            'activity_type' => StaffFileTimeEntry::ACTIVITY_TYPE,
            'use_for' => 'matter',
            'task_status' => 0,
            'pin' => 0,
        ];

        if ($entry->activities_log_id) {
            $existing = ActivitiesLog::query()->find($entry->activities_log_id);
            if ($existing) {
                $existing->fill($payload);
                $existing->save();

                return $existing;
            }
        }

        return ActivitiesLog::query()->create($payload);
    }

    protected function pauseAllRunning(int $staffId, ?int $exceptId = null): void
    {
        $query = StaffFileTimeEntry::query()
            ->where('staff_id', $staffId)
            ->where('is_running', true);

        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }

        $running = $query->get();
        foreach ($running as $entry) {
            $entry->clock_seconds = $this->elapsedSeconds($entry);
            $entry->is_running = false;
            if ($entry->status === StaffFileTimeEntry::STATUS_DOING) {
                // leave as doing (paused) so resume is clear
            }
            $entry->save();
        }
    }

    protected function elapsedSeconds(StaffFileTimeEntry $entry): int
    {
        $base = (int) $entry->clock_seconds;
        if (! $entry->is_running || $entry->started_at === null) {
            return $base;
        }

        return $base + max(0, now()->getTimestamp() - $entry->started_at->getTimestamp());
    }

    protected function assertOwnedOpen(int $staffId, StaffFileTimeEntry $entry): void
    {
        if ((int) $entry->staff_id !== $staffId) {
            throw ValidationException::withMessages(['entry' => 'Not found.']);
        }

        if ($entry->status === StaffFileTimeEntry::STATUS_DONE) {
            throw ValidationException::withMessages(['entry' => 'Entry is already done.']);
        }
    }

    /**
     * @param  Collection<int, StaffFileTimeEntry>  $entries
     * @return array<string, mixed>
     */
    protected function tally($entries): array
    {
        $confirmed = 0;
        $adminMinutes = 0;
        $open = 0;
        $matterIds = [];
        $byKind = [];

        foreach (StaffFileTimeEntry::kinds() as $kind) {
            $byKind[$kind] = 0;
        }

        foreach ($entries as $entry) {
            if ($entry->status === StaffFileTimeEntry::STATUS_DONE) {
                $mins = (int) ($entry->confirmed_minutes ?? 0);
                $confirmed += $mins;
                if ($entry->isAdmin()) {
                    $adminMinutes += $mins;
                } elseif ($entry->client_matter_id) {
                    $matterIds[$entry->client_matter_id] = true;
                }
                $byKind[$entry->kind] = ($byKind[$entry->kind] ?? 0) + $mins;
            } else {
                $open++;
            }
        }

        return [
            'confirmed_minutes' => $confirmed,
            'files_timed' => count($matterIds),
            'still_open' => $open,
            'admin_minutes' => $adminMinutes,
            'by_kind' => $byKind,
        ];
    }

    /**
     * @param  Collection<int, StaffFileTimeEntry>  $entries
     * @return list<array<string, mixed>>
     */
    protected function byMatter($entries): array
    {
        $groups = [];
        foreach ($entries as $entry) {
            if ($entry->status !== StaffFileTimeEntry::STATUS_DONE) {
                continue;
            }
            $key = $entry->isAdmin() ? 'admin' : (string) $entry->client_matter_id;
            if (! isset($groups[$key])) {
                $groups[$key] = [
                    'key' => $key,
                    'client_matter_id' => $entry->client_matter_id,
                    'matter_no' => $entry->isAdmin() ? 'Admin / no file' : ($entry->clientMatter?->client_unique_matter_no ?? '—'),
                    'is_admin' => $entry->isAdmin(),
                    'blocks' => 0,
                    'minutes' => 0,
                    'by_kind' => [],
                ];
            }
            $mins = (int) ($entry->confirmed_minutes ?? 0);
            $groups[$key]['blocks']++;
            $groups[$key]['minutes'] += $mins;
            $groups[$key]['by_kind'][$entry->kind] = ($groups[$key]['by_kind'][$entry->kind] ?? 0) + $mins;
        }

        $list = array_values($groups);
        usort($list, static fn (array $a, array $b): int => $b['minutes'] <=> $a['minutes']);

        return $list;
    }
}
