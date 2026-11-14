<?php

namespace App\Listeners;

use App\Services\ZohoDelivery;
use Illuminate\Mail\Events\MessageSent;
use Symfony\Component\Mime\Email;

class MarkZohoMailDelivered
{
    public function __construct(private ZohoDelivery $zohoDelivery) {}

    /**
     * Zoho has no delivery webhook. A successful Zoho send with no earlier result is Delivered.
     */
    public function handle(MessageSent $event): void
    {
        $message = $event->sent->getOriginalMessage();
        if (! $message instanceof Email) {
            return;
        }

        $this->zohoDelivery->markDeliveredIfUnconfirmed($message);
    }
}
