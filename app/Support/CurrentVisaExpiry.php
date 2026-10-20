<?php

namespace App\Support;

use App\Models\ClientVisaCountry;
use Carbon\Carbon;

/**
 * The current visa expiry is the latest date on the client's visa rows.
 * A missing date stays blank so it can be entered on the visa row.
 */
final class CurrentVisaExpiry
{
    public static function sql(string $adminAlias): string
    {
        if (! preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $adminAlias)) {
            throw new \InvalidArgumentException('Unsafe SQL alias for current visa expiry.');
        }

        return '(SELECT cvc.visa_expiry_date FROM client_visa_countries cvc '
            .'WHERE cvc.client_id = '.$adminAlias.'.id AND cvc.visa_expiry_date IS NOT NULL '
            .'ORDER BY cvc.visa_expiry_date DESC LIMIT 1)';
    }

    public static function formatForDisplay(mixed $clientId): string
    {
        $date = self::latestDate($clientId);
        if ($date === null) {
            return '';
        }

        return Carbon::parse($date)->format('d/m/Y');
    }

    public static function latestDate(mixed $clientId): ?string
    {
        if ($clientId === null || $clientId === '' || ! is_numeric($clientId)) {
            return null;
        }

        return self::dateString(
            ClientVisaCountry::query()
                ->where('client_id', (int) $clientId)
                ->whereNotNull('visa_expiry_date')
                ->orderByDesc('visa_expiry_date')
                ->value('visa_expiry_date')
        );
    }

    private static function dateString(mixed $value): ?string
    {
        if ($value === null || $value === '' || $value === '0000-00-00') {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        $parsed = date_create((string) $value);

        return $parsed ? $parsed->format('Y-m-d') : null;
    }
}
