<?php

namespace Tests\Unit\Services\VisaPricing;

use App\Services\VisaPricing\VisaPricingDataRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class VisaPricingDataRepositoryTest extends TestCase
{
    #[Test]
    public function it_uses_config_visas_when_google_sheets_disabled(): void
    {
        Config::set('visa_pricing_estimator.google_sheets.enabled', false);
        Config::set('visa_pricing_estimator.visas', [
            ['id' => '99', 'label' => 'Test visa', 'base_charge' => 100],
        ]);

        $repository = new VisaPricingDataRepository;

        $this->assertSame('99', $repository->visas()[0]['id']);
    }

    #[Test]
    public function it_uses_cached_visas_when_google_sheets_enabled(): void
    {
        Config::set('visa_pricing_estimator.google_sheets.enabled', true);
        Config::set('visa_pricing_estimator.google_sheets.cache_key', 'visa_pricing_test_cache');
        Config::set('visa_pricing_estimator.visas', [
            ['id' => '1', 'label' => 'Config fallback', 'base_charge' => 1],
        ]);

        Cache::forever('visa_pricing_test_cache', [
            'visas' => [
                ['id' => '42', 'label' => 'Cached visa', 'base_charge' => 500],
            ],
            'synced_at' => now()->toIso8601String(),
        ]);

        $repository = new VisaPricingDataRepository;

        $this->assertSame('42', $repository->visas()[0]['id']);
    }

    #[Test]
    public function it_falls_back_to_config_when_cache_empty(): void
    {
        Config::set('visa_pricing_estimator.google_sheets.enabled', true);
        Config::set('visa_pricing_estimator.google_sheets.cache_key', 'visa_pricing_empty_cache');
        Config::set('visa_pricing_estimator.visas', [
            ['id' => '7', 'label' => 'Config fallback', 'base_charge' => 10],
        ]);

        Cache::forget('visa_pricing_empty_cache');

        $repository = new VisaPricingDataRepository;

        $this->assertSame('7', $repository->visas()[0]['id']);
    }
}
