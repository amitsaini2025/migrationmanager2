<?php

namespace App\Services\VisaPricing;

use InvalidArgumentException;

class VisaPricingSheetParser
{
    private const HEADER_SCAN_MAX_ROWS = 25;

    /**
     * @param  list<list<string|null>>  $rows
     * @return list<array{
     *     id: string,
     *     label: string,
     *     base_charge: int,
     *     additional_18_plus: int|null,
     *     additional_u18: int|null,
     *     non_internet_app_charge: int|null,
     *     subsequent_temp_app_charge: int|null,
     *     page: int|null
     * }>
     */
    public function parse(array $rows): array
    {
        if ($rows === []) {
            throw new InvalidArgumentException('Visa pricing sheet is empty.');
        }

        $headerRowIndex = $this->findHeaderRowIndex($rows);
        $headerRow = array_map(fn ($cell) => $this->normalizeHeader((string) ($cell ?? '')), $rows[$headerRowIndex]);
        $columnMap = $this->resolveColumnMap($headerRow);

        $visas = [];
        $sequentialId = 0;

        for ($i = $headerRowIndex + 1, $count = count($rows); $i < $count; $i++) {
            $row = $rows[$i];
            if ($this->rowIsEmpty($row)) {
                continue;
            }

            $label = $this->resolveLabel($row, $columnMap);
            if ($label === '') {
                continue;
            }

            $baseCharge = $this->parseCharge($this->cell($row, $columnMap, 'base_charge'), required: false);
            if ($baseCharge === null) {
                continue;
            }

            $sequentialId++;
            $explicitId = $this->cell($row, $columnMap, 'id');
            $id = $explicitId !== null && trim($explicitId) !== ''
                ? trim($explicitId)
                : (string) $sequentialId;

            $visas[] = [
                'id' => $id,
                'label' => $label,
                'base_charge' => $baseCharge,
                'additional_18_plus' => $this->parseCharge($this->cell($row, $columnMap, 'additional_18_plus')),
                'additional_u18' => $this->parseCharge($this->cell($row, $columnMap, 'additional_u18')),
                'non_internet_app_charge' => $this->parseCharge($this->cell($row, $columnMap, 'non_internet_app_charge')),
                'subsequent_temp_app_charge' => $this->parseCharge($this->cell($row, $columnMap, 'subsequent_temp_app_charge')),
                'page' => $this->parsePage($this->cell($row, $columnMap, 'page')),
            ];
        }

        if ($visas === []) {
            throw new InvalidArgumentException('No visa rows parsed from sheet.');
        }

        return $visas;
    }

    /**
     * @param  list<list<string|null>>  $rows
     */
    private function findHeaderRowIndex(array $rows): int
    {
        $maxScan = min(count($rows), self::HEADER_SCAN_MAX_ROWS);

        for ($i = 0; $i < $maxScan; $i++) {
            $headerRow = array_map(
                fn ($cell) => $this->normalizeHeader((string) ($cell ?? '')),
                $rows[$i],
            );

            if ($this->headerRowIsRecognized($headerRow)) {
                return $i;
            }
        }

        throw new InvalidArgumentException('Sheet must include Visa Subclass and Base Application Charge columns.');
    }

    /**
     * @param  list<string>  $headerRow
     */
    private function headerRowIsRecognized(array $headerRow): bool
    {
        try {
            $this->resolveColumnMap($headerRow);

            return true;
        } catch (InvalidArgumentException) {
            return false;
        }
    }

    /**
     * @param  list<string>  $headerRow
     * @return array<string, int>
     */
    private function resolveColumnMap(array $headerRow): array
    {
        $patterns = [
            'id' => ['^id$'],
            'label' => ['visa subclass'],
            'category' => ['^category$'],
            'subclass' => ['^subclass$'],
            'stream' => ['visa / stream', 'visa/stream', 'visa stream'],
            'base_charge' => ['base application charge', 'main applicant'],
            'additional_18_plus' => ['additional applicant charge 18', 'additional applicant 18'],
            'additional_u18' => ['additional applicant charge under 18', 'applicant under 18'],
            'non_internet_app_charge' => ['non-internet application charge', 'non internet application charge'],
            'subsequent_temp_app_charge' => [
                'subsequent temporary application charge',
                'second vac',
                'extra charge',
            ],
            'page' => ['^page$'],
        ];

        $map = [];

        foreach ($patterns as $key => $needles) {
            foreach ($headerRow as $index => $header) {
                foreach ($needles as $needle) {
                    $matches = str_starts_with($needle, '^')
                        ? preg_match('/'.$needle.'/', $header) === 1
                        : str_contains($header, $needle);

                    if ($matches) {
                        if ($key === 'subsequent_temp_app_charge' && isset($map[$key])) {
                            continue;
                        }

                        $map[$key] = $index;
                        break 2;
                    }
                }
            }
        }

        $hasLegacyLabel = isset($map['label']);
        $hasCompositeLabel = isset($map['stream']) || isset($map['subclass']);
        $hasBaseCharge = isset($map['base_charge']);

        if (! $hasBaseCharge || (! $hasLegacyLabel && ! $hasCompositeLabel)) {
            throw new InvalidArgumentException('Sheet must include Visa Subclass and Base Application Charge columns.');
        }

        return $map;
    }

    /**
     * @param  list<string|null>  $row
     * @param  array<string, int>  $columnMap
     */
    private function resolveLabel(array $row, array $columnMap): string
    {
        if (isset($columnMap['label'])) {
            return trim((string) ($this->cell($row, $columnMap, 'label') ?? ''));
        }

        $stream = trim((string) ($this->cell($row, $columnMap, 'stream') ?? ''));
        $subclass = trim((string) ($this->cell($row, $columnMap, 'subclass') ?? ''));
        $category = trim((string) ($this->cell($row, $columnMap, 'category') ?? ''));

        if ($stream !== '' && $subclass !== '') {
            return $stream.' (subclass '.$subclass.')';
        }

        if ($stream !== '') {
            return $stream;
        }

        if ($subclass !== '' && $category !== '') {
            return $category.' (subclass '.$subclass.')';
        }

        if ($subclass !== '') {
            return 'Visa (subclass '.$subclass.')';
        }

        return '';
    }

    /**
     * @param  list<string|null>  $row
     * @param  array<string, int>  $columnMap
     */
    private function cell(array $row, array $columnMap, string $key): ?string
    {
        if (! isset($columnMap[$key])) {
            return null;
        }

        $index = $columnMap[$key];

        return isset($row[$index]) ? (string) $row[$index] : null;
    }

    /**
     * @param  list<string|null>  $row
     */
    private function rowIsEmpty(array $row): bool
    {
        foreach ($row as $cell) {
            if (trim((string) ($cell ?? '')) !== '') {
                return false;
            }
        }

        return true;
    }

    private function normalizeHeader(string $header): string
    {
        $header = mb_strtolower(trim($header));

        return preg_replace('/\s+/', ' ', $header) ?? $header;
    }

    private function parseCharge(?string $value, bool $required = false): ?int
    {
        if ($value === null || trim($value) === '') {
            if ($required) {
                throw new InvalidArgumentException('Missing required charge value in sheet row.');
            }

            return null;
        }

        $normalized = strtolower(trim($value));
        if (in_array($normalized, ['n/a', 'na', '-', 'nil', 'none'], true)) {
            return null;
        }

        $numeric = preg_replace('/[^0-9.\-]/', '', str_replace(',', '', $value));
        if ($numeric === '' || $numeric === null) {
            if ($required) {
                throw new InvalidArgumentException('Invalid charge value in sheet: '.$value);
            }

            return null;
        }

        return (int) round((float) $numeric);
    }

    private function parsePage(?string $value): ?int
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $numeric = preg_replace('/[^0-9]/', '', $value);
        if ($numeric === '') {
            return null;
        }

        return (int) $numeric;
    }
}
