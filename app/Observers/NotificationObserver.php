<?php

namespace App\Observers;

use App\Models\Notification;
use App\Support\CachedHeaderCounts;
use Illuminate\Support\Facades\DB;

class NotificationObserver
{
    public function saved(Notification $notification): void
    {
        $this->forget($notification);
    }

    public function deleted(Notification $notification): void
    {
        $this->forget($notification);
    }

    private function forget(Notification $notification): void
    {
        $receiverId = (int) $notification->receiver_id;
        if ($receiverId < 1) {
            return;
        }

        DB::afterCommit(static function () use ($receiverId): void {
            CachedHeaderCounts::forgetNotificationUnread($receiverId);
        });
    }
}
