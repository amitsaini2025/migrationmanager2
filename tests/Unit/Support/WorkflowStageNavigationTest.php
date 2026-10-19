<?php

namespace Tests\Unit\Support;

use App\Models\WorkflowStage;
use App\Support\WorkflowStageNavigation;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WorkflowStageNavigationTest extends TestCase
{
    private function stage(int $id, string $name, int $sortOrder): WorkflowStage
    {
        $stage = new WorkflowStage;
        $stage->id = $id;
        $stage->name = $name;
        $stage->sort_order = $sortOrder;
        $stage->workflow_id = 99;

        return $stage;
    }

    /**
     * @return Collection<int, WorkflowStage>
     */
    private function healthyStages(): Collection
    {
        return collect([
            $this->stage(1, 'Checklist', 1),
            $this->stage(2, 'Sent', 2),
            $this->stage(3, 'Received', 3),
            $this->stage(11, 'Decision Received', 11),
            $this->stage(12, 'Ready to Close', 12),
            $this->stage(13, 'File Closed', 13),
        ]);
    }

    #[Test]
    public function next_after_decision_received_is_ready_to_close(): void
    {
        $next = WorkflowStageNavigation::nextAfter($this->healthyStages(), 11);

        Assert::assertNotNull($next);
        Assert::assertSame(12, (int) $next->id);
        Assert::assertSame('Ready to Close', $next->name);
    }

    #[Test]
    public function next_after_does_not_skip_sibling_with_same_sort_order(): void
    {
        // Old SQL: COALESCE(sort_order,id) > 11 skips Ready to Close (also sort_order 11).
        $stages = collect([
            $this->stage(1, 'Checklist', 1),
            $this->stage(10, 'Decision Received', 11),
            $this->stage(11, 'Ready to Close', 11),
            $this->stage(12, 'File Closed', 12),
        ]);

        $next = WorkflowStageNavigation::nextAfter($stages, 10);

        Assert::assertNotNull($next);
        Assert::assertSame(11, (int) $next->id);
        Assert::assertSame('Ready to Close', $next->name);
    }

    #[Test]
    public function previous_before_uses_list_position(): void
    {
        $stages = collect([
            $this->stage(1, 'Checklist', 1),
            $this->stage(2, 'Sent', 2),
            $this->stage(3, 'Received', 3),
        ]);

        $prev = WorkflowStageNavigation::previousBefore($stages, 3);
        Assert::assertNotNull($prev);
        Assert::assertSame(2, (int) $prev->id);

        Assert::assertNull(WorkflowStageNavigation::previousBefore($stages, 1));
        Assert::assertNull(WorkflowStageNavigation::nextAfter($stages, 3));
    }

    #[Test]
    public function index_of_returns_null_for_unknown_stage(): void
    {
        $stages = collect([
            $this->stage(1, 'Checklist', 1),
        ]);

        Assert::assertSame(0, WorkflowStageNavigation::indexOf($stages, 1));
        Assert::assertNull(WorkflowStageNavigation::indexOf($stages, 999));
        Assert::assertNull(WorkflowStageNavigation::nextAfter($stages, 999));
    }
}
