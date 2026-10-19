<?php

namespace App\Support;

use App\Models\WorkflowStage;
use Illuminate\Support\Collection;

/**
 * Resolves next/previous workflow stages by position in the ordered stage list.
 *
 * Matches the Workflow tab UI (order by COALESCE(sort_order, id)) so duplicate or
 * messy sort_order values cannot jump to the wrong stage.
 */
class WorkflowStageNavigation
{
    /**
     * Ordered stages for a matter's workflow (or unscoped fallback when no workflow id).
     *
     * @return Collection<int, WorkflowStage>
     */
    public static function orderedStages(?int $matterWorkflowId, ?int $currentStageWorkflowId = null): Collection
    {
        $query = WorkflowStage::query()->orderByRaw('COALESCE(sort_order, id) ASC')->orderBy('id');

        if ($matterWorkflowId) {
            $query->where('workflow_id', $matterWorkflowId);
        } elseif ($currentStageWorkflowId) {
            $query->where('workflow_id', $currentStageWorkflowId);
        }

        return $query->get();
    }

    /**
     * Next stage after the current id in an already-ordered list (by position, not sort_order math).
     */
    public static function nextAfter(Collection $stages, int $currentStageId): ?WorkflowStage
    {
        return self::adjacent($stages, $currentStageId, 1);
    }

    /**
     * Previous stage before the current id in an already-ordered list.
     */
    public static function previousBefore(Collection $stages, int $currentStageId): ?WorkflowStage
    {
        return self::adjacent($stages, $currentStageId, -1);
    }

    /**
     * 0-based index of a stage id in the ordered list, or null if missing.
     */
    public static function indexOf(Collection $stages, int $stageId): ?int
    {
        $index = $stages->search(fn ($stage) => (int) $stage->id === $stageId);

        return $index === false ? null : (int) $index;
    }

    private static function adjacent(Collection $stages, int $currentStageId, int $delta): ?WorkflowStage
    {
        $index = self::indexOf($stages, $currentStageId);
        if ($index === null) {
            return null;
        }

        $target = $index + $delta;
        if ($target < 0 || $target >= $stages->count()) {
            return null;
        }

        $stage = $stages->values()->get($target);

        return $stage ?: null;
    }
}
