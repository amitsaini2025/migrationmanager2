<?php

namespace App\Services;

use App\Events\OfficeVisitNotificationCreated;
use App\Models\CheckinLog;
use App\Models\Notification;
use App\Models\Staff;
use App\Support\NotificationUrl;
use Illuminate\Support\Facades\Log;

class OfficeVisitNotificationService
{
    public const ACTION_CHECKED_IN = 'checked_in';

    public const ACTION_REASSIGNED = 'reassigned';

    public const ACTION_PLEASE_SEND = 'please_send';

    /**
     * One row per visit and receiver. A later nudge updates that row
     * instead of inserting another identical notification.
     */
    public function upsert(CheckinLog $visit, int $receiverId, string $action, Staff $sender): Notification
    {
        $actor = trim($sender->first_name.' '.$sender->last_name) ?: 'Staff';
        $message = $this->message($visit, $action, $actor);
        $url = $this->visitUrl($visit);

        $notification = Notification::query()
            ->where('notification_type', 'officevisit')
            ->where('module_id', $visit->id)
            ->where('receiver_id', $receiverId)
            ->orderByDesc('id')
            ->first();

        if (! $notification) {
            $notification = new Notification;
            $notification->notification_type = 'officevisit';
            $notification->module_id = $visit->id;
            $notification->receiver_id = $receiverId;
        }

        $notification->sender_id = $sender->id;
        $notification->url = $url;
        $notification->message = $message;
        $notification->seen = 0;
        $notification->receiver_status = 0;
        $notification->sender_status = 1;
        $notification->created_at = now();
        $notification->save();

        $this->broadcast($notification, $visit, $actor, $action);

        return $notification;
    }

    public function message(CheckinLog $visit, string $action, string $actorName): string
    {
        $client = $visit->contactDisplayLabel();
        $purpose = trim((string) $visit->visit_purpose);
        if ($purpose === '') {
            $purpose = 'Office visit';
        }

        $actionText = match ($action) {
            self::ACTION_REASSIGNED => 'Reassigned by '.$actorName.'.',
            self::ACTION_PLEASE_SEND => 'Please send. Requested by '.$actorName.'.',
            default => 'Checked in by '.$actorName.'.',
        };

        return $client.' — '.$purpose.'. '.$actionText;
    }

    public function visitUrl(CheckinLog $visit): string
    {
        $tab = match ((int) $visit->status) {
            2 => 'attending',
            1 => 'completed',
            default => 'waiting',
        };

        return NotificationUrl::withQuery(url('/office-visits/'.$tab), [
            'checkin' => $visit->id,
        ]);
    }

    private function broadcast(Notification $notification, CheckinLog $visit, string $actor, string $action): void
    {
        try {
            broadcast(new OfficeVisitNotificationCreated(
                (int) $notification->id,
                (int) $notification->receiver_id,
                [
                    'id' => $notification->id,
                    'checkin_id' => $visit->id,
                    'is_reception_alert' => $action === self::ACTION_PLEASE_SEND,
                    'message' => $notification->message,
                    'sender_name' => $actor,
                    'client_name' => $visit->contactDisplayLabel(),
                    'visit_purpose' => $visit->visit_purpose,
                    'created_at' => $notification->created_at?->toIso8601String() ?? now()->toIso8601String(),
                    'url' => $notification->url,
                ]
            ));
        } catch (\Exception $exception) {
            Log::warning('Failed to broadcast office visit notification', [
                'notification_id' => $notification->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
