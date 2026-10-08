<?php

namespace App\Support;

use Illuminate\Validation\Rule;

class LeadSources
{
    /**
     * Old CRM placeholder values — treat as "not selected" on edit/detail.
     *
     * @return list<string>
     */
    public static function legacyUnsetValues(): array
    {
        return [
            'Others',
            'SubAgent',
            'Sub Agent',
        ];
    }

    /**
     * @return list<string>
     */
    public static function options(): array
    {
        return config('lead_sources', []);
    }

    public static function isLegacyUnset(?string $value): bool
    {
        if ($value === null || $value === '') {
            return true;
        }

        return in_array($value, self::legacyUnsetValues(), true);
    }

    public static function isConfiguredOption(?string $value): bool
    {
        return $value !== null && $value !== '' && in_array($value, self::options(), true);
    }

    /**
     * Value to show on detail/summary pages; null means hide or "Not set".
     */
    public static function displayValue(?string $value): ?string
    {
        if (self::isLegacyUnset($value)) {
            return null;
        }

        return $value;
    }

    /**
     * Value pre-selected on edit dropdown; only configured options are pre-selected.
     */
    public static function selectedValueForEdit(?string $value): ?string
    {
        return self::isConfiguredOption($value) ? $value : null;
    }

    /**
     * Persist source on edit save without clearing external/system-set values when left blank.
     */
    public static function resolveOnSave(?string $submitted, ?string $current): ?string
    {
        $submitted = $submitted === '' ? null : $submitted;

        if ($submitted !== null) {
            return $submitted;
        }

        if (self::isLegacyUnset($current) || self::isConfiguredOption($current)) {
            return null;
        }

        return $current;
    }

    /**
     * @return list<string>
     */
    public static function allowedValues(?string $legacy = null): array
    {
        $allowed = self::options();

        if ($legacy !== null && $legacy !== '' && ! self::isLegacyUnset($legacy) && ! in_array($legacy, $allowed, true)) {
            $allowed[] = $legacy;
        }

        return $allowed;
    }

    /**
     * @return array<int, mixed>
     */
    public static function storeRules(): array
    {
        return ['required', Rule::in(self::options())];
    }

    /**
     * @return array<int, mixed>
     */
    public static function updateRules(?string $current = null): array
    {
        return ['nullable', Rule::in(self::allowedValues(self::isLegacyUnset($current) ? null : $current))];
    }
}
