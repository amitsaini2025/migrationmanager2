<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class BroadcastNotificationCreated implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  string  $batchUuid
     * @param  string  $message
     * @param  string|null  $title
     * @param  int  $senderId
     * @param  array<int,int>  $recipientIds
     * @param  string  $scope
     * @param  \Illuminate\Support\Carbon  $sentAt
     */
    public function __construct(
        public string $batchUuid,
        public string $message,
        public ?string $title,
        public int $senderId,
        public string $senderName,
        public array $recipientIds,
        public string $scope,
        public Carbon $sentAt
    ) {
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int,\Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $channels = [];

        if ($this->scope === 'all') {
            $channels[] = new Channel('broadcasts');
        }

        foreach ($this->recipientIds as $recipientId) {
            $channels[] = new PrivateChannel("user.{$recipientId}");
        }

        return $channels;
    }

    /**
     * Customize the event name.
     */
    public function broadcastAs(): string
    {
        return 'BroadcastNotificationCreated';
    }

    /**
     * Data sent to the frontend upon broadcast.
     *
     * @return array<string,mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'batch_uuid' => $this->batchUuid,
            'message' => $this->message,
            'title' => $this->title,
            'sender_id' => $this->senderId,
            'sender_name' => $this->senderName,
            'recipient_ids' => $this->recipientIds,
            'scope' => $this->scope,
            'sent_at' => $this->sentAt->toIso8601String(),
        ];
    }
}


