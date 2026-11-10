<?php

namespace App\Services;

use Illuminate\Mail\Message;
use Symfony\Component\Mime\Email;

class SesDeliveryHeaders
{
    public const LOG_HEADER = 'X-CRM-Email-Log-Id';

    /**
     * Stamp the CRM log id and the SES SMTP headers that publish it back on events.
     */
    public function apply(Email $email, int $emailLogId): void
    {
        if ($emailLogId < 1) {
            return;
        }

        $headers = $email->getHeaders();
        if (! $headers->has(self::LOG_HEADER)) {
            $headers->addTextHeader(self::LOG_HEADER, (string) $emailLogId);
        }

        $configurationSet = trim((string) config('services.ses.configuration_set'));
        if ($configurationSet !== '' && ! $headers->has('X-SES-CONFIGURATION-SET')) {
            $headers->addTextHeader('X-SES-CONFIGURATION-SET', $configurationSet);
        }

        $tag = 'email_log_id='.$emailLogId;
        $existing = $headers->get('X-SES-MESSAGE-TAGS');
        if ($existing === null) {
            $headers->addTextHeader('X-SES-MESSAGE-TAGS', $tag);

            return;
        }

        $current = $existing->getBodyAsString();
        if (! str_contains($current, $tag)) {
            $headers->remove('X-SES-MESSAGE-TAGS');
            $headers->addTextHeader('X-SES-MESSAGE-TAGS', trim($current.','.$tag, ','));
        }
    }

    public function applyToMailMessage(Message $message, int $emailLogId): void
    {
        $this->apply($message->getSymfonyMessage(), $emailLogId);
    }
}
