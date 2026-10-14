<?php

namespace App\Services\VisaPricing;

use Illuminate\Support\Facades\Log;

class VisaPricingSyncService
{
    public function __construct(
        private GoogleSheetsVisaPricingFetcher $fetcher,
        private VisaPricingSheetParser $parser,
        private VisaPricingDataRepository $repository,
    ) {}

    public function sync(): bool
    {
        if (! $this->repository->googleSheetsEnabled()) {
            Log::info('Visa pricing Google Sheets sync skipped (disabled).');

            return true;
        }

        $config = config('visa_pricing_estimator.google_sheets', []);
        $spreadsheetId = (string) ($config['spreadsheet_id'] ?? '');
        $sheetGid = (int) ($config['sheet_gid'] ?? 0);
        $serviceAccountPath = (string) ($config['service_account_path'] ?? '');
        $minRows = (int) ($config['min_visa_rows'] ?? 100);

        if ($spreadsheetId === '' || $sheetGid <= 0 || $serviceAccountPath === '') {
            Log::error('Visa pricing Google Sheets sync misconfigured.');

            return false;
        }

        try {
            $rows = $this->fetcher->fetchRows($spreadsheetId, $sheetGid, $serviceAccountPath);
            $visas = $this->parser->parse($rows);

            if (count($visas) < $minRows) {
                Log::error('Visa pricing sheet sync rejected: too few rows', [
                    'parsed' => count($visas),
                    'minimum' => $minRows,
                ]);

                return false;
            }

            $this->repository->storeSyncedVisas($visas);

            Log::info('Visa pricing sheet synced successfully', [
                'visa_count' => count($visas),
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::error('Visa pricing Google Sheets sync failed', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
