<?php

namespace App\Support\EmployerSpreadsheetImport;

use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

final class WorkbookReader
{
    /**
     * @return list<SheetRow>
     */
    public function readDirectory(string $directory): array
    {
        $files = [
            'sponsorship' => $directory.DIRECTORY_SEPARATOR.'sponsorship.xlsx',
            '186' => $directory.DIRECTORY_SEPARATOR.'186 LIST.xlsx',
            '482' => $directory.DIRECTORY_SEPARATOR.'482 VIUST.xlsx',
        ];

        $rows = [];
        foreach ($files as $source => $path) {
            if (! is_file($path)) {
                throw new \RuntimeException('Workbook not found: '.$path);
            }
            foreach ($this->readFile($source, $path) as $row) {
                $rows[] = $row;
            }
        }

        return $rows;
    }

    /**
     * @return list<SheetRow>
     */
    public function readFile(string $source, string $path): array
    {
        $spreadsheet = IOFactory::createReader('Xlsx');
        $spreadsheet->setReadDataOnly(true);
        $sheet = $spreadsheet->load($path)->getActiveSheet();
        $header = null;
        $rows = [];

        foreach ($sheet->getRowIterator() as $excelRow) {
            $values = [];
            foreach ($excelRow->getCellIterator() as $cell) {
                $values[] = $cell->getValue();
            }
            if ($header === null) {
                $header = array_map(fn (mixed $value): string => $this->header($value), $values);

                continue;
            }
            $record = [];
            foreach ($header as $index => $name) {
                $record[$name] = $values[$index] ?? null;
            }
            if (! $this->rowHasValues($record)) {
                continue;
            }
            $rows[] = $this->toRow($source, $record);
        }

        return $rows;
    }

    /**
     * @param  array<string, mixed>  $record
     */
    private function toRow(string $source, array $record): SheetRow
    {
        $role = trim((string) ($record['Migrant/ Sponsor'] ?? ''));
        if ($role === '' && $source === 'sponsorship') {
            $role = 'Sponsor';
        }
        if ($role === '' && $source !== 'sponsorship') {
            $role = 'Migrant';
        }

        return new SheetRow(
            source: $source,
            matterId: trim((string) ($record['Matter ID'] ?? '')),
            business: $this->text($record['Business / Sponsor Name'] ?? null) ?? $this->text($record['Company Name'] ?? null),
            trading: $this->text($record['TradingName'] ?? null),
            role: $role,
            surname: $this->text($record['Surname'] ?? null),
            given: $this->text($record['Given Names'] ?? null),
            emails: $this->emails($record['E-mail'] ?? null),
            phone: $this->phone($record['Mobile'] ?? null) ?? $this->phone($record['Contact Mobile'] ?? null),
            matterType: $this->text($record['Matter Type'] ?? null),
            fileStatus: $this->text($record['File Status'] ?? null),
            dob: $this->date($record['D.O.B.'] ?? null),
            position: $this->text($record['Current Position'] ?? null),
            anzsco: $this->text($record['Applicant ANZSCO Code'] ?? null),
            responsible: $this->text($record['Responsible Name'] ?? null),
            engagementOn: $this->date($record['Engagement Date'] ?? null),
            passportCountry: $this->text($record['Current Passport -Country'] ?? null),
            passportExpiry: $this->date($record['Current Passport -Expiry'] ?? null),
            maritalStatus: $this->text($record['Marital Status'] ?? null),
        );
    }

    private function header(mixed $value): string
    {
        return trim(preg_replace('/\s+/', ' ', str_replace("\n", ' ', (string) $value)) ?? '');
    }

    /**
     * @param  array<string, mixed>  $record
     */
    private function rowHasValues(array $record): bool
    {
        foreach ($record as $value) {
            if ($this->text($value) !== null || $value instanceof \DateTimeInterface) {
                return true;
            }
        }

        return false;
    }

    private function text(mixed $value): ?string
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }
        $text = trim(str_replace("\n", ' ', (string) $value));

        return $text === '' ? null : $text;
    }

    /**
     * @return list<string>
     */
    private function emails(mixed $value): array
    {
        $text = $this->text($value);
        if ($text === null) {
            return [];
        }

        $emails = [];
        foreach (preg_split('/[;,]/', $text) ?: [] as $email) {
            $email = strtolower(trim($email));
            if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $emails[] = $email;
            }
        }

        return array_values(array_unique($emails));
    }

    private function phone(mixed $value): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $value) ?? '';
        if ($digits === '') {
            return null;
        }
        if (str_starts_with($digits, '61') && strlen($digits) > 9) {
            $digits = '0'.substr($digits, 2);
        }

        return $digits;
    }

    private function date(mixed $value): ?string
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }
        if (is_numeric($value) && (float) $value > 20000 && (float) $value < 80000) {
            return ExcelDate::excelToDateTimeObject((float) $value)->format('Y-m-d');
        }
        $text = $this->text($value);
        if ($text === null) {
            return null;
        }
        foreach (['d/m/Y', 'Y-m-d', 'd-m-Y'] as $format) {
            try {
                $parsed = Carbon::createFromFormat($format, $text);
                if ($parsed !== false) {
                    return $parsed->format('Y-m-d');
                }
            } catch (\Throwable) {
                continue;
            }
        }

        return null;
    }
}
