<?php

namespace App\Support;

/**
 * Normalize email attachment filenames for storage-preference matching.
 * Preview and full parse can differ slightly (path prefixes, case, Unicode).
 */
final class EmailAttachmentFilename
{
    /**
     * Strip Outlook NUL / C0 control characters that break HTML attrs and matching.
     */
    public static function sanitize(?string $filename): string
    {
        if ($filename === null) {
            return '';
        }

        $filename = preg_replace('/[\x00-\x1F\x7F]+/u', '', $filename) ?? '';

        return trim($filename);
    }

    public static function normalizeKey(?string $filename): string
    {
        $filename = self::sanitize($filename);
        if ($filename === '') {
            return '';
        }

        $filename = str_replace('\\', '/', $filename);
        $filename = basename($filename);
        $filename = trim($filename);

        if ($filename === '') {
            return '';
        }

        if (class_exists(\Normalizer::class)) {
            $normalized = \Normalizer::normalize($filename, \Normalizer::FORM_C);
            if (is_string($normalized) && $normalized !== '') {
                $filename = $normalized;
            }
        }

        return mb_strtolower($filename, 'UTF-8');
    }
}
