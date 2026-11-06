<?php

namespace App\Support;

use DateTimeImmutable;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * Aging buckets for the client invoice list.
 *
 * trans_date is stored as dd/mm/yyyy. Paid and void rows sit outside every
 * bucket, matching the Aging Status badge on the invoice list.
 */
final class InvoiceListAging
{
    private const BUCKETS = ['current', 'warning', 'urgent', 'critical'];

    public static function apply(EloquentBuilder|QueryBuilder $query, mixed $category, ?string $today = null): void
    {
        if (! is_string($category)) {
            return;
        }

        $category = strtolower(trim($category));
        if (! in_array($category, self::BUCKETS, true)) {
            return;
        }

        $today = self::today($today);

        $query->where(function ($inner) {
            $inner->whereNull('void_invoice')
                ->orWhere('void_invoice', '!=', 1);
        });

        $query->where(function ($inner) {
            $inner->whereNull('invoice_status')
                ->orWhere('invoice_status', '!=', 1);
        });

        $query->whereRaw(self::outstandingSql().' > 0');

        [$daysSql, $bindings] = self::daysSql($query, $today);

        match ($category) {
            'current' => $query->whereRaw("({$daysSql}) <= 30", $bindings),
            'warning' => $query->whereRaw("({$daysSql}) > 30 AND ({$daysSql}) <= 60", [...$bindings, ...$bindings]),
            'urgent' => $query->whereRaw("({$daysSql}) > 60 AND ({$daysSql}) <= 90", [...$bindings, ...$bindings]),
            'critical' => $query->whereRaw("({$daysSql}) > 90", $bindings),
        };
    }

    /**
     * @return 'current'|'warning'|'urgent'|'critical'|null
     */
    public static function bucket(object $invoice, ?string $today = null): ?string
    {
        if (self::isVoid($invoice) || self::isPaid($invoice)) {
            return null;
        }

        $days = self::daysOld(isset($invoice->trans_date) ? (string) $invoice->trans_date : null, $today);

        if ($days <= 30) {
            return 'current';
        }

        if ($days <= 60) {
            return 'warning';
        }

        if ($days <= 90) {
            return 'urgent';
        }

        return 'critical';
    }

    /**
     * @return array{class: string, label: string, icon: string, days: string|null}
     */
    public static function presentation(object $invoice, ?string $today = null): array
    {
        if (self::isVoid($invoice)) {
            return [
                'class' => 'badge-void',
                'label' => 'Voided',
                'icon' => 'fa-ban',
                'days' => null,
            ];
        }

        if (self::isPaid($invoice)) {
            return [
                'class' => 'badge-paid',
                'label' => 'Paid',
                'icon' => 'fa-check-circle',
                'days' => null,
            ];
        }

        $days = self::daysOld(isset($invoice->trans_date) ? (string) $invoice->trans_date : null, $today);
        $bucket = self::bucket($invoice, $today);

        $labels = [
            'current' => ['badge-current', 'Current', 'fa-check-circle'],
            'warning' => ['badge-warning', 'Warning', 'fa-exclamation-triangle'],
            'urgent' => ['badge-urgent', 'Urgent', 'fa-exclamation-circle'],
            'critical' => ['badge-critical', 'Critical', 'fa-times-circle'],
        ];

        [$class, $label, $icon] = $labels[$bucket];

        return [
            'class' => $class,
            'label' => $label,
            'icon' => $icon,
            'days' => $days.' days',
        ];
    }

    public static function outstandingAmount(object $invoice): float
    {
        $amount = (float) ($invoice->balance_amount ?? 0);
        if (($invoice->payment_type ?? null) === 'Discount') {
            $amount = abs($amount);
        }

        return $amount;
    }

    public static function daysOld(?string $transDate, ?string $today = null): int
    {
        $parsed = self::parseTransDate($transDate);
        if ($parsed === null) {
            return 0;
        }

        $todayDate = DateTimeImmutable::createFromFormat('!Y-m-d', self::today($today));
        if ($todayDate === false) {
            return 0;
        }

        return (int) $parsed->diff($todayDate)->format('%r%a');
    }

    public static function isVoid(object $invoice): bool
    {
        return (int) ($invoice->void_invoice ?? 0) === 1;
    }

    public static function isPaid(object $invoice): bool
    {
        return (int) ($invoice->invoice_status ?? 0) === 1 || self::outstandingAmount($invoice) <= 0;
    }

    private static function outstandingSql(): string
    {
        return "CASE WHEN payment_type = 'Discount' THEN ABS(COALESCE(balance_amount, 0)) ELSE COALESCE(balance_amount, 0) END";
    }

    /**
     * @return array{0: string, 1: array<int, string>}
     */
    private static function daysSql(EloquentBuilder|QueryBuilder $query, string $today): array
    {
        $driver = $query->getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            return [
                "CASE WHEN trans_date ~ '^[0-9]{2}/[0-9]{2}/[0-9]{4}$' THEN (CAST(? AS DATE) - TO_DATE(trans_date, 'DD/MM/YYYY')) ELSE 0 END",
                [$today],
            ];
        }

        if ($driver === 'sqlite') {
            $iso = "substr(trans_date, 7, 4) || '-' || substr(trans_date, 4, 2) || '-' || substr(trans_date, 1, 2)";

            return [
                "CASE WHEN trans_date GLOB '[0-9][0-9]/[0-9][0-9]/[0-9][0-9][0-9][0-9]' AND julianday({$iso}) IS NOT NULL THEN CAST(julianday(?) - julianday({$iso}) AS INTEGER) ELSE 0 END",
                [$today],
            ];
        }

        return [
            "CASE WHEN trans_date REGEXP '^[0-9]{2}/[0-9]{2}/[0-9]{4}$' THEN DATEDIFF(?, STR_TO_DATE(trans_date, '%d/%m/%Y')) ELSE 0 END",
            [$today],
        ];
    }

    private static function today(?string $today): string
    {
        $today = $today ?: date('Y-m-d');

        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $today) === 1 ? $today : date('Y-m-d');
    }

    private static function parseTransDate(?string $transDate): ?DateTimeImmutable
    {
        $transDate = trim((string) $transDate);
        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $transDate) !== 1) {
            return null;
        }

        $parsed = DateTimeImmutable::createFromFormat('!d/m/Y', $transDate);
        if ($parsed === false) {
            return null;
        }

        $errors = DateTimeImmutable::getLastErrors();
        if (is_array($errors) && (($errors['warning_count'] ?? 0) > 0 || ($errors['error_count'] ?? 0) > 0)) {
            return null;
        }

        return $parsed;
    }
}
