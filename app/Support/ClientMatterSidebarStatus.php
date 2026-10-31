<?php

namespace App\Support;

/**
 * Labels for the client/company matter dropdown and the status pill under it.
 *
 * Open and workflow-closed files keep the stage name (File Closed, Withdrawn, Refund).
 * A file stopped with Discontinue (matter_status = 0) stays in the list and says so,
 * including the stage it was on when it was stopped.
 */
class ClientMatterSidebarStatus
{
    /**
     * Workflow stages that mean the file was finished, not stopped early.
     *
     * @var list<string>
     */
    public const CLOSED_STAGE_NAMES = [
        'file closed',
        'withdrawn',
        'refund',
        'discontinued',
    ];

    public static function badge(?int $matterStatus, ?string $stageName): string
    {
        $stage = trim((string) $stageName);

        if ($matterStatus !== null && (int) $matterStatus === 0) {
            if ($stage === '' || strcasecmp($stage, 'Discontinued') === 0) {
                return 'Discontinued';
            }

            return 'Discontinued - stopped at '.$stage;
        }

        return $stage !== '' ? $stage : 'N/A';
    }

    public static function optionSuffix(?int $matterStatus): string
    {
        if ($matterStatus !== null && (int) $matterStatus === 0) {
            return ' - Discontinued';
        }

        return '';
    }

    /**
     * 0 open, 1 finished on a close stage, 2 stopped with Discontinue.
     */
    public static function sortRank(?int $matterStatus, ?string $stageName): int
    {
        if ($matterStatus !== null && (int) $matterStatus === 0) {
            return 2;
        }

        $stage = strtolower(trim((string) $stageName));
        if ($stage !== '' && in_array($stage, self::CLOSED_STAGE_NAMES, true)) {
            return 1;
        }

        return 0;
    }
}
