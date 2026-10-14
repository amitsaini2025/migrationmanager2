<?php

namespace App\Services\VisaPricing;

use Illuminate\Support\Facades\Cache;

class VisaPricingDataRepository
{
    /**
     * @return list<array<string, mixed>>
     */
    public function visas(): array
    {
        $visas = [];

        if ($this->googleSheetsEnabled()) {
            $cached = Cache::get($this->cacheKey());
            if (is_array($cached) && ! empty($cached['visas']) && is_array($cached['visas'])) {
                $visas = $cached['visas'];
            }
        }

        if ($visas === []) {
            $visas = config('visa_pricing_estimator.visas', []);
        }

        return array_map(
            static fn (array $visa): array => VisaPricingVisaRecord::normalize($visa),
            $visas,
        );
    }

    public function additionalApplicantCharge18Plus(): int
    {
        return (int) config('visa_pricing_estimator.additional_applicant_charge_18_plus', 4685);
    }

    public function additionalApplicantChargeU18(): int
    {
        return (int) config('visa_pricing_estimator.additional_applicant_charge_u18', 2345);
    }

    public function googleSheetsEnabled(): bool
    {
        return (bool) config('visa_pricing_estimator.google_sheets.enabled', false);
    }

    public function cacheKey(): string
    {
        return (string) config('visa_pricing_estimator.google_sheets.cache_key', 'visa_pricing_estimator.google_sheets_payload');
    }

    /**
     * @param  list<array<string, mixed>>  $visas
     */
    public function storeSyncedVisas(array $visas): void
    {
        Cache::forever($this->cacheKey(), [
            'visas' => $visas,
            'synced_at' => now()->toIso8601String(),
        ]);
    }
}
