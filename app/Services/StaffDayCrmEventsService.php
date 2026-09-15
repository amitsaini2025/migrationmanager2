<?php

namespace App\Services;

use App\Models\ActivitiesLog;
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
            ->get(['id', 'subject', 'mail_body_type', 'conversion_type', 'client_matter_id', 'created_at'])
            ->map(function (EmailLog $log): array {
                $isSent = ($log->mail_body_type ?? '') === 'sent';
                $kind = $isSent ? 'Email out' : 'Email';

                return $this->row(
                    $kind,
                    (string) ($log->subject ?: 'Email'),
                    $log->created_at,
                    $this->matterNo($log->client_matter_id),
                    'email_log:'.$log->id,
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
            ->get(['id', 'file_name', 'client_matter_id', 'created_at'])
            ->map(function (Document $doc): array {
                $title = (string) ($doc->file_name ?: 'Document');

                return $this->row(
                    'Document',
                    $title,
                    $doc->created_at,
                    $this->matterNo($doc->client_matter_id),
                    'document:'.$doc->id,
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
                    null,
                    'booking:'.$appt->id,
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

        $columns = array_values(array_filter(['id', $bodyCol, $timeCol]));

        return SmsLog::query()
            ->where('sender_id', $staffId)
            ->whereBetween($timeCol, [$start, $end])
            ->orderByDesc($timeCol)
            ->limit(40)
            ->get($columns)
            ->map(function (SmsLog $sms) use ($timeCol, $bodyCol): array {
                $at = $sms->{$timeCol} ?? $sms->created_at ?? null;
                $body = $bodyCol !== null ? ($sms->{$bodyCol} ?? null) : null;
                $title = (string) (Str::limit((string) ($body ?? 'SMS'), 80));

                return $this->row('SMS', $title, $at, null, 'sms:'.$sms->id);
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
            ->get(['id', 'subject', 'activity_type', 'task_group', 'created_at'])
            ->map(function (ActivitiesLog $log): array {
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

                return $this->row($kind, $subject !== '' ? $subject : $kind, $log->created_at, null, 'feed:'.$log->id);
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

        return Note::query()
            ->where('user_id', $staffId)
            ->where('is_action', 0)
            ->whereNull('assigned_to')
            ->whereIn('type', ['client', 'lead'])
            ->whereIn('task_group', ['Call', 'In-Person'])
            ->whereBetween('created_at', [$start, $end])
            ->orderByDesc('created_at')
            ->limit(40)
            ->get(['id', 'title', 'task_group', 'matter_id', 'created_at'])
            ->map(function (Note $note): array {
                $group = (string) ($note->task_group ?? '');
                $kind = stripos($group, 'person') !== false ? 'In-person note' : 'Call note';
                $title = (string) ($note->title ?: $kind);

                return $this->row(
                    $kind,
                    $title,
                    $note->created_at,
                    $this->matterNo($note->matter_id ?? null),
                    'note:'.$note->id,
                );
            });
    }

    /**
     * @return array<string, mixed>
     */
    protected function row(string $kind, string $title, mixed $at, ?string $ref, string $key): array
    {
        $carbon = $at ? Carbon::parse($at)->timezone((string) config('app.timezone')) : now();

        return [
            'key' => $key,
            'kind' => $kind,
            'title' => $title,
            'ref' => $ref,
            'time' => $carbon->format('g:i a'),
            'sort_at' => $carbon->timestamp,
        ];
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

        static $cache = [];
        if (! array_key_exists($id, $cache)) {
            $cache[$id] = ClientMatter::query()
                ->where('id', $id)
                ->value('client_unique_matter_no');
        }

        return $cache[$id] ? (string) $cache[$id] : null;
    }
}
