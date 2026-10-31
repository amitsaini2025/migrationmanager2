<?php

namespace App\Support;

/**
 * Resolves a visa-sheet category (TR stream, and similar) from the matter
 * title and nick name, using the sheet's suggest rules in config order.
 *
 * Earlier rules win, so "second post higher" is not also "post higher".
 */
final class VisaSheetCategory
{
    public static function derivesFromMatter(array $config): bool
    {
        return ! empty($config['refused_visa_type_from_matter']);
    }

    /**
     * @param  array<string, list<string>>  $rules
     */
    public static function resolve(?string $title, ?string $nick, array $rules): ?string
    {
        $haystack = strtolower(trim((string) $title.' '.(string) $nick));
        if (trim($haystack) === '') {
            return null;
        }

        foreach ($rules as $key => $needles) {
            if (! is_string($key) || ! is_array($needles)) {
                continue;
            }
            foreach ($needles as $needle) {
                $needle = strtolower(trim((string) $needle));
                if ($needle !== '' && str_contains($haystack, $needle)) {
                    return $key;
                }
            }
        }

        return null;
    }

    /**
     * Postgres CASE expression (no alias) matching {@see resolve()}.
     *
     * @param  array<string, list<string>>  $rules
     */
    public static function sqlCase(string $titleExpr, string $nickExpr, array $rules): string
    {
        $titleExpr = self::safeExpr($titleExpr);
        $nickExpr = self::safeExpr($nickExpr);
        $haystack = "LOWER(COALESCE({$titleExpr}, '') || ' ' || COALESCE({$nickExpr}, ''))";
        $whens = [];

        foreach ($rules as $key => $needles) {
            if (! is_string($key) || ! is_array($needles)) {
                continue;
            }
            $checks = [];
            foreach ($needles as $needle) {
                $needle = strtolower(trim((string) $needle));
                if ($needle === '') {
                    continue;
                }
                $checks[] = 'STRPOS('.$haystack.', '.self::quote($needle).') > 0';
            }
            if ($checks === []) {
                continue;
            }
            $whens[] = 'WHEN '.implode(' OR ', $checks).' THEN '.self::quote($key);
        }

        if ($whens === []) {
            return 'NULL';
        }

        return 'CASE '.implode(' ', $whens).' ELSE NULL END';
    }

    private static function safeExpr(string $expr): string
    {
        if (! preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*(\.[a-zA-Z_][a-zA-Z0-9_]*)?$/', $expr)) {
            return 'NULL';
        }

        return $expr;
    }

    private static function quote(string $value): string
    {
        return "'".str_replace("'", "''", $value)."'";
    }
}
