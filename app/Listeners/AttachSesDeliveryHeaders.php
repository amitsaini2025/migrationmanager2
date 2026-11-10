<?php

namespace App\Listeners;

use App\Services\SesDeliveryHeaders;
use Illuminate\Mail\Events\MessageSending;

class AttachSesDeliveryHeaders
{
    public function __construct(private SesDeliveryHeaders $headers) {}

    /**
     * Add SES configuration-set and message-tag headers when a send is tied to an email log.
     */
    public function handle(MessageSending $event): void
    {
        $header = $event->message->getHeaders()->get(SesDeliveryHeaders::LOG_HEADER);
        if ($header === null) {
            return;
        }

        $emailLogId = trim($header->getBodyAsString());
        if (! ctype_digit($emailLogId)) {
            return;
        }

        $this->headers->apply($event->message, (int) $emailLogId);
    }
}
