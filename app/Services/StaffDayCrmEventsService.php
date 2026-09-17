<?php

namespace App\Services;

use App\Models\ActivitiesLog;
use App\Models\Admin;
use App\Models\BookingAppointment;
use App\Models\ClientMatter;
use App\Models\Document;
use App\Models\EmailLog;
use App\Models\Note;
use App\Models\SmsLog;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class StaffDayCrmEventsService
{
    public const LIST_CAP = 50;

    /** @var array<int, string|null> */
    protected array $clientLabelCache = [];

    /** @var array<int, string|null> */
    protected array $matterNoCache = [];

    public function __construct(
        protected StaffWorkloadService $workloadService,
    ) {}

    /**
     * Read-only union of today's CRM writes by this staff. Does not write.
     *
     * @return array{items: list<array<string, mixed>>, total: int, more: int, date: string}
     */
    public function forStaff(int $staffId, ?Carbon $day = null, int $limit = self::LIST_CAP): array
    {
        [$start, $end] = $this->workloadService->dayBounds($day);
        $items = collect()
            ->merge($this->emailEvents($staffId, $start, $end))
            ->merge($this->documentEvents($staffId, $start, $end))
            ->merge($this->bookingEvents($staffId, $start, $end))
            ->merge($this->smsEvents($staffId, $start, $end))
            ->merge($this->feedEvents($staffId, $start, $end))
            ->merge($this->contactNoteEvents($staffId, $start, $end))
            ->sortByDesc(fn (array $row) => $row['sort_at'])
            ->values();

        $total = $items->count();
        $sliced = $items->take($limit)->map(function (array $row): array {
            unset($row['sort_at']);

            return $row;
        })->all();

        return [
            'items' => $sliced,
            'total' => $total,
            'more' => max(0, $total - count($sliced)),
            'date' => $start->toDateString(),
        ];
    }

    /**
     * CRM writes by this staff on one record within a time window (auto session promotion).
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function forStaffOnRecord(
        int $staffId,
        int $clientId,
        ?int $clientMatterId,
        Carbon $start,
        Carbon $end,
    ): Collection {
        $items = collect()
            ->merge($this->emailEvents($staffId, $start, $end))
            ->merge($this->documentEvents($staffId, $start, $end))
            ->merge($this->bookingEvents($staffId, $start, $end))
            ->merge($this->smsEvents($staffId, $start, $end))
            ->merge($this->feedEvents($staffId, $start, $end))
            ->merge($this->contactNoteEvents($staffId, $start, $end))
            ->filter(function (array $row) use ($clientId, $clientMatterId): bool {
                if ((int) ($row['client_id'] ?? 0) !== $clientId) {
                    return false;
                }

                if ($clientMatterId === null) {
                    return true;
                }

                $rowMatter = $row['client_matter_id'] ?? null;
                if ($rowMatter === null || $rowMatter === '') {
                    return true;
                }

                return (int) $rowMatter === $clientMatterId;
            })
            ->sortBy(fn (array $row) => $row['sort_at'])
            ->values();

        return $items;
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    protected function emailEvents(int $staffId, Carbon $start, Carbon $end): Collection
    {
        if (! Schema::hasTable('email_logs')) {
            return collect();
        }

        return EmailLog::query()
            ->where('user_id', $staffId)
            ->whereBetween('created_at', [$start, $end])
            ->where(function ($q) {
                $q->whereNull('conversion_type')
                    ->orWhere(function ($sub) {
                        $sub->where('conversion_type', '!=', 'system_generated');
                    });
            })
            ->where(function ($q) {
                // Exclude fetched inbound per efficiency spec.
                $q->whereNull('conversion_type')
                    ->orWhere('conversion_type', '!=', 'conversion_email_fetch')
                    ->orWhere(function ($sub) {
                        $sub->where('conversion_type', 'conversion_email_fetch')
                            ->where('mail_body_type', 'sent');
                    });
            })
            ->orderByDesc('created_at')
            ->limit(80)
            ->get(array_values(array_filter([
                'id',
                'subject',
                'mail_body_type',
                'conversion_type',
                Schema::hasColumn('email_logs', 'client_id') ? 'client_id' : null,
                Schema::hasColumn('email_logs', 'client_matter_id') ? 'client_matter_id' : null,
                'created_at',
            ])))
            ->map(function (EmailLog $log): array {
                $isSent = ($log->mail_body_type ?? '') === 'sent';
                $kind = $isSent ? 'Email out' : 'Email';
                $attrs = $log->getAttributes();
                $clientId = array_key_exists('client_id', $attrs) && $log->client_id !== null
                    ? (int) $log->client_id
                    : null;
                $matterId = array_key_exists('client_matter_id', $attrs)
                    && $log->client_matter_id !== null
                    && is_numeric($log->client_matter_id)
                    ? (int) $log->client_matter_id
                    : null;

                return $this->row(
                    $kind,
                    (string) ($log->subject ?: 'Email'),
                    $log->created_at,
                    $this->personOrMatterRef($clientId, $matterId),
                    'email_log:'.$log->id,
                    $clientId,
                    $matterId,
                );
            });
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    protected function documentEvents(int $staffId, Carbon $start, Carbon $end): Collection
    {
        if (! Schema::hasTable('documents')) {
            return collect();
        }

        return Document::query()
            ->where(function ($q) use ($staffId) {
                $q->where('created_by', $staffId)->orWhere('user_id', $staffId);
            })
            ->whereBetween('created_at', [$start, $end])
            ->orderByDesc('created_at')
            ->limit(80)
            ->get(array_values(array_filter([
                'id',
                Schema::hasColumn('documents', 'file_name') ? 'file_name' : null,
                Schema::hasColumn('documents', 'name') ? 'name' : null,
                Schema::hasColumn('documents', 'doc_name') ? 'doc_name' : null,
                Schema::hasColumn('documents', 'client_id') ? 'client_id' : null,
                Schema::hasColumn('documents', 'client_matter_id') ? 'client_matter_id' : null,
                'created_at',
            ])))
            ->map(function (Document $doc): array {
                $title = (string) ($doc->file_name ?: $doc->name ?: $doc->doc_name ?: 'Document');
                $attrs = $doc->getAttributes();
                $matterId = array_key_exists('client_matter_id', $attrs)
                    && $doc->client_matter_id !== null
                    && is_numeric($doc->client_matter_id)
                    ? (int) $doc->client_matter_id
                    : null;
                $clientId = array_key_exists('client_id', $attrs) && $doc->client_id !== null
                    ? (int) $doc->client_id
                    : null;

                return $this->row(
                    'Document',
                    $title,
                    $doc->created_at,
                    $this->personOrMatterRef($clientId, $matterId),
                    'document:'.$doc->id,
                    $clientId,
                    $matterId,
                );
            });
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    protected function bookingEvents(int $staffId, Carbon $start, Carbon $end): Collection
    {
        if (! Schema::hasTable('booking_appointments')) {
            return collect();
        }

        return BookingAppointment::query()
            ->where('user_id', $staffId)
            ->whereBetween('created_at', [$start, $end])
            ->orderByDesc('created_at')
            ->limit(40)
            ->get(['id', 'client_id', 'client_name', 'meeting_type', 'service_type', 'created_at'])
            ->map(function (BookingAppointment $appt): array {
                $who = trim((string) ($appt->client_name ?? ''));
                $type = (string) ($appt->meeting_type ?: $appt->service_type ?: 'Appointment');
                $title = $who !== '' ? "{$type} — {$who}" : $type;

                return $this->row(
                    'Booking',
                    $title,
                    $appt->created_at,
                    $this->personOrMatterRef(
                        $appt->client_id !== null ? (int) $appt->client_id : null,
                        null,
                    ),
                    'booking:'.$appt->id,
                    $appt->client_id !== null ? (int) $appt->client_id : null,
                    null,
                );
            });
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    protected function smsEvents(int $staffId, Carbon $start, Carbon $end): Collection
    {
        if (! Schema::hasTable('sms_logs')) {
            return collect();
        }

        $timeCol = Schema::hasColumn('sms_logs', 'sent_at') ? 'sent_at' : 'created_at';
        // Production schema uses message_content; keep message as a safe fallback.
        $bodyCol = Schema::hasColumn('sms_logs', 'message_content')
            ? 'message_content'
            : (Schema::hasColumn('sms_logs', 'message') ? 'message' : null);

        $columns = array_values(array_filter(['id', $bodyCol, $timeCol, 'client_id']));

        $logs = SmsLog::query()
            ->where('sender_id', $staffId)
            ->whereBetween($timeCol, [$start, $end])
            ->orderByDesc($timeCol)
            ->limit(40)
            ->get($columns);

        $activityIdsBySms = [];
        if (
            $logs->isNotEmpty()
            && Schema::hasTable('activities_logs')
            && Schema::hasColumn('activities_logs', 'sms_log_id')
        ) {
            $activityIdsBySms = ActivitiesLog::query()
                ->whereIn('sms_log_id', $logs->pluck('id')->all())
                ->orderBy('id')
                ->get(['id', 'sms_log_id'])
                ->groupBy(fn (ActivitiesLog $log): int => (int) $log->sms_log_id)
                ->map(fn (Collection $group): int => (int) $group->first()->id)
                ->all();
        }

        return $logs->map(function (SmsLog $sms) use ($timeCol, $bodyCol, $activityIdsBySms): array {
            $at = $sms->{$timeCol} ?? $sms->created_at ?? null;
            $body = $bodyCol !== null ? ($sms->{$bodyCol} ?? null) : null;
            $title = (string) (Str::limit((string) ($body ?? 'SMS'), 80));

            $clientId = Schema::hasColumn('sms_logs', 'client_id') && $sms->client_id !== null
                ? (int) $sms->client_id
                : null;

            $activityLogId = $activityIdsBySms[(int) $sms->id] ?? null;

            return $this->row(
                'SMS',
                $title,
                $at,
                $this->personOrMatterRef($clientId, null),
                'sms:'.$sms->id,
                $clientId,
                null,
                $activityLogId,
            );
        });
    }

    /**
     * Stage, completed/updated actions, EOI, email feed rows (excluding file_time).
     *
     * @return Collection<int, array<string, mixed>>
     */
    protected function feedEvents(int $staffId, Carbon $start, Carbon $end): Collection
    {
        if (! Schema::hasTable('activities_logs')) {
            return collect();
        }

        return ActivitiesLog::query()
            ->where('created_by', $staffId)
            ->whereBetween('created_at', [$start, $end])
            ->where(function ($q) {
                $q->where('activity_type', 'stage')
                    ->orWhere('activity_type', 'like', 'eoi_%')
                    ->orWhere('subject', 'like', 'completed action for%')
                    ->orWhere('subject', 'like', 'Updated action for%');
            })
            ->where(function ($q) {
                $q->whereNull('activity_type')
                    ->orWhere('activity_type', '!=', 'file_time');
            })
            ->orderByDesc('created_at')
            ->limit(80)
            ->get(array_values(array_filter([
                'id',
                'subject',
                'activity_type',
                'task_group',
                'client_id',
                Schema::hasColumn('activities_logs', 'client_matter_id') ? 'client_matter_id' : null,
                'created_at',
            ])))
            ->map(function (ActivitiesLog $log): array {
                $matterId = Schema::hasColumn('activities_logs', 'client_matter_id') && $log->client_matter_id !== null
                    ? (int) $log->client_matter_id
                    : null;

                $subject = (string) ($log->subject ?? '');
                $type = (string) ($log->activity_type ?? '');
                if (str_starts_with(strtolower($subject), 'completed action for')) {
                    $kind = 'Action completed';
                } elseif (str_starts_with(strtolower($subject), 'updated action for')) {
                    $kind = 'Action updated';
                } elseif ($type === 'stage') {
                    $kind = 'Stage';
                } elseif (str_starts_with($type, 'eoi_')) {
                    $kind = 'EOI';
                } else {
                    $kind = 'Activity';
                }

                return $this->row(
                    $kind,
                    $subject !== '' ? $subject : $kind,
                    $log->created_at,
                    $this->personOrMatterRef(
                        $log->client_id !== null ? (int) $log->client_id : null,
                        $matterId,
                    ),
                    'feed:'.$log->id,
                    $log->client_id !== null ? (int) $log->client_id : null,
                    $matterId,
                    (int) $log->id,
                );
            });
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    protected function contactNoteEvents(int $staffId, Carbon $start, Carbon $end): Collection
    {
        if (! Schema::hasTable('notes')) {
            return collect();
        }

        $columns = ['id', 'user_id', 'title', 'task_group', 'matter_id', 'client_id', 'created_at'];
        if (Schema::hasColumn('notes', 'description')) {
            $columns[] = 'description';
        }

        $notes = Note::query()
            ->where('user_id', $staffId)
            ->where('is_action', 0)
            ->whereNull('assigned_to')
            ->whereIn('type', ['client', 'lead'])
            ->whereBetween('created_at', [$start, $end])
            ->orderByDesc('created_at')
            ->limit(40)
            ->get($columns);

        $activityLogIds = $this->activityLogIdsForNotes($notes);

        return $notes->map(function (Note $note) use ($activityLogIds): array {
            $group = (string) ($note->task_group ?? '');
            $kind = $this->contactNoteKind($group);
            $title = (string) ($note->title ?: $kind);

            $clientId = $note->client_id !== null ? (int) $note->client_id : null;
            $matterId = $note->matter_id !== null && is_numeric($note->matter_id) ? (int) $note->matter_id : null;

            $row = $this->row(
                $kind,
                $title,
                $note->created_at,
                $this->personOrMatterRef($clientId, $matterId),
                'note:'.$note->id,
                $clientId,
                $matterId,
                $activityLogIds[(int) $note->id] ?? null,
            );

            $body = $this->noteBodyPlain($note->description ?? null);
            if ($body !== '') {
                $row['body'] = $body;
            }

            return $row;
        });
    }

    /**
     * Map contact note ids to matching activities_logs rows (created when the note was saved).
     *
     * @param  Collection<int, Note>  $notes
     * @return array<int, int>
     */
    protected function activityLogIdsForNotes(Collection $notes): array
    {
        if ($notes->isEmpty() || ! Schema::hasTable('activities_logs')) {
            return [];
        }

        $clientIds = $notes->pluck('client_id')
            ->filter(fn ($id): bool => $id !== null && (int) $id > 0)
            ->map(fn ($id): int => (int) $id)
            ->unique()
            ->values();

        if ($clientIds->isEmpty()) {
            return [];
        }

        $createdAts = $notes->pluck('created_at')->filter();
        if ($createdAts->isEmpty()) {
            return [];
        }

        $start = Carbon::parse($createdAts->min())->subMinutes(2);
        $end = Carbon::parse($createdAts->max())->addMinutes(2);

        $hasDescription = Schema::hasColumn('activities_logs', 'description');
        $columns = ['id', 'client_id', 'created_by', 'created_at'];
        if ($hasDescription) {
            $columns[] = 'description';
        }

        $userIds = $notes->pluck('user_id')
            ->filter(fn ($id): bool => $id !== null && (int) $id > 0)
            ->map(fn ($id): int => (int) $id)
            ->unique()
            ->values()
            ->all();

        $logsQuery = ActivitiesLog::query()
            ->whereIn('client_id', $clientIds->all())
            ->where('activity_type', 'note')
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('id');

        if ($userIds !== []) {
            $logsQuery->whereIn('created_by', $userIds);
        }

        $logs = $logsQuery->get($columns);
        if ($logs->isEmpty()) {
            return [];
        }

        $map = [];
        $usedLogIds = [];

        foreach ($notes as $note) {
            $noteId = (int) $note->id;
            $noteClientId = $note->client_id !== null ? (int) $note->client_id : null;
            $noteUserId = $note->user_id !== null ? (int) $note->user_id : null;
            if ($noteClientId === null || $noteClientId < 1) {
                continue;
            }

            $noteAt = Carbon::parse($note->created_at)->getTimestamp();
            $notePlain = $this->noteBodyPlain($note->description ?? null);

            $bestId = null;
            $bestScore = PHP_INT_MAX;

            foreach ($logs as $log) {
                $logId = (int) $log->id;
                if (isset($usedLogIds[$logId])) {
                    continue;
                }

                if ((int) $log->client_id !== $noteClientId) {
                    continue;
                }

                if ($noteUserId !== null && (int) $log->created_by !== $noteUserId) {
                    continue;
                }

                $diffSeconds = abs(Carbon::parse($log->created_at)->getTimestamp() - $noteAt);
                if ($diffSeconds > 120) {
                    continue;
                }

                $score = $diffSeconds;
                if ($hasDescription && $notePlain !== '') {
                    $logPlain = $this->noteBodyPlain((string) ($log->description ?? ''));
                    if ($logPlain !== '' && (str_contains($logPlain, $notePlain) || str_contains($notePlain, $logPlain))) {
                        $score -= 1000;
                    } elseif ($logPlain !== '') {
                        continue;
                    }
                }

                if ($score < $bestScore) {
                    $bestScore = $score;
                    $bestId = $logId;
                }
            }

            if ($bestId !== null) {
                $map[$noteId] = $bestId;
                $usedLogIds[$bestId] = true;
            }
        }

        return $map;
    }

    protected function contactNoteKind(string $taskGroup): string
    {
        if (stripos($taskGroup, 'person') !== false) {
            return 'In-person note';
        }

        return match (strtolower($taskGroup)) {
            'call' => 'Call note',
            'email' => 'Email note',
            'others' => 'Other note',
            'attention' => 'Attention note',
            '' => 'Note',
            default => $taskGroup.' note',
        };
    }

    protected function noteBodyPlain(?string $html): string
    {
        $plain = html_entity_decode(strip_tags((string) $html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $plain = preg_replace('/\s+/u', ' ', $plain) ?? '';

        return trim($plain);
    }

    /**
     * @return array<string, mixed>
     */
    protected function row(
        string $kind,
        string $title,
        mixed $at,
        ?string $ref,
        string $key,
        ?int $clientId = null,
        ?int $clientMatterId = null,
        ?int $activityLogId = null,
    ): array {
        $carbon = $at ? Carbon::parse($at)->timezone((string) config('app.timezone')) : now();

        return [
            'key' => $key,
            'kind' => $kind,
            'title' => $title,
            'ref' => $ref,
            'url' => $this->activityFeedUrl($clientId, $clientMatterId, $activityLogId),
            'time' => $carbon->format('g:i a'),
            'sort_at' => $carbon->timestamp,
            'client_id' => $clientId,
            'client_matter_id' => $clientMatterId,
        ];
    }

    protected function personOrMatterRef(?int $clientId, mixed $matterId): ?string
    {
        $matter = $this->matterNo($matterId);
        $client = $this->clientOrLeadRef($clientId);

        // Match client detail sidebar: {client_id}-{client_unique_matter_no}
        if ($client !== null && $client !== '' && $matter !== null && $matter !== '') {
            return $client.'-'.$matter;
        }

        if ($matter !== null && $matter !== '') {
            return $matter;
        }

        return $client;
    }

    /**
     * Deep-link to the client Activity tab, optionally focusing a specific feed row.
     */
    protected function activityFeedUrl(?int $clientId, mixed $matterId, ?int $activityLogId = null): ?string
    {
        if ($clientId === null || $clientId < 1) {
            return null;
        }

        $encoded = base64_encode(convert_uuencode((string) $clientId));
        $matterNo = $this->matterNo($matterId);

        if ($matterNo !== null && $matterNo !== '') {
            $url = route('clients.detail', [$encoded, $matterNo, 'activityfeed']);
        } else {
            $url = route('clients.detail', [$encoded, 'activityfeed']);
        }

        if ($activityLogId !== null && $activityLogId > 0) {
            return $url.'#activity_'.$activityLogId;
        }

        return $url;
    }

    protected function notesRecordUrl(?int $clientId, mixed $matterId, ?int $activityLogId = null): ?string
    {
        return $this->activityFeedUrl($clientId, $matterId, $activityLogId);
    }

    protected function recordUrl(?int $clientId, mixed $matterId): ?string
    {
        return $this->activityFeedUrl($clientId, $matterId);
    }

    protected function clientOrLeadRef(?int $clientId): ?string
    {
        if ($clientId === null || $clientId < 1 || ! Schema::hasTable('admins')) {
            return null;
        }

        if (! array_key_exists($clientId, $this->clientLabelCache)) {
            $admin = Admin::query()->find($clientId, ['id', 'client_id', 'first_name', 'last_name']);
            if (! $admin) {
                $this->clientLabelCache[$clientId] = null;
            } else {
                $code = trim((string) ($admin->client_id ?? ''));
                if ($code !== '') {
                    $this->clientLabelCache[$clientId] = $code;
                } else {
                    // Prefer unique CRM codes; name is only a last-resort fallback.
                    $name = trim((string) ($admin->first_name ?? '').' '.($admin->last_name ?? ''));
                    $this->clientLabelCache[$clientId] = $name !== '' ? $name : ('Record #'.$clientId);
                }
            }
        }

        return $this->clientLabelCache[$clientId];
    }

    protected function matterNo(mixed $matterId): ?string
    {
        if ($matterId === null || $matterId === '') {
            return null;
        }

        // documents.client_matter_id is historically a varchar; prefer numeric id lookup.
        if (! is_numeric($matterId)) {
            return (string) $matterId;
        }

        $id = (int) $matterId;
        if ($id < 1) {
            return null;
        }

        if (! array_key_exists($id, $this->matterNoCache)) {
            $this->matterNoCache[$id] = ClientMatter::query()
                ->where('id', $id)
                ->value('client_unique_matter_no');
        }

        return $this->matterNoCache[$id] ? (string) $this->matterNoCache[$id] : null;
    }
}
