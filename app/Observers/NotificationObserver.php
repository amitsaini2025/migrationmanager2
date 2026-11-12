<?php

namespace App\Observers;

use App\Models\Notification;
use App\Support\CachedHeaderCounts;

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

        CachedHeaderCounts::forgetNotificationUnread($receiverId);
    }
}
