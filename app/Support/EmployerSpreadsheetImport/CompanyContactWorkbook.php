<?php

namespace App\Support\EmployerSpreadsheetImport;

use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

final class CompanyContactWorkbook
{
    /**
     * @return list<CompanyContactRow>
     */
    public function read(string $path): array
    {
        if (! is_file($path)) {
            throw new \RuntimeException('Workbook not found: '.$path);
        }

        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);
        $sheet = $reader->load($path)->getActiveSheet();
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
                if ($name === '') {
                    continue;
                }
                $record[$name] = $values[$index] ?? null;
            }
            $row = $this->toRow($record);
            if ($row->matterId === '' && $row->business === null) {
                continue;
            }
            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * @param  array<string, mixed>  $record
     */
    private function toRow(array $record): CompanyContactRow
    {
        $contacts = [];
        for ($number = 1; $number <= 8; $number++) {
            $prefix = 'Contact '.$number.' ';
            $surname = $this->text($record[$prefix.'Contact Surname'] ?? null);
            $given = $this->text($record[$prefix.'Given Names'] ?? null);
            $phone = $this->phone($record[$prefix.'Mobile'] ?? null) ?? $this->phone($record[$prefix.'Work Phone'] ?? null);
            $email = $this->email($record[$prefix.'Preferred Email'] ?? null) ?? $this->email($record[$prefix.'Alternative Email'] ?? null);
            $position = $this->text($record[$prefix.'Position'] ?? null);
            $type = $this->text($record[$prefix.'Contact Type'] ?? null);
            $dob = $this->date($record[$prefix.'DOB'] ?? null);
            if ($surname === null && $given === null && $phone === null && $email === null) {
                continue;
            }
            $contacts[] = [
                'surname' => $surname,
                'given' => $given,
                'position' => $position,
                'type' => $type,
                'dob' => $dob,
                'phone' => $phone,
                'email' => $email,
            ];
        }

        return new CompanyContactRow(
            matterId: trim((string) ($record['Matter ID'] ?? '')),
            business: $this->text($record['Business / Sponsor Name'] ?? null) ?? $this->text($record['Company Name'] ?? null),
            trading: $this->text($record['TradingName'] ?? null),
            matterPhone: $this->phone($record['Contact Mobile'] ?? null),
            contactBusinessName: $this->text($record['Contact Business Name'] ?? null),
            contactTrading: $this->text($record['Contact Business Trading Name'] ?? null),
            abn: $this->digits($record['Contact Business ABN'] ?? null),
            acn: $this->digits($record['Contact Business ACN'] ?? null),
            structure: $this->text($record['Contact Business Structure'] ?? null),
            industry: $this->text($record['Contact Business Industry'] ?? null),
            startDate: $this->date($record['Contact Business Start Date'] ?? null),
            businessPhone: $this->phone($record['Contact Business Phone Number'] ?? null),
            businessMobile: $this->phone($record['Contact Business Mobile/Cell'] ?? null),
            businessEmail: $this->email($record['Contact Business Preferred Email'] ?? null) ?? $this->email($record['Contact Business Alternative Email'] ?? null),
            street1: $this->text($record['Contact Business Street 1'] ?? null),
            street2: $this->text($record['Contact Business Street 2'] ?? null),
            suburb: $this->text($record['Contact Business Suburb'] ?? null),
            state: $this->text($record['Contact Business State'] ?? null),
            postcode: $this->text($record['Contact Business Postcode'] ?? null),
            country: $this->text($record['Contact Business Country'] ?? null),
            contacts: $contacts,
        );
    }

    private function header(mixed $value): string
    {
        return trim(preg_replace('/\s+/', ' ', str_replace("\n", ' ', (string) $value)) ?? '');
    }

    private function text(mixed $value): ?string
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }
        $text = trim(str_replace("\n", ' ', (string) $value));

        return $text === '' ? null : $text;
    }

    private function digits(mixed $value): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $value) ?? '';

        return $digits === '' ? null : $digits;
    }

    private function email(mixed $value): ?string
    {
        $email = strtolower(trim((string) $this->text($value)));

        return $email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null;
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
            return $this->usableDate($value->format('Y-m-d'));
        }
        if (is_numeric($value) && (float) $value > 20000 && (float) $value < 80000) {
            return $this->usableDate(ExcelDate::excelToDateTimeObject((float) $value)->format('Y-m-d'));
        }
        $text = $this->text($value);
        if ($text === null) {
            return null;
        }
        foreach (['d/m/Y', 'j/n/Y', 'Y-m-d', 'd-m-Y'] as $format) {
            try {
                $parsed = Carbon::createFromFormat('!'.$format, $text);
                if ($parsed !== false) {
                    return $this->usableDate($parsed->format('Y-m-d'));
                }
            } catch (\Throwable) {
                continue;
            }
        }

        return null;
    }

    private function usableDate(string $date): ?string
    {
        $year = (int) substr($date, 0, 4);
        if ($year < 1900 || $year > (int) date('Y')) {
            return null;
        }

        return $date;
    }
}
