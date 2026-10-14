<?php

namespace App\Services\VisaPricing;

use InvalidArgumentException;

class VisaPricingSheetParser
{
    private const HEADER_SCAN_MAX_ROWS = 25;

    /**
     * @param  list<list<string|null>>  $rows
     * @return list<VisaPricingVisaRecord::VisaPricingRow>
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

            $visaStream = $this->resolveVisaStream($row, $columnMap);
            $subclass = trim((string) ($this->cell($row, $columnMap, 'subclass') ?? ''));
            $category = trim((string) ($this->cell($row, $columnMap, 'category') ?? ''));

            if ($visaStream === '' && $subclass === '' && $category === '') {
                continue;
            }

            $sequentialId++;
            $explicitId = $this->cell($row, $columnMap, 'id');
            $id = $explicitId !== null && trim($explicitId) !== ''
                ? trim($explicitId)
                : (string) $sequentialId;

            $visas[] = VisaPricingVisaRecord::normalize([
                'id' => $id,
                'category' => $category,
                'subclass' => $subclass,
                'visa_stream' => $visaStream !== '' ? $visaStream : $category,
                'main_applicant_fee' => $this->cell($row, $columnMap, 'main_applicant_fee') ?? '',
                'additional_applicant_18_plus_fee' => $this->cell($row, $columnMap, 'additional_applicant_18_plus_fee'),
                'applicant_under_18_fee' => $this->cell($row, $columnMap, 'applicant_under_18_fee'),
                'second_vac_charges' => $this->parseText($this->cell($row, $columnMap, 'second_vac_charges')),
                'notes' => $this->parseText($this->cell($row, $columnMap, 'notes')),
                'source' => $this->parseText($this->cell($row, $columnMap, 'source')),
            ]);
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

        throw new InvalidArgumentException('Sheet must include required visa pricing columns (e.g. Main Applicant / Visa Stream).');
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
            'legacy_label' => ['visa subclass'],
            'category' => ['^category$'],
            'subclass' => ['^subclass$'],
            'visa_stream' => ['visa / stream', 'visa/stream', 'visa stream'],
            'main_applicant_fee' => ['base application charge', 'main applicant'],
            'additional_applicant_18_plus_fee' => ['additional applicant charge 18', 'additional applicant 18'],
            'applicant_under_18_fee' => ['additional applicant charge under 18', 'applicant under 18'],
            'second_vac_charges' => [
                'subsequent temporary application charge',
                'second vac',
            ],
            'notes' => ['^notes$'],
            'source' => ['^source$'],
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
                        if ($key === 'second_vac_charges' && isset($map[$key])) {
                            continue;
                        }

                        $map[$key] = $index;
                        break 2;
                    }
                }
            }
        }

        $hasLegacyLabel = isset($map['legacy_label']);
        $hasStreamLayout = isset($map['visa_stream']) || isset($map['subclass']) || isset($map['category']);
        $hasMainFee = isset($map['main_applicant_fee']);

        if (! $hasMainFee || (! $hasLegacyLabel && ! $hasStreamLayout)) {
            throw new InvalidArgumentException('Sheet must include required visa pricing columns (e.g. Main Applicant / Visa Stream).');
        }

        return $map;
    }

    /**
     * @param  list<string|null>  $row
     * @param  array<string, int>  $columnMap
     */
    private function resolveVisaStream(array $row, array $columnMap): string
    {
        if (isset($columnMap['visa_stream'])) {
            $stream = trim((string) ($this->cell($row, $columnMap, 'visa_stream') ?? ''));
            if ($stream !== '') {
                return $stream;
            }
        }

        if (isset($columnMap['legacy_label'])) {
            return trim((string) ($this->cell($row, $columnMap, 'legacy_label') ?? ''));
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

    private function parseText(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }
}
