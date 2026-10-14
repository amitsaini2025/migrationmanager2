<?php

namespace App\Services\Google;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleServiceAccountTokenProvider
{
    public const SHEETS_READONLY_SCOPE = 'https://www.googleapis.com/auth/spreadsheets.readonly';

    public function getAccessToken(string $relativeStoragePath, string $scope): ?string
    {
        $fullPath = storage_path('app/'.ltrim($relativeStoragePath, '/'));

        if (! is_readable($fullPath)) {
            Log::warning('Google service account file not readable', ['path' => $fullPath]);

            return null;
        }

        $data = json_decode((string) file_get_contents($fullPath), true);

        if (! is_array($data) || empty($data['client_email']) || empty($data['private_key'])) {
            Log::warning('Invalid Google service account JSON', ['path' => $fullPath]);

            return null;
        }

        $now = time();
        $jwt = JWT::encode([
            'iss' => $data['client_email'],
            'scope' => $scope,
            'aud' => 'https://oauth2.googleapis.com/token',
            'exp' => $now + 3600,
            'iat' => $now,
        ], $data['private_key'], 'RS256');

        $response = Http::timeout(15)
            ->connectTimeout(5)
            ->asForm()
            ->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);

        if (! $response->successful()) {
            Log::error('Google OAuth token exchange failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        }

        return $response->json('access_token');
    }
}
