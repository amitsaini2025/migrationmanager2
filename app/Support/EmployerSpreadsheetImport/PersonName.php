<?php

namespace App\Support\EmployerSpreadsheetImport;

final class PersonName
{
    /**
     * @return array{0: string, 1: string} first name, last name
     */
    public static function split(?string $surname, ?string $given): array
    {
        $given = trim((string) $given);
        $surname = trim((string) $surname);

        if ($given !== '') {
            return [$given, $surname !== '' ? $surname : '.'];
        }

        $parts = preg_split('/\s+/', $surname) ?: [];
        $parts = array_values(array_filter($parts, fn (string $part): bool => $part !== ''));
        if (count($parts) <= 1) {
            return [$surname !== '' ? $surname : 'Unknown', '.'];
        }

        $last = (string) array_pop($parts);

        return [implode(' ', $parts), $last];
    }
}
