<?php

namespace App\Services\VisaPricing;

use App\Services\Google\GoogleServiceAccountTokenProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleSheetsVisaPricingFetcher
{
    public function __construct(
        private GoogleServiceAccountTokenProvider $tokenProvider,
    ) {}

    /**
     * @return list<list<string|null>>
     */
    public function fetchRows(string $spreadsheetId, int $sheetGid, string $serviceAccountPath): array
    {
        $accessToken = $this->tokenProvider->getAccessToken(
            $serviceAccountPath,
            GoogleServiceAccountTokenProvider::SHEETS_READONLY_SCOPE,
        );

        if ($accessToken === null) {
            throw new \RuntimeException('Unable to obtain Google Sheets access token.');
        }

        $sheetTitle = $this->resolveSheetTitle($spreadsheetId, $sheetGid, $accessToken);
        $escapedTitle = str_replace("'", "''", $sheetTitle);
        $range = rawurlencode("'{$escapedTitle}'!A:Z");

        $response = Http::timeout(30)
            ->connectTimeout(5)
            ->withToken($accessToken)
            ->acceptJson()
            ->get("https://sheets.googleapis.com/v4/spreadsheets/{$spreadsheetId}/values/{$range}", [
                'majorDimension' => 'ROWS',
                'valueRenderOption' => 'UNFORMATTED_VALUE',
            ]);

        if (! $response->successful()) {
            Log::error('Google Sheets values fetch failed', [
                'spreadsheet_id' => $spreadsheetId,
                'sheet_gid' => $sheetGid,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new \RuntimeException('Failed to fetch visa pricing sheet from Google.');
        }

        $values = $response->json('values');

        return is_array($values) ? $values : [];
    }

    private function resolveSheetTitle(string $spreadsheetId, int $sheetGid, string $accessToken): string
    {
        $response = Http::timeout(15)
            ->connectTimeout(5)
            ->withToken($accessToken)
            ->acceptJson()
            ->get("https://sheets.googleapis.com/v4/spreadsheets/{$spreadsheetId}", [
                'fields' => 'sheets(properties(sheetId,title))',
            ]);

        if (! $response->successful()) {
            throw new \RuntimeException('Failed to load spreadsheet metadata from Google.');
        }

        foreach ($response->json('sheets', []) as $sheet) {
            $properties = $sheet['properties'] ?? [];
            if ((int) ($properties['sheetId'] ?? -1) === $sheetGid) {
                $title = (string) ($properties['title'] ?? '');
                if ($title !== '') {
                    return $title;
                }
            }
        }

        throw new \RuntimeException("Sheet gid {$sheetGid} not found in spreadsheet.");
    }
}
