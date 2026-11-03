<?php

namespace App\Support;

/**
 * Draft text and From-address checks for Account tab "Send to Client".
 */
final class AccountClientSendComposer
{
    public const PREFERRED_FROM = 'invoice@bansalimmigration.com.au';

    /**
     * @return array{subject: string, message: string, attachment_name: string, document_label: string}
     */
    public static function draft(string $kind, string $clientName, string $number): array
    {
        $name = trim($clientName) !== '' ? trim($clientName) : 'Client';
        $number = trim($number) !== '' ? trim($number) : '—';

        $documentLabel = match ($kind) {
            'invoice' => 'invoice',
            'office' => 'office receipt',
            default => 'client fund receipt',
        };

        $subjectPrefix = match ($kind) {
            'invoice' => 'Invoice',
            'office' => 'Office Receipt',
            default => 'Client Fund Receipt',
        };

        $attachmentPrefix = match ($kind) {
            'invoice' => 'Invoice',
            'office' => 'Office-Receipt',
            default => 'Receipt',
        };

        $message = "Dear {$name},\n\n"
            ."Please find attached your {$documentLabel} #{$number}.\n\n"
            ."If you have any questions, please don't hesitate to contact us.\n\n"
            ."Best regards,\n"
            .'Bansal Immigration';

        return [
            'subject' => $subjectPrefix.' #'.$number.' from Bansal Immigration',
            'message' => $message,
            'attachment_name' => $attachmentPrefix.'-'.$number.'.pdf',
            'document_label' => $documentLabel,
        ];
    }

    public static function plainToHtml(string $plain): string
    {
        $plain = str_replace(["\r\n", "\r"], "\n", $plain);
        $plain = trim($plain);

        return nl2br(e($plain), false);
    }

    /**
     * @param  list<array{email?: string, name?: string, nickname?: string}>  $senders
     * @return array{email: string, name: string}|null
     */
    public static function resolveSender(string $email, array $senders): ?array
    {
        $email = strtolower(trim($email));
        if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return null;
        }

        foreach ($senders as $sender) {
            $candidate = strtolower(trim((string) ($sender['email'] ?? '')));
            if ($candidate !== $email) {
                continue;
            }

            $name = trim((string) ($sender['name'] ?? ''));

            return [
                'email' => $candidate,
                'name' => $name !== '' ? $name : 'Bansal Immigration',
            ];
        }

        return null;
    }

    /**
     * @param  list<array{email?: string, name?: string}>  $senders
     */
    public static function suggestedFrom(array $senders): string
    {
        $preferred = strtolower(self::PREFERRED_FROM);
        foreach ($senders as $sender) {
            $candidate = strtolower(trim((string) ($sender['email'] ?? '')));
            if ($candidate === $preferred) {
                return $candidate;
            }
        }

        foreach ($senders as $sender) {
            $candidate = strtolower(trim((string) ($sender['email'] ?? '')));
            if ($candidate !== '' && filter_var($candidate, FILTER_VALIDATE_EMAIL)) {
                return $candidate;
            }
        }

        return '';
    }
}
