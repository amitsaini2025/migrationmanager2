<?php

namespace App\Support\EmployerSpreadsheetImport;

final class BusinessName
{
    public static function loose(?string $name): string
    {
        $name = strtolower(trim((string) $name));
        $name = str_replace('&', ' and ', $name);
        $name = preg_replace('/[^a-z0-9 ]+/', ' ', $name) ?? '';
        $name = preg_replace('/\b(proprietary|limited|pty|ltd|inc|incorporated|co|company|the|trustee|trustees|atf)\d*/', ' ', $name) ?? '';
        $name = preg_replace('/\s+/', ' ', $name) ?? '';

        return trim($name);
    }

    public static function groupKey(?string $name): string
    {
        return str_replace('trenthem', 'trentham', self::loose($name));
    }

    public static function isTrustee(?string $name): bool
    {
        return str_contains(self::loose($name), 'trust');
    }
}
