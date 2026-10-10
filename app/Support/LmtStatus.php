<?php

namespace App\Support;

use Carbon\Carbon;
use Carbon\CarbonInterface;

/**
 * Status of a matter's Labour Market Testing record.
 *
 * Ready and Expired treat today as the nomination lodgement day. Until both
 * advertisements are in use, the status follows the stored start and end dates.
 * After that, it follows the two advertisements. The score does not check
 * publication reach or the wording of the advertisements.
 */
final class LmtStatus
{
    public const NOT_RECORDED = 'not_recorded';

    public const NOT_REQUIRED = 'not_required';

    public const INCOMPLETE = 'incomplete';

    public const TOO_SHORT = 'too_short';

    public const NOT_STARTED = 'not_started';

    public const ADVERTISING = 'advertising';

    public const READY = 'ready';

    public const EXPIRED = 'expired';

    /** Applications must be accepted for at least 28 calendar days, counting the open and close dates. */
    public const MIN_ADVERTISING_DAYS = 28;

    /** Testing must fall in the four months ending on the day a nomination is lodged. */
    public const LODGEMENT_WINDOW_MONTHS = 4;

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return [
            self::EXPIRED => 'Expired',
            self::TOO_SHORT => 'Too short',
            self::INCOMPLETE => 'Incomplete',
            self::ADVERTISING => 'Advertising',
            self::NOT_STARTED => 'Not started',
            self::READY => 'Ready to lodge',
            self::NOT_REQUIRED => 'Not required',
            self::NOT_RECORDED => 'Not recorded',
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $advertisements
     * @return array{key: string, label: string, detail: string, span_days: int|null}
     */
    public static function assess(
        mixed $required,
        mixed $start,
        mixed $end,
        ?CarbonInterface $today = null,
        mixed $useAdvertisements = false,
        array $advertisements = [],
    ): array {
        $today = ($today ?? Carbon::today())->copy()->startOfDay();
        $requiredFlag = self::requiredFlag($required);

        if ($requiredFlag === false) {
            return self::result(self::NOT_REQUIRED, null, 'Marked as not required for this matter.');
        }

        if (self::requiredFlag($useAdvertisements) === true) {
            return self::assessAdvertisements($advertisements, $today);
        }

        return self::assessDates($requiredFlag, $start, $end, $today);
    }

    /**
     * @return array{key: string, label: string, detail: string, span_days: int|null}
     */
    public static function assessRecord(object $record, ?CarbonInterface $today = null): array
    {
        return self::assess(
            $record->lmt_required ?? null,
            $record->lmt_start_date ?? null,
            $record->lmt_end_date ?? null,
            $today,
            $record->lmt_use_advertisements ?? false,
            [
                [
                    'publication' => $record->lmt_ad1_publication ?? null,
                    'opened_on' => $record->lmt_ad1_opened_on ?? null,
                    'closed_on' => $record->lmt_ad1_closed_on ?? null,
                ],
                [
                    'publication' => $record->lmt_ad2_publication ?? null,
                    'opened_on' => $record->lmt_ad2_opened_on ?? null,
                    'closed_on' => $record->lmt_ad2_closed_on ?? null,
                ],
            ],
        );
    }

    /**
     * @return array{key: string, label: string, detail: string, span_days: int|null}
     */
    private static function assessDates(?bool $requiredFlag, mixed $start, mixed $end, Carbon $today): array
    {
        $startDate = self::dateOrNull($start);
        $endDate = self::dateOrNull($end);

        if ($requiredFlag === null && $startDate === null && $endDate === null) {
            return self::result(self::NOT_RECORDED, null, 'No Labour Market Testing details have been saved.');
        }

        if ($startDate === null || $endDate === null) {
            return self::result(self::INCOMPLETE, null, 'Start date and end date are both required.');
        }

        if ($endDate->lt($startDate)) {
            return self::result(self::INCOMPLETE, null, 'End date is before the start date.');
        }

        $spanDays = self::inclusiveDays($startDate, $endDate);
        if ($spanDays < self::MIN_ADVERTISING_DAYS) {
            return self::result(
                self::TOO_SHORT,
                $spanDays,
                'Applications must be accepted for at least '.self::MIN_ADVERTISING_DAYS.' calendar days.'
            );
        }

        $windowOpens = $today->copy()->subMonths(self::LODGEMENT_WINDOW_MONTHS);
        if ($startDate->lt($windowOpens)) {
            return self::result(
                self::EXPIRED,
                $spanDays,
                'The start date is more than '.self::LODGEMENT_WINDOW_MONTHS.' months ago, so this testing cannot be used for a nomination lodged today.'
            );
        }

        if ($startDate->gt($today)) {
            return self::result(self::NOT_STARTED, $spanDays, 'The advertising window has not started.');
        }

        if ($endDate->gte($today)) {
            return self::result(self::ADVERTISING, $spanDays, 'The advertising window is still open.');
        }

        return self::result(
            self::READY,
            $spanDays,
            'The '.self::MIN_ADVERTISING_DAYS.' calendar days have finished and still fall inside the '.self::LODGEMENT_WINDOW_MONTHS.' months before today.'
        );
    }

    /**
     * @param  array<int, array<string, mixed>>  $advertisements
     * @return array{key: string, label: string, detail: string, span_days: int|null}
     */
    private static function assessAdvertisements(array $advertisements, Carbon $today): array
    {
        $present = [];
        foreach ($advertisements as $advertisement) {
            if (! is_array($advertisement)) {
                continue;
            }
            $slot = self::normaliseSlot($advertisement);
            if ($slot['publication'] === '' || $slot['opened_on'] === null) {
                continue;
            }
            $present[] = $slot;
        }

        if (count($present) < 2) {
            return self::result(
                self::INCOMPLETE,
                null,
                'Both advertisements need a publication and the date applications opened.'
            );
        }

        foreach ($present as $slot) {
            if ($slot['closed_on'] !== null && $slot['closed_on']->lt($slot['opened_on'])) {
                return self::result(self::INCOMPLETE, null, 'An advertisement close date is before the date applications opened.');
            }
        }

        $earliest = $present[0]['opened_on'];
        foreach ($present as $slot) {
            if ($slot['opened_on']->lt($earliest)) {
                $earliest = $slot['opened_on'];
            }
        }

        $runs = [];
        $accepting = false;
        foreach ($present as $slot) {
            if ($slot['opened_on']->gt($today)) {
                continue;
            }

            $end = $slot['closed_on'] ?? $today->copy();
            if ($end->gt($today)) {
                $end = $today->copy();
            }
            if ($slot['closed_on'] === null || $slot['closed_on']->gt($today)) {
                $accepting = true;
            }
            $runs[] = ['start' => $slot['opened_on'], 'end' => $end];
        }

        if ($runs === []) {
            return self::result(self::NOT_STARTED, null, 'The advertising window has not started.');
        }

        $spanDays = self::continuousDays($runs);
        if ($spanDays < self::MIN_ADVERTISING_DAYS) {
            if ($accepting) {
                return self::result(
                    self::ADVERTISING,
                    $spanDays,
                    'Applications are still being accepted. '.$spanDays.' of '.self::MIN_ADVERTISING_DAYS.' continuous calendar days have passed.'
                );
            }

            return self::result(
                self::TOO_SHORT,
                $spanDays,
                'Applications must be accepted for at least '.self::MIN_ADVERTISING_DAYS.' continuous calendar days. A gap between advertisements is not added.'
            );
        }

        $windowOpens = $today->copy()->subMonths(self::LODGEMENT_WINDOW_MONTHS);
        if ($earliest->lt($windowOpens)) {
            return self::result(
                self::EXPIRED,
                $spanDays,
                'The earliest open date is more than '.self::LODGEMENT_WINDOW_MONTHS.' months ago, so this testing cannot be used for a nomination lodged today.'
            );
        }

        return self::result(
            self::READY,
            $spanDays,
            'The '.self::MIN_ADVERTISING_DAYS.' calendar days are continuous and still fall inside the '.self::LODGEMENT_WINDOW_MONTHS.' months before today.'
        );
    }

    /**
     * @param  list<array{start: Carbon, end: Carbon}>  $runs
     */
    private static function continuousDays(array $runs): int
    {
        if (count($runs) >= 2 && self::rangesOverlap($runs[0]['start'], $runs[0]['end'], $runs[1]['start'], $runs[1]['end'])) {
            $unionStart = $runs[0]['start']->lte($runs[1]['start']) ? $runs[0]['start'] : $runs[1]['start'];
            $unionEnd = $runs[0]['end']->gte($runs[1]['end']) ? $runs[0]['end'] : $runs[1]['end'];

            return self::inclusiveDays($unionStart, $unionEnd);
        }

        $span = 0;
        foreach ($runs as $run) {
            $span = max($span, self::inclusiveDays($run['start'], $run['end']));
        }

        return $span;
    }

    private static function rangesOverlap(Carbon $startA, Carbon $endA, Carbon $startB, Carbon $endB): bool
    {
        return $startA->lte($endB) && $startB->lte($endA);
    }

    private static function inclusiveDays(Carbon $start, Carbon $end): int
    {
        return (int) $start->diffInDays($end, true) + 1;
    }

    /**
     * @param  array<string, mixed>  $advertisement
     * @return array{publication: string, opened_on: ?Carbon, closed_on: ?Carbon}
     */
    private static function normaliseSlot(array $advertisement): array
    {
        return [
            'publication' => trim((string) ($advertisement['publication'] ?? '')),
            'opened_on' => self::dateOrNull($advertisement['opened_on'] ?? null),
            'closed_on' => self::dateOrNull($advertisement['closed_on'] ?? null),
        ];
    }

    /**
     * @return array{key: string, label: string, detail: string, span_days: int|null}
     */
    private static function result(string $key, ?int $spanDays, string $detail): array
    {
        return [
            'key' => $key,
            'label' => self::options()[$key],
            'detail' => $detail,
            'span_days' => $spanDays,
        ];
    }

    private static function requiredFlag(mixed $required): ?bool
    {
        if ($required === null || $required === '') {
            return null;
        }

        if ($required === true || $required === 1 || $required === '1' || $required === 't' || $required === 'true') {
            return true;
        }

        if ($required === false || $required === 0 || $required === '0' || $required === 'f' || $required === 'false') {
            return false;
        }

        return null;
    }

    private static function dateOrNull(mixed $value): ?Carbon
    {
        if ($value === null || $value === '' || $value === '0000-00-00') {
            return null;
        }

        if ($value instanceof CarbonInterface) {
            return Carbon::instance($value)->startOfDay();
        }

        try {
            return Carbon::parse($value)->startOfDay();
        } catch (\Throwable) {
            return null;
        }
    }
}
