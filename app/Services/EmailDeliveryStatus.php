<?php

namespace App\Services;

class EmailDeliveryStatus
{
    public static function statusLabel(?string $status): string
    {
        return match ($status) {
            'processed' => 'Processing',
            'delivered' => 'Delivered',
            'deferred' => 'Delayed',
            'blocked' => 'Delayed',
            'bounced' => 'Undelivered',
            'dropped' => 'Failed',
            'send_failed' => 'Send Failed',
            default => 'Pending',
        };
    }

    public static function statusBadgeClass(?string $status): string
    {
        return match ($status) {
            'delivered' => 'badge-success',
            'processed' => 'badge-info',
            'deferred', 'blocked' => 'badge-warning',
            'bounced', 'dropped', 'send_failed' => 'badge-danger',
            default => 'badge-secondary',
        };
    }

    public static function summarizeUserAgent(?string $userAgent): ?string
    {
        if ($userAgent === null || $userAgent === '') {
            return null;
        }

        $ua = strtolower($userAgent);

        if (str_contains($ua, 'iphone') || str_contains($ua, 'ipad')) {
            return 'Apple device';
        }
        if (str_contains($ua, 'android')) {
            return 'Android device';
        }
        if (str_contains($ua, 'outlook')) {
            return 'Outlook';
        }
        if (str_contains($ua, 'gmail')) {
            return 'Gmail';
        }
        if (str_contains($ua, 'applemail') || str_contains($ua, 'mac os x mail')) {
            return 'Apple Mail';
        }

        return 'Email client';
    }
}
