<?php

namespace App\Services;

use App\Models\EmailLog;
use App\Models\EmailLogEvent;
use Symfony\Component\Mime\Email;

class ZohoDelivery
{
    public const VIA_HEADER = 'X-CRM-Sent-Via';

    public const REASON = 'Accepted by Zoho. Zoho does not send a delivery report.';

    public function isZohoTransport(string $transport): bool
    {
        $host = strtolower((string) parse_url($transport, PHP_URL_HOST));
        if ($host === '') {
            return false;
        }

        $sesHost = strtolower(trim((string) config('mail.mailers.smtp.host')));
        if ($sesHost !== '' && $host === $sesHost) {
            return false;
        }

        return in_array($host, $this->zohoHosts(), true);
    }

    /**
     * Zoho accepted the message and no provider has reported a result yet.
     */
    public function markDeliveredIfUnconfirmed(Email $email): void
    {
        $via = $email->getHeaders()->get(self::VIA_HEADER);
        if ($via === null || strtolower(trim($via->getBodyAsString())) !== 'zoho') {
            return;
        }

        $logHeader = $email->getHeaders()->get(SesDeliveryHeaders::LOG_HEADER);
        if ($logHeader === null || ! ctype_digit(trim($logHeader->getBodyAsString()))) {
            return;
        }

        $emailLog = EmailLog::find((int) $logHeader->getBodyAsString());
        if (! $emailLog || (int) $emailLog->mail_type !== 1) {
            return;
        }

        $current = $emailLog->delivery_status;
        if ($current !== null && $current !== '' && $current !== 'pending') {
            return;
        }

        $emailLog->update([
            'delivery_status' => 'delivered',
            'delivered_at' => $emailLog->delivered_at ?? now(),
            'status_reason' => self::REASON,
        ]);

        EmailLogEvent::create([
            'email_log_id' => $emailLog->id,
            'event_type' => 'delivered',
            'occurred_at' => now(),
            'metadata' => [
                'source' => 'zoho',
                'reason' => self::REASON,
            ],
        ]);
    }

    /**
     * @return list<string>
     */
    private function zohoHosts(): array
    {
        $hosts = [];
        foreach (config('mail.mailers', []) as $name => $mailer) {
            if (! is_string($name) || ! is_array($mailer)) {
                continue;
            }

            if ($name !== 'zoho' && ! str_starts_with($name, 'zoho_mailbox_')) {
                continue;
            }

            $host = strtolower(trim((string) ($mailer['host'] ?? '')));
            if ($host !== '') {
                $hosts[] = $host;
            }
        }

        return array_values(array_unique($hosts));
    }
}
