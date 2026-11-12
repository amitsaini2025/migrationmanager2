<?php

namespace App\Support;

/**
 * Build and resolve attachment_storage prefs from the email upload modal.
 * Preview and full-parse filenames can differ slightly; normalize for matching.
 */
final class EmailAttachmentStorageMap
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public static function fromList(mixed $decoded): array
    {
        if (! is_array($decoded)) {
            return [];
        }

        $map = [];
        foreach ($decoded as $item) {
            if (! is_array($item)) {
                continue;
            }
            $rawKey = $item['original_filename'] ?? $item['filename'] ?? null;
            if (! is_string($rawKey)) {
                continue;
            }

            $key = EmailAttachmentFilename::sanitize($rawKey);
            if ($key === '') {
                continue;
            }

            $item['original_filename'] = $key;
            if (isset($item['filename']) && is_string($item['filename'])) {
                $item['filename'] = EmailAttachmentFilename::sanitize($item['filename']) ?: $key;
            }

            $map[$key] = $item;

            $normalized = EmailAttachmentFilename::normalizeKey($key);
            if ($normalized !== '') {
                $map[$normalized] = $item;
            }
        }

        return $map;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function fromJson(?string $json): array
    {
        if ($json === null || trim($json) === '') {
            return [];
        }

        $decoded = json_decode($json, true);

        return self::fromList($decoded);
    }

    /**
     * @param  array<string, array<string, mixed>>  $storageMap
     * @return array<string, mixed>|null
     */
    public static function resolve(array $storageMap, string $originalFilename): ?array
    {
        if ($originalFilename !== '' && isset($storageMap[$originalFilename]) && is_array($storageMap[$originalFilename])) {
            return $storageMap[$originalFilename];
        }

        $normalized = EmailAttachmentFilename::normalizeKey($originalFilename);
        if ($normalized !== '' && isset($storageMap[$normalized]) && is_array($storageMap[$normalized])) {
            return $storageMap[$normalized];
        }

        return null;
    }

    /**
     * True when the client sent a usable attachment_storage preference map.
     * An empty list is treated as "no prefs" (not "remove all") so a broken
     * modal submit cannot drop every attachment. Explicit remove-all uses a
     * skip sentinel entry (see email-upload-flow.js).
     *
     * @param  array<string, array<string, mixed>>  $storageMap
     */
    public static function isReviewedList(bool $requestHasAttachmentStorage, array $storageMap = []): bool
    {
        return $requestHasAttachmentStorage && $storageMap !== [];
    }

    /**
     * @param  array<string, mixed>  $storageConfig
     */
    public static function requestsDocumentCopy(array $storageConfig): bool
    {
        return ($storageConfig['storage_type'] ?? '') === 'documents'
            && (int) ($storageConfig['category_id'] ?? 0) > 0;
    }
}
