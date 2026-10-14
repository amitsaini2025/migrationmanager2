<?php

namespace App\Services\VisaPricing;

/**
 * Canonical visa pricing row shape (aligned with Google Sheet columns).
 *
 * @phpstan-type VisaPricingRow array{
 *     id: string,
 *     category: string,
 *     subclass: string,
 *     visa_stream: string,
 *     main_applicant_fee: string,
 *     additional_applicant_18_plus_fee: string|null,
 *     applicant_under_18_fee: string|null,
 *     second_vac_charges: string|null,
 *     notes: string|null,
 *     source: string|null
 * }
 */
class VisaPricingVisaRecord
{
    /**
     * Visa list API item — same keys and order as config/visa_pricing_estimator.php rows.
     *
     * @return VisaPricingRow
     */
    public static function toListResponseRow(array $visa): array
    {
        return self::normalize($visa);
    }

    /**
     * @return VisaPricingRow
     */
    public static function normalize(array $visa): array
    {
        if (array_key_exists('main_applicant_fee', $visa)) {
            $subclass = (string) ($visa['subclass'] ?? '');
            $visaStream = (string) ($visa['visa_stream'] ?? '');
            if ($subclass === '') {
                $subclass = self::subclassFromLabel($visaStream);
            }

            return [
                'id' => (string) $visa['id'],
                'category' => (string) ($visa['category'] ?? ''),
                'subclass' => $subclass,
                'visa_stream' => $visaStream,
                'main_applicant_fee' => self::normalizeMainFeeDisplay($visa['main_applicant_fee']),
                'additional_applicant_18_plus_fee' => self::normalizeOptionalFeeDisplay($visa['additional_applicant_18_plus_fee'] ?? null),
                'applicant_under_18_fee' => self::normalizeOptionalFeeDisplay($visa['applicant_under_18_fee'] ?? null),
                'second_vac_charges' => self::normalizeSecondVacCharges($visa['second_vac_charges'] ?? null),
                'notes' => isset($visa['notes']) ? (is_string($visa['notes']) ? $visa['notes'] : null) : null,
                'source' => isset($visa['source']) ? (is_string($visa['source']) ? $visa['source'] : null) : null,
            ];
        }

        $legacyLabel = (string) ($visa['label'] ?? '');

        return [
            'id' => (string) $visa['id'],
            'category' => '',
            'subclass' => self::subclassFromLabel($legacyLabel),
            'visa_stream' => $legacyLabel,
            'main_applicant_fee' => self::normalizeMainFeeDisplay($visa['base_charge'] ?? 0),
            'additional_applicant_18_plus_fee' => self::normalizeOptionalFeeDisplay($visa['additional_18_plus'] ?? null),
            'applicant_under_18_fee' => self::normalizeOptionalFeeDisplay($visa['additional_u18'] ?? null),
            'second_vac_charges' => self::normalizeSecondVacCharges($visa['subsequent_temp_app_charge'] ?? null),
            'notes' => null,
            'source' => null,
        ];
    }

    public static function displayLabel(array $visa): string
    {
        $visa = self::normalize($visa);
        $stream = trim($visa['visa_stream']);
        $subclass = trim($visa['subclass']);

        if ($stream !== '' && $subclass !== '') {
            return $stream.' (subclass '.$subclass.')';
        }

        if ($stream !== '') {
            return $stream;
        }

        if ($subclass !== '') {
            return 'Visa (subclass '.$subclass.')';
        }

        return '';
    }

    public static function subclass(array $visa): string
    {
        $visa = self::normalize($visa);

        if ($visa['subclass'] !== '') {
            return $visa['subclass'];
        }

        return self::subclassFromLabel($visa['visa_stream']);
    }

    public static function stream(array $visa): ?string
    {
        $visa = self::normalize($visa);
        $stream = trim($visa['visa_stream']);

        return $stream !== '' ? $stream : null;
    }

    public static function mainApplicantFee(array $visa): int
    {
        return self::parseFeeAmount(self::normalize($visa)['main_applicant_fee']) ?? 0;
    }

    public static function additionalApplicant18PlusFee(array $visa): ?int
    {
        $normalized = self::normalize($visa);

        return self::parseFeeAmount($normalized['additional_applicant_18_plus_fee']);
    }

    public static function applicantUnder18Fee(array $visa): ?int
    {
        $normalized = self::normalize($visa);

        return self::parseFeeAmount($normalized['applicant_under_18_fee']);
    }

    public static function parseFeeAmount(mixed $value): ?int
    {
        if ($value === null) {
            return null;
        }

        if (is_int($value)) {
            return $value;
        }

        if (is_float($value)) {
            return (int) round($value);
        }

        $trimmed = trim((string) $value);
        if ($trimmed === '') {
            return null;
        }

        $normalized = strtolower($trimmed);
        if (in_array($normalized, ['n/a', 'na', '-', 'nil', 'none'], true)) {
            return null;
        }

        $numeric = preg_replace('/[^0-9.\-]/', '', str_replace(',', '', $trimmed));
        if ($numeric === '' || $numeric === null) {
            return null;
        }

        return (int) round((float) $numeric);
    }

    public static function formatFeeDisplay(int $amount): string
    {
        return '$'.number_format($amount, 0, '.', ',');
    }

    public static function hasAdditionalApplicant18PlusFeeKey(array $visa): bool
    {
        return array_key_exists('additional_applicant_18_plus_fee', $visa)
            || array_key_exists('additional_18_plus', $visa);
    }

    public static function hasApplicantUnder18FeeKey(array $visa): bool
    {
        return array_key_exists('applicant_under_18_fee', $visa)
            || array_key_exists('additional_u18', $visa);
    }

    /**
     * Case-insensitive search across common list fields.
     */
    public static function matchesSearchQuery(array $visa, string $searchLower): bool
    {
        $visa = self::normalize($visa);
        $haystacks = [
            self::displayLabel($visa),
            $visa['category'],
            $visa['subclass'],
            $visa['visa_stream'],
            (string) ($visa['notes'] ?? ''),
        ];

        foreach ($haystacks as $haystack) {
            if ($haystack !== '' && str_contains(mb_strtolower($haystack), $searchLower)) {
                return true;
            }
        }

        return false;
    }

    private static function subclassFromLabel(string $label): string
    {
        if (preg_match('/\(\s*subclass\s*([^)]+)\)/i', $label, $matches)) {
            return trim($matches[1]);
        }

        return '';
    }

    private static function normalizeMainFeeDisplay(mixed $value): string
    {
        $amount = self::parseFeeAmount($value);

        return $amount === null ? '$0' : self::formatFeeDisplay($amount);
    }

    private static function normalizeOptionalFeeDisplay(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $amount = self::parseFeeAmount($value);

        return $amount === null ? null : self::formatFeeDisplay($amount);
    }

    private static function normalizeSecondVacCharges(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_int($value) || is_float($value)) {
            return (string) (int) $value;
        }

        $trimmed = trim((string) $value);

        return $trimmed === '' ? null : $trimmed;
    }
}
