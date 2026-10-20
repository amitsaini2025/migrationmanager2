<?php

namespace App\Support\EmployerSpreadsheetImport;

/**
 * Matches a spreadsheet business to CRM companies by normalised name.
 *
 * @phpstan-type CompanyRecord array{id: int, name: string}
 */
final class CompanyNameMatcher
{
    /**
     * @param  list<CompanyRecord>  $companies  Legal names and trading names, same id allowed more than once
     * @return array{id: ?int, ambiguous: bool}
     */
    public static function match(?string $name, array $companies): array
    {
        $key = BusinessName::groupKey($name);
        if ($key === '') {
            return ['id' => null, 'ambiguous' => false];
        }

        $hits = [];
        foreach ($companies as $company) {
            $crmKey = BusinessName::groupKey($company['name']);
            if ($crmKey === '' || ! self::sameBusiness($key, $crmKey)) {
                continue;
            }
            $hits[$company['id']] = true;
        }

        if (count($hits) === 1) {
            return ['id' => (int) array_key_first($hits), 'ambiguous' => false];
        }

        if (count($hits) > 1) {
            return ['id' => null, 'ambiguous' => true];
        }

        return ['id' => null, 'ambiguous' => false];
    }

    public static function sameBusiness(string $left, string $right): bool
    {
        if ($left === $right) {
            return true;
        }

        if (self::digitSuffixContainment($left, $right)) {
            return true;
        }

        if (strlen($left) >= 8 && strlen($right) >= 8 && abs(strlen($left) - strlen($right)) <= 2 && levenshtein($left, $right) <= 1) {
            return true;
        }

        return false;
    }

    private static function digitSuffixContainment(string $left, string $right): bool
    {
        $shorter = strlen($left) <= strlen($right) ? $left : $right;
        $longer = $shorter === $left ? $right : $left;
        if (strlen($shorter) < 12 || ! str_contains($longer, $shorter)) {
            return false;
        }

        $remainder = trim(str_replace($shorter, '', $longer));

        return $remainder === '' || preg_match('/^[0-9 ]+$/', $remainder) === 1;
    }
}
