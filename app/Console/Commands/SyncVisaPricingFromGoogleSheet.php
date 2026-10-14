<?php

namespace App\Console\Commands;

use App\Services\VisaPricing\VisaPricingSyncService;
use Illuminate\Console\Command;

class SyncVisaPricingFromGoogleSheet extends Command
{
    protected $signature = 'visa-pricing:sync-google-sheet';

    protected $description = 'Sync visa pricing estimator data from Google Sheets into cache';

    public function handle(VisaPricingSyncService $syncService): int
    {
        if (! config('visa_pricing_estimator.google_sheets.enabled', false)) {
            $this->info('Google Sheets sync is disabled (VISA_PRICING_GOOGLE_SHEETS_ENABLED).');

            return self::SUCCESS;
        }

        $ok = $syncService->sync();

        if (! $ok) {
            $this->error('Visa pricing sync failed. APIs will continue using the last cached or config fallback data.');

            return self::FAILURE;
        }

        $this->info('Visa pricing sync completed successfully.');

        return self::SUCCESS;
    }
}
