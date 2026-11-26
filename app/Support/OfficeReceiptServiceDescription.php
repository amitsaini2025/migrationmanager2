<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * Section 49(b) service text for office receipts: prefer a real invoice line
 * description when the receipt description is only a matter name or a generic word.
 */
final class OfficeReceiptServiceDescription
{
    /** @var list<string> */
    private const GENERIC_WORDS = [
        'cash',
        'fees',
        'fee',
        'pf',
        'payment',
        'paid',
        'office receipt',
        'office receipt applied',
        'receipt',
        'test',
        'n/a',
        'na',
        'misc',
        'miscellaneous',
    ];

    public static function resolve(
        ?string $receiptDescription,
        ?string $invoiceNo,
        ?int $clientId = null,
        ?string $matterName = null,
    ): string {
        $receiptDescription = trim((string) $receiptDescription);
        $invoiceNo = trim((string) $invoiceNo);

        if ($receiptDescription !== '' && ! self::isGeneric($receiptDescription, $matterName)) {
            return $receiptDescription;
        }

        if ($invoiceNo === '') {
            return $receiptDescription;
        }

        $invoiceService = self::invoiceLineDescription($invoiceNo, $clientId);
        if ($invoiceService !== null && $invoiceService !== '') {
            return $invoiceService;
        }

        return $receiptDescription;
    }

    public static function isGeneric(?string $description, ?string $matterName = null): bool
    {
        $description = trim((string) $description);
        if ($description === '') {
            return true;
        }

        $normalized = mb_strtolower($description);
        $normalized = preg_replace('/\s+/', ' ', $normalized) ?? $normalized;

        if (in_array($normalized, self::GENERIC_WORDS, true)) {
            return true;
        }

        $matterName = trim((string) $matterName);
        if ($matterName !== '') {
            $matterNormalized = mb_strtolower(preg_replace('/\s+/', ' ', $matterName) ?? $matterName);
            if ($normalized === $matterNormalized) {
                return true;
            }
        }

        return false;
    }

    private static function invoiceLineDescription(string $invoiceNo, ?int $clientId): ?string
    {
        $fromLines = self::descriptionsFrom(
            'account_all_invoice_receipts',
            $invoiceNo,
            $clientId
        );
        if ($fromLines !== null) {
            return $fromLines;
        }

        return self::descriptionsFrom('account_client_receipts', $invoiceNo, $clientId);
    }

    private static function descriptionsFrom(string $table, string $invoiceNo, ?int $clientId): ?string
    {
        $query = DB::table($table)
            ->where('receipt_type', 3)
            ->where('invoice_no', $invoiceNo)
            ->whereNotNull('description')
            ->where('description', '!=', '');

        if ($clientId !== null && $clientId > 0) {
            $query->where('client_id', $clientId);
        }

        $preferredTypes = ['Professional Fee', 'Department Charges', 'Disbursements', 'Other Cost', 'Surcharge'];
        $parts = [];

        foreach ($preferredTypes as $paymentType) {
            $lines = (clone $query)
                ->where('payment_type', $paymentType)
                ->orderBy('id')
                ->pluck('description');

            foreach ($lines as $line) {
                $line = trim((string) $line);
                if ($line === '' || self::isGeneric($line) || in_array($line, $parts, true)) {
                    continue;
                }
                $parts[] = $line;
            }
        }

        if ($parts !== []) {
            return implode('; ', $parts);
        }

        $fallback = $query->orderBy('id')->value('description');

        return is_string($fallback) && trim($fallback) !== '' ? trim($fallback) : null;
    }
}
