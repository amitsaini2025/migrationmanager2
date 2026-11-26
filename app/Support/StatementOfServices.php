<?php

namespace App\Support;

use App\Models\ClientAddress;
use App\Services\InvoicePaymentSyncService;
use Illuminate\Support\Facades\DB;

/**
 * Matter-level statement of services: every final invoice line,
 * with the charge reduced by any office adjustment on that invoice.
 */
final class StatementOfServices
{
    /**
     * @param  iterable<int, object>  $rows
     * @param  array<string, float>  $officeDiscountByInvoice
     * @return list<array{date: string, description: string, invoice_no: string, charge: float}>
     */
    public static function lines(iterable $rows, array $officeDiscountByInvoice): array
    {
        $built = [];
        foreach ($rows as $row) {
            $invoiceNo = self::invoiceKey($row);
            $built[] = [
                'date' => trim((string) ($row->trans_date ?? '')),
                'description' => trim((string) ($row->description ?? '')),
                'invoice_no' => $invoiceNo,
                'charge' => self::signedCharge($row),
            ];
        }

        $remaining = [];
        foreach ($officeDiscountByInvoice as $invoiceNo => $amount) {
            $remaining[(string) $invoiceNo] = round(max(0.0, (float) $amount), 2);
        }

        foreach ($built as $index => $line) {
            $invoiceNo = $line['invoice_no'];
            if ($invoiceNo === '' || ! isset($remaining[$invoiceNo]) || $remaining[$invoiceNo] <= 0) {
                continue;
            }

            if ($line['charge'] <= 0) {
                continue;
            }

            $applied = min($line['charge'], $remaining[$invoiceNo]);
            $built[$index]['charge'] = round($line['charge'] - $applied, 2);
            $remaining[$invoiceNo] = round($remaining[$invoiceNo] - $applied, 2);
        }

        return self::sortByServiceDate($built);
    }

    /**
     * @return array{
     *     client_name: string,
     *     client_address_lines: list<string>,
     *     client_matter_display: string,
     *     client_matter_no: string,
     *     migration_agent_name: string|null,
     *     migration_agent_marn: string|null,
     *     statement_date: string,
     *     lines: list<array{date: string, description: string, invoice_no: string, charge: float}>,
     *     total: float
     * }|null
     */
    public static function forMatter(int $clientId, int $matterId): ?array
    {
        if ($clientId <= 0 || $matterId <= 0) {
            return null;
        }

        $client = DB::table('admins')->where('id', $clientId)->first();
        if (! $client) {
            return null;
        }

        $matter = DB::table('client_matters')
            ->leftJoin('matters', 'matters.id', '=', 'client_matters.sel_matter_id')
            ->where('client_matters.id', $matterId)
            ->where('client_matters.client_id', $clientId)
            ->select('client_matters.client_unique_matter_no', 'matters.title as matter_name')
            ->first();

        if (! $matter) {
            return null;
        }

        $rows = DB::table('account_client_receipts')
            ->where('client_id', $clientId)
            ->where('client_matter_id', $matterId)
            ->where('receipt_type', 3)
            ->whereRaw("LOWER(COALESCE(save_type, '')) = ?", ['final'])
            ->where(function ($query): void {
                $query->whereNull('void_invoice')->orWhere('void_invoice', 0);
            })
            ->orderBy('id')
            ->get(['trans_date', 'description', 'withdraw_amount', 'payment_type', 'invoice_no', 'trans_no']);

        $discounts = [];
        $sync = app(InvoicePaymentSyncService::class);
        foreach ($rows as $row) {
            $invoiceNo = self::invoiceKey($row);
            if ($invoiceNo === '' || array_key_exists($invoiceNo, $discounts)) {
                continue;
            }
            $discounts[$invoiceNo] = $sync->sumOfficeDiscountForInvoice($clientId, $invoiceNo);
        }

        $lines = self::lines($rows, $discounts);
        $agent = MatterMigrationAgent::forClientMatter($matterId);

        $matterNo = trim(trim((string) ($client->client_id ?? '')).'-'.trim((string) ($matter->client_unique_matter_no ?? '')), '-');
        $matterName = trim((string) ($matter->matter_name ?? ''));

        return [
            'client_name' => trim(implode(' ', array_filter([
                $client->first_name ?? null,
                $client->last_name ?? null,
            ]))),
            'client_address_lines' => self::clientAddressLines($clientId),
            'client_matter_display' => $matterName !== '' ? $matterName.' ('.$matterNo.')' : $matterNo,
            'client_matter_no' => $matterNo,
            'migration_agent_name' => $agent['agent_name'],
            'migration_agent_marn' => $agent['agent_marn'],
            'statement_date' => now()->format('d/m/Y'),
            'lines' => $lines,
            'total' => round(array_sum(array_column($lines, 'charge')), 2),
        ];
    }

    private static function signedCharge(object $row): float
    {
        $amount = round((float) ($row->withdraw_amount ?? 0), 2);
        $paymentType = strtolower(trim((string) ($row->payment_type ?? '')));
        if ($paymentType === 'discount') {
            return round(-abs($amount), 2);
        }

        return $amount;
    }

    private static function invoiceKey(object $row): string
    {
        $invoiceNo = trim((string) ($row->invoice_no ?? ''));
        if ($invoiceNo !== '') {
            return $invoiceNo;
        }

        return trim((string) ($row->trans_no ?? ''));
    }

    /**
     * @param  list<array{date: string, description: string, invoice_no: string, charge: float}>  $lines
     * @return list<array{date: string, description: string, invoice_no: string, charge: float}>
     */
    private static function sortByServiceDate(array $lines): array
    {
        $indexed = [];
        foreach ($lines as $index => $line) {
            $indexed[] = ['index' => $index, 'line' => $line];
        }

        usort($indexed, function (array $left, array $right): int {
            $leftDate = \DateTimeImmutable::createFromFormat('!d/m/Y', $left['line']['date']) ?: null;
            $rightDate = \DateTimeImmutable::createFromFormat('!d/m/Y', $right['line']['date']) ?: null;

            if ($leftDate instanceof \DateTimeImmutable && $rightDate instanceof \DateTimeImmutable) {
                $compared = $leftDate <=> $rightDate;
                if ($compared !== 0) {
                    return $compared;
                }
            }

            return $left['index'] <=> $right['index'];
        });

        return array_column($indexed, 'line');
    }

    /**
     * @return list<string>
     */
    private static function clientAddressLines(int $clientId): array
    {
        $address = DB::table('client_addresses')
            ->where('client_id', $clientId)
            ->where('is_current', 1)
            ->first();

        if (! $address) {
            $address = DB::table('client_addresses')
                ->where('client_id', $clientId)
                ->orderByRaw(ClientAddress::ORDER_BY_DISPLAY_SQL)
                ->orderByDesc('id')
                ->first();
        }

        if (! $address) {
            return [];
        }

        $lines = [];
        $street = trim((string) ($address->address_line_1 ?? $address->address ?? ''));
        $line2 = trim((string) ($address->address_line_2 ?? ''));
        if ($line2 !== '') {
            $street = $street !== '' ? $street.', '.$line2 : $line2;
        }
        if ($street !== '') {
            $lines[] = $street;
        }

        $locality = trim(implode(' ', array_filter([
            $address->suburb ?? null,
            $address->state ?? null,
            $address->zip ?? null,
        ])));
        if ($locality !== '') {
            $lines[] = $locality;
        }

        $country = trim((string) ($address->country ?? ''));
        if ($country !== '') {
            $lines[] = $country;
        }

        return $lines;
    }
}
