<?php

namespace App\Support;

use Carbon\Carbon;
use Carbon\CarbonInterface;

/**
 * Status of a matter's Labour Market Testing record from the fields already stored.
 *
 * Ready and Expired treat today as the nomination lodgement day. The score does
 * not check advertisement count, reach, or file content.
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

    /** Applications must stay open for at least four weeks from first publication. */
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
     * @return array{key: string, label: string, detail: string, span_days: int|null}
     */
    public static function assess(mixed $required, mixed $start, mixed $end, ?CarbonInterface $today = null): array
    {
        $today = ($today ?? Carbon::today())->copy()->startOfDay();
        $requiredFlag = self::requiredFlag($required);
        $startDate = self::dateOrNull($start);
        $endDate = self::dateOrNull($end);

        if ($requiredFlag === false) {
            return self::result(self::NOT_REQUIRED, null, 'Marked as not required for this matter.');
        }

        if ($requiredFlag === null && $startDate === null && $endDate === null) {
            return self::result(self::NOT_RECORDED, null, 'No Labour Market Testing details have been saved.');
        }

        if ($startDate === null || $endDate === null) {
            return self::result(self::INCOMPLETE, null, 'Start date and end date are both required.');
        }

        if ($endDate->lt($startDate)) {
            return self::result(self::INCOMPLETE, null, 'End date is before the start date.');
        }

        $spanDays = (int) $startDate->diffInDays($endDate, true);
        if ($spanDays < self::MIN_ADVERTISING_DAYS) {
            return self::result(
                self::TOO_SHORT,
                $spanDays,
                'Advertising must stay open for at least '.self::MIN_ADVERTISING_DAYS.' days.'
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
            'The '.self::MIN_ADVERTISING_DAYS.'-day window has finished and still falls inside the '.self::LODGEMENT_WINDOW_MONTHS.' months before today.'
        );
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
