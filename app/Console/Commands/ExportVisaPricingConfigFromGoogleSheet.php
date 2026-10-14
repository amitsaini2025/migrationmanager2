<?php

namespace App\Console\Commands;

use App\Services\VisaPricing\GoogleSheetsVisaPricingFetcher;
use App\Services\VisaPricing\VisaPricingSheetParser;
use Illuminate\Console\Command;

class ExportVisaPricingConfigFromGoogleSheet extends Command
{
    protected $signature = 'visa-pricing:export-config-from-google-sheet';

    protected $description = 'Overwrite config/visa_pricing_estimator.php visas array from Google Sheets';

    public function handle(
        GoogleSheetsVisaPricingFetcher $fetcher,
        VisaPricingSheetParser $parser,
    ): int {
        $config = config('visa_pricing_estimator.google_sheets', []);
        $spreadsheetId = (string) ($config['spreadsheet_id'] ?? '');
        $sheetGid = (int) ($config['sheet_gid'] ?? 0);
        $serviceAccountPath = (string) ($config['service_account_path'] ?? '');

        if ($spreadsheetId === '' || $sheetGid <= 0 || $serviceAccountPath === '') {
            $this->error('Google Sheets sync is not configured.');

            return self::FAILURE;
        }

        $rows = $fetcher->fetchRows($spreadsheetId, $sheetGid, $serviceAccountPath);
        $visas = $parser->parse($rows);

        $path = config_path('visa_pricing_estimator.php');
        $contents = file_get_contents($path);
        if ($contents === false) {
            $this->error('Unable to read config file.');

            return self::FAILURE;
        }

        $header = $this->buildConfigHeader();
        $visasExport = $this->exportVisasArray($visas);
        $newContents = $header.$visasExport."\n    ],\n];\n";

        file_put_contents($path, $newContents);

        $this->info('Updated '.$path.' with '.count($visas).' visa rows from Google Sheets.');

        return self::SUCCESS;
    }

    private function buildConfigHeader(): string
    {
        return <<<'PHP'
<?php

/**
 * Visa Pricing Estimator Configuration
 *
 * Visa rows are aligned with the Australian_Visa_Application_Charges Google Sheet.
 * Regenerate with: php artisan visa-pricing:export-config-from-google-sheet
 *
 * Prices are in AUD.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Default charges for additional applicants (fallback when row has no fee)
    |--------------------------------------------------------------------------
    */
    'additional_applicant_charge_18_plus' => 4685,
    'additional_applicant_charge_u18' => 2345,

    /*
    |--------------------------------------------------------------------------
    | Google Sheets sync (optional — APIs fall back to the visas array below)
    |--------------------------------------------------------------------------
    */
    'google_sheets' => [
        'enabled' => filter_var(env('VISA_PRICING_GOOGLE_SHEETS_ENABLED', false), FILTER_VALIDATE_BOOL),
        'spreadsheet_id' => env('VISA_PRICING_GOOGLE_SPREADSHEET_ID', '19rIOHPLH-SZZfhworCr5_f1kRGSUx19wjJa3lt2ncjs'),
        'sheet_gid' => (int) env('VISA_PRICING_GOOGLE_SHEET_GID', 29243613),
        'service_account_path' => env('VISA_PRICING_GOOGLE_SERVICE_ACCOUNT_PATH', env('FCM_SERVICE_ACCOUNT_PATH', 'firebase-service-account.json')),
        'cache_key' => 'visa_pricing_estimator.google_sheets_payload',
        'min_visa_rows' => (int) env('VISA_PRICING_GOOGLE_MIN_VISA_ROWS', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Visa list (sheet columns: id, category, subclass, visa_stream, fees, etc.)
    |--------------------------------------------------------------------------
    */
    'visas' => [

PHP;
    }

    /**
     * @param  list<array<string, mixed>>  $visas
     */
    private function exportVisasArray(array $visas): string
    {
        $blocks = [];

        foreach ($visas as $visa) {
            $blocks[] = $this->exportVisaRow($visa);
        }

        return implode("\n", $blocks);
    }

    /**
     * @param  array<string, mixed>  $visa
     */
    private function exportVisaRow(array $visa): string
    {
        $lines = [
            '        [',
            "            'id' => '".$visa['id']."',",
            "            'category' => ".$this->exportString($visa['category']).',',
            "            'subclass' => ".$this->exportString($visa['subclass']).',',
            "            'visa_stream' => ".$this->exportString($visa['visa_stream']).',',
            "            'main_applicant_fee' => ".$this->exportString((string) $visa['main_applicant_fee']).',',
            "            'additional_applicant_18_plus_fee' => ".$this->exportNullableString(
                isset($visa['additional_applicant_18_plus_fee']) ? (string) $visa['additional_applicant_18_plus_fee'] : null,
            ).',',
            "            'applicant_under_18_fee' => ".$this->exportNullableString(
                isset($visa['applicant_under_18_fee']) ? (string) $visa['applicant_under_18_fee'] : null,
            ).',',
            "            'second_vac_charges' => ".$this->exportNullableString(
                isset($visa['second_vac_charges']) ? (string) $visa['second_vac_charges'] : null,
            ).',',
            "            'notes' => ".$this->exportNullableString($visa['notes']).',',
            "            'source' => ".$this->exportNullableString($visa['source']).',',
            '        ],',
        ];

        return implode("\n", $lines);
    }

    private function exportString(string $value): string
    {
        return var_export($value, true);
    }

    private function exportNullableString(?string $value): string
    {
        return $value === null ? 'null' : var_export($value, true);
    }
}
