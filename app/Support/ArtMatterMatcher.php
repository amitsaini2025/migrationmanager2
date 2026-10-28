<?php

namespace App\Support;

/**
 * ART sheet matter matching: nick_name art, whole-word "art" in title,
 * or title markers for AAT/tribunal wording.
 *
 * Whole-word matching avoids false positives such as "Partner" (contains "art").
 */
final class ArtMatterMatcher
{
    /**
     * SQL boolean expression for joining/filtering matters as ART.
     * Uses Postgres word-boundary regex (\m / \M), matching production DB.
     */
    public static function sqlCondition(string $mattersAlias = 'm'): string
    {
        $alias = preg_replace('/[^a-zA-Z0-9_]/', '', $mattersAlias) ?: 'm';

        return '('
            ."LOWER(COALESCE({$alias}.nick_name, '')) = 'art'"
            ." OR LOWER(COALESCE({$alias}.title, '')) ~ '\\mart\\M'"
            ." OR LOWER(COALESCE({$alias}.title, '')) LIKE '%administrative appeals%'"
            ." OR LOWER(COALESCE({$alias}.title, '')) LIKE '%tribunal%'"
            .')';
    }

    /**
     * PHP-side equivalent of {@see sqlCondition()} for tests and non-SQL checks.
     */
    public static function matches(?string $nickName, ?string $title): bool
    {
        $nick = strtolower(trim((string) $nickName));
        if ($nick === 'art') {
            return true;
        }

        $titleLower = strtolower((string) $title);
        if ($titleLower === '') {
            return false;
        }

        if (str_contains($titleLower, 'administrative appeals') || str_contains($titleLower, 'tribunal')) {
            return true;
        }

        return (bool) preg_match('/\bart\b/i', $titleLower);
    }
}
