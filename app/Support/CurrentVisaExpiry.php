<?php

namespace App\Support;

use App\Models\Admin;
use App\Models\ClientVisaCountry;
use Illuminate\Support\Facades\Schema;

/**
 * The current visa expiry is the latest date on the client's visa rows.
 * admins.visaExpiry is only the fallback when the client has no visa row.
 */
final class CurrentVisaExpiry
{
    public static function sql(string $adminAlias): string
    {
        if (! preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $adminAlias)) {
            throw new \InvalidArgumentException('Unsafe SQL alias for current visa expiry.');
        }

        $fallback = $adminAlias.'."visaExpiry"';

        return 'CASE WHEN EXISTS (SELECT 1 FROM client_visa_countries cvc WHERE cvc.client_id = '.$adminAlias.'.id) THEN ('
            .'SELECT cvc.visa_expiry_date FROM client_visa_countries cvc '
            .'WHERE cvc.client_id = '.$adminAlias.'.id AND cvc.visa_expiry_date IS NOT NULL '
            .'ORDER BY cvc.visa_expiry_date DESC LIMIT 1'
            .') ELSE '.$fallback.' END';
    }

    /**
     * Copy the latest visa-row expiry onto the client column.
     * Clients with no visa rows keep the column, so older records still have a fallback.
     */
    public static function sync(Admin $client): void
    {
        if (! Schema::hasTable('client_visa_countries') || ! Schema::hasColumn('admins', 'visaExpiry')) {
            return;
        }

        $hasVisaRow = ClientVisaCountry::query()
            ->where('client_id', $client->id)
            ->exists();

        if (! $hasVisaRow) {
            return;
        }

        $latest = ClientVisaCountry::query()
            ->where('client_id', $client->id)
            ->whereNotNull('visa_expiry_date')
            ->orderByDesc('visa_expiry_date')
            ->value('visa_expiry_date');

        $latest = self::dateString($latest);
        $current = self::dateString($client->visaExpiry);

        if ($current === $latest) {
            return;
        }

        $client->visaExpiry = $latest;
        $client->save();
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
