<?php

namespace App\Listeners;

use App\Services\ZohoDelivery;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mime\Email;

class TagZohoTransport
{
    public function __construct(private ZohoDelivery $zohoDelivery) {}

    /**
     * Remember that this attempt is the Zoho SMTP transport. The status is updated only after it succeeds.
     */
    public function handle(MessageEvent $event): void
    {
        if (! $this->zohoDelivery->isZohoTransport($event->getTransport())) {
            return;
        }

        $message = $event->getMessage();
        if (! $message instanceof Email) {
            return;
        }

        if (! $message->getHeaders()->has(ZohoDelivery::VIA_HEADER)) {
            $message->getHeaders()->addTextHeader(ZohoDelivery::VIA_HEADER, 'zoho');
        }
    }
}
