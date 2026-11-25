<?php

namespace App\Services\FrontDesk;

use App\Models\Admin;
use Illuminate\Support\Collection;

class CheckInLookupService
{
    /**
     * Normalize a phone string to digits. Australian numbers become the 10-digit national form.
     */
    public function normalizePhone(string $phone): string
    {
        $canonical = $this->canonicalAustralianPhone($phone);
        if ($canonical !== '') {
            return $canonical;
        }

        return Admin::normalizePhoneDigitsForUniqueness($phone);
    }

    /**
     * Canonical Australian national number (10 digits with leading 0), or empty if not AU.
     *
     * Accepts common entry forms: 04XXXXXXXX, 4XXXXXXXX, +61 4XX XXX XXX, 02/03/07/08 landlines.
     */
    public function canonicalAustralianPhone(string $phone): string
    {
        $digits = Admin::normalizePhoneDigitsForUniqueness($phone);
        if ($digits === '') {
            return '';
        }

        if (str_starts_with($digits, '61') && strlen($digits) === 11) {
            $digits = '0'.substr($digits, 2);
        } elseif (strlen($digits) === 9 && preg_match('/^[23478]/', $digits) === 1) {
            $digits = '0'.$digits;
        }

        if (preg_match('/^0[23478]\d{8}$/', $digits) === 1) {
            return $digits;
        }

        return '';
    }

    public function isValidAustralianPhone(string $phone): bool
    {
        return $this->canonicalAustralianPhone($phone) !== '';
    }

    /**
     * @return array<int, mixed>
     */
    public function australianPhoneValidationRules(): array
    {
        return [
            'required',
            'string',
            'max:20',
            function (string $attribute, mixed $value, \Closure $fail): void {
                if (! is_string($value) || ! $this->isValidAustralianPhone($value)) {
                    $fail('Please enter a valid Australian phone number (10 digits, with or without the leading 0).');
                }
            },
        ];
    }

    /**
     * Search clients and leads by phone (and optionally email).
     *
     * Full Australian numbers match with/without leading 0 and +61 variants.
     * Partial / non-AU input keeps the previous loose LIKE behaviour (staff wizard).
     *
     * Returns a Collection of Admin models (type = client|lead).
     * Excludes soft-deleted and archived records (same active CRM rules as uniqueness checks).
     * When $email is provided the query is ANDed so the result set is narrower.
     */
    public function lookup(string $rawPhone, ?string $email = null): Collection
    {
        $normalized = $this->normalizePhone($rawPhone);
        $phone = $rawPhone;

        if ($normalized === '') {
            return collect();
        }

        $query = Admin::whereIn('type', ['client', 'lead'])
            ->where(function ($q) use ($normalized, $phone, $rawPhone): void {
                $canonical = $this->canonicalAustralianPhone($rawPhone);
                if ($canonical !== '') {
                    $variants = array_values(array_unique(array_merge(
                        Admin::phoneDigitMatchVariants($canonical),
                        Admin::phoneDigitMatchVariants($rawPhone)
                    )));
                    $expr = Admin::phoneDigitsSqlExpression('phone');
                    $placeholders = implode(',', array_fill(0, count($variants), '?'));
                    $q->whereRaw("{$expr} IN ({$placeholders})", $variants);

                    return;
                }

                $expr = Admin::phoneDigitsSqlExpression('phone');
                $q->whereRaw("{$expr} LIKE ?", ['%'.$normalized.'%']);
                $trimmed = trim($phone);
                if ($trimmed !== '') {
                    $q->orWhere('phone', 'like', '%'.addcslashes($trimmed, '%_\\').'%');
                }
            });

        if (! empty($email)) {
            $query->where('email', 'like', '%'.trim($email).'%');
        }

        // Same active-record rules as CRM uniqueness/lists: exclude deleted + archived.
        Admin::constrainToActiveCrmRecords($query);

        return $query
            ->with('company')
            ->orderByRaw("CASE WHEN type = 'client' THEN 0 ELSE 1 END")
            ->limit(20)
            ->get();
    }

    /**
     * Format a single Admin for the wizard summary card.
     */
    public function formatForWizard(Admin $record): array
    {
        return [
            'id' => $record->id,
            'type' => $record->type,
            'name' => $record->display_name,
            'email' => $record->email,
            'phone' => $record->phone,
            'is_company' => (bool) $record->is_company,
            'company_name' => $record->company?->company_name,
        ];
    }
}
