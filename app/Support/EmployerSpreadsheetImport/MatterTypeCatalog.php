<?php

namespace App\Support\EmployerSpreadsheetImport;

/**
 * Maps a spreadsheet matter label onto a CRM matter title.
 *
 * Titles are resolved against the matters table at runtime so local ids are not copied to production.
 */
final class MatterTypeCatalog
{
    /**
     * @return array{title: string, for_company: bool, kind: string, note: ?string}|null
     */
    public static function match(?string $sheetType, string $role, string $subclass186Stream): ?array
    {
        $label = self::label($sheetType);
        if ($label === '') {
            return null;
        }

        if (str_starts_with($label, 'family sponsorship')) {
            return null;
        }

        if ($role === 'Migrant') {
            return self::matchMigrant($label, $subclass186Stream);
        }

        return self::matchSponsor($label);
    }

    /**
     * @return array{title: string, for_company: bool, kind: string, note: ?string}|null
     */
    private static function matchMigrant(string $label, string $subclass186Stream): ?array
    {
        if (str_contains($label, '186')) {
            $title = match ($subclass186Stream) {
                'trt' => '186 - Employer Nomination Scheme - Temporary Residence Transition',
                'agreement' => '186 - Employer Nomination Scheme - Agreement',
                default => '186 - Employer Nomination Scheme - Direct Entry',
            };

            return self::personVisa($title, 'The 186 sheet does not say Direct Entry, TRT, or Agreement. This import uses '.$subclass186Stream.'.');
        }

        if (str_contains($label, 'subsequent') && str_contains($label, 'skills in demand')) {
            return self::personVisa('482 - Skills in Demand (Subsequent)', null);
        }

        if (str_contains($label, 'skills in demand')) {
            return self::personVisa('482 - Skills in Demand', null);
        }

        if (str_contains($label, 'temporary skill shortage')) {
            $note = str_contains($label, 'subsequent')
                ? 'Subsequent entrant TSS is stored as 482 Temporary Skill Shortage - Short Term. There is no separate subsequent TSS matter type.'
                : null;

            return self::personVisa('482 - Temporary Skill Shortage - Short Term', $note);
        }

        return null;
    }

    /**
     * @return array{title: string, for_company: bool, kind: string, note: ?string}|null
     */
    private static function matchSponsor(string $label): ?array
    {
        return match (true) {
            str_contains($label, 'standard business sponsorship') => self::company('Standard Business Sponsorship', 'sponsorship', null),
            str_contains($label, 'temporary activities sponsorship'),
            str_contains($label, 'temporary activity sponsorship') => self::company('Temporary Activities Sponsorship', 'sponsorship', null),
            str_contains($label, 'designated area migration agreement') => self::company('Designated Area Migration Agreement', 'sponsorship', null),
            $label === 'labour agreement' => self::company('Labour Agreement-DAMA', 'sponsorship', null),
            str_contains($label, 'skills in demand') => self::company('Skill in Demand Nomination - Core Skills', 'nomination', null),
            $label === 'employer nomination' => self::company('Employer Nomination Scheme', 'nomination', null),
            str_contains($label, 'regional employer nomination') => self::company('Regional Employer Nomination', 'nomination', null),
            str_contains($label, 'training nomination') => self::company('Training Nomination', 'nomination', null),
            str_starts_with($label, 'art ') => self::company('Administrative Review Tribunal', 'tribunal', null),
            default => null,
        };
    }

    public static function isActiveFileStatus(?string $status): bool
    {
        $status = strtolower(trim((string) $status));

        return ! in_array($status, ['file closed', '**ready to close**', 'ready to close'], true);
    }

    private static function label(?string $sheetType): string
    {
        $label = strtolower(trim((string) $sheetType));
        $label = str_replace(['–', '—'], '-', $label);
        $label = preg_replace('/\s+/', ' ', $label) ?? '';

        return trim($label);
    }

    /**
     * @return array{title: string, for_company: bool, kind: string, note: ?string}
     */
    private static function personVisa(string $title, ?string $note): array
    {
        return [
            'title' => $title,
            'for_company' => false,
            'kind' => 'visa',
            'note' => $note,
        ];
    }

    /**
     * @return array{title: string, for_company: bool, kind: string, note: ?string}
     */
    private static function company(string $title, string $kind, ?string $note): array
    {
        return [
            'title' => $title,
            'for_company' => true,
            'kind' => $kind,
            'note' => $note,
        ];
    }
}
