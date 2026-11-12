<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Notification extends Model
{
    use Sortable;

    protected $fillable = ['sender_id', 'receiver_id', 'module_id', 'url', 'notification_type', 'message', 'receiver_status', 'sender_status', 'seen'];

    public $sortable = ['id', 'created_at', 'updated_at'];

    /**
     * Get the checkin log associated with this notification
     */
    public function checkinLog()
    {
        return $this->belongsTo(CheckinLog::class, 'module_id');
    }

    /**
     * Get the sender of this notification
     */
    public function sender()
    {
        return $this->belongsTo(Staff::class, 'sender_id');
    }

    /**
     * Get the receiver of this notification
     */
    public function receiver()
    {
        return $this->belongsTo(Staff::class, 'receiver_id');
    }

    /**
     * Mark a notification read only when it belongs to the logged-in staff member.
     */
    public static function markReadIfOwned(int $notificationId, int $userId): bool
    {
        if ($notificationId < 1 || $userId < 1) {
            return false;
        }

        $notification = static::query()->whereKey($notificationId)->first();
        if (! $notification || (int) $notification->receiver_id !== $userId) {
            return false;
        }

        if ((int) $notification->receiver_status === 1) {
            return true;
        }

        $notification->receiver_status = 1;
        $notification->save();

        return true;
    }
}
