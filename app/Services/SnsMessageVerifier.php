<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class SnsMessageVerifier
{
    /**
     * Verify an Amazon SNS JSON message signature.
     *
     * @param  array<string, mixed>  $envelope
     */
    public function verify(array $envelope): void
    {
        $certUrl = (string) ($envelope['SigningCertURL'] ?? '');
        if (! $this->isAllowedCertUrl($certUrl)) {
            throw new RuntimeException('Untrusted SNS signing certificate URL.');
        }

        $signature = base64_decode((string) ($envelope['Signature'] ?? ''), true);
        if ($signature === false || $signature === '') {
            throw new RuntimeException('Invalid SNS signature.');
        }

        $version = (string) ($envelope['SignatureVersion'] ?? '1');
        $algorithm = match ($version) {
            '1' => OPENSSL_ALGO_SHA1,
            '2' => OPENSSL_ALGO_SHA256,
            default => throw new RuntimeException('Unsupported SNS signature version.'),
        };

        $publicKey = openssl_pkey_get_public($this->certificate($certUrl));
        if ($publicKey === false) {
            throw new RuntimeException('Invalid SNS signing certificate.');
        }

        $valid = openssl_verify($this->stringToSign($envelope), $signature, $publicKey, $algorithm);
        if ($valid !== 1) {
            throw new RuntimeException('SNS signature verification failed.');
        }
    }

    /**
     * @param  array<string, mixed>  $envelope
     */
    public function stringToSign(array $envelope): string
    {
        $type = (string) ($envelope['Type'] ?? '');
        $keys = match ($type) {
            'Notification' => ['Message', 'MessageId', 'Timestamp', 'TopicArn', 'Type'],
            'SubscriptionConfirmation', 'UnsubscribeConfirmation' => ['Message', 'MessageId', 'SubscribeURL', 'Timestamp', 'Token', 'TopicArn', 'Type'],
            default => throw new RuntimeException('Unsupported SNS message type.'),
        };

        if ($type === 'Notification' && array_key_exists('Subject', $envelope)) {
            array_splice($keys, 2, 0, ['Subject']);
        }

        $signed = '';
        foreach ($keys as $key) {
            if (! array_key_exists($key, $envelope) || ! is_string($envelope[$key])) {
                throw new RuntimeException('SNS message is missing '.$key.'.');
            }

            $signed .= $key."\n".$envelope[$key]."\n";
        }

        return $signed;
    }

    public function isAllowedSnsUrl(string $url): bool
    {
        $parts = parse_url($url);
        if (! is_array($parts) || ($parts['scheme'] ?? '') !== 'https') {
            return false;
        }

        $host = strtolower((string) ($parts['host'] ?? ''));

        return preg_match('/^sns\.[a-z0-9-]+\.amazonaws\.com(\.cn)?$/', $host) === 1;
    }

    public function isAllowedCertUrl(string $url): bool
    {
        if (! $this->isAllowedSnsUrl($url)) {
            return false;
        }

        $path = parse_url($url, PHP_URL_PATH);

        return is_string($path) && str_ends_with(strtolower($path), '.pem');
    }

    private function certificate(string $certUrl): string
    {
        return Cache::remember('sns-signing-cert:'.hash('sha256', $certUrl), 3600, function () use ($certUrl): string {
            $response = Http::withOptions(['allow_redirects' => false])
                ->timeout(5)
                ->connectTimeout(3)
                ->get($certUrl);

            $body = $response->body();
            if ($response->failed() || ! str_contains($body, 'BEGIN CERTIFICATE')) {
                throw new RuntimeException('Could not download SNS signing certificate.');
            }

            return $body;
        });
    }
}
