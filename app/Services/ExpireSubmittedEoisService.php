<?php

namespace App\Services;

use App\Models\ClientEoiReference;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class ExpireSubmittedEoisService
{
    public const YEARS_UNTIL_EXPIRY = 2;

    /**
     * Mark submitted EOIs as expired once their submission date is at least 2 years ago.
     * Only touches status=submitted so invited/nominated/etc. stay unchanged.
     */
    public function expire(?CarbonInterface $asOf = null): int
    {
        $cutoff = ($asOf ?? Carbon::now())->copy()->subYears(self::YEARS_UNTIL_EXPIRY)->toDateString();

        return ClientEoiReference::query()
            ->where('eoi_status', ClientEoiReference::STATUS_SUBMITTED)
            ->whereNotNull('EOI_submission_date')
            ->whereDate('EOI_submission_date', '<=', $cutoff)
            ->update(['eoi_status' => ClientEoiReference::STATUS_EXPIRED]);
    }

    /**
     * Display/API helper: treat overdue submitted EOIs as expired even before the job runs.
     */
    public function effectiveStatus(?string $status, mixed $submissionDate, ?CarbonInterface $asOf = null): ?string
    {
        if ($status !== ClientEoiReference::STATUS_SUBMITTED || blank($submissionDate)) {
            return $status;
        }

        $submission = $submissionDate instanceof CarbonInterface
            ? $submissionDate->copy()->startOfDay()
            : Carbon::parse($submissionDate)->startOfDay();

        $cutoff = ($asOf ?? Carbon::now())->copy()->subYears(self::YEARS_UNTIL_EXPIRY)->startOfDay();

        if ($submission->lessThanOrEqualTo($cutoff)) {
            return ClientEoiReference::STATUS_EXPIRED;
        }

        return $status;
    }
}
