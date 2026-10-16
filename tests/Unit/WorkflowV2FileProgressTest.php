<?php

namespace Tests\Unit;

use App\Support\WorkflowV2Display;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WorkflowV2FileProgressTest extends TestCase
{
    #[Test]
    public function first_stage_shows_zero_percent(): void
    {
        $this->assertSame(0, WorkflowV2Display::fileProgressPercentage(1, 13, false));
    }

    #[Test]
    public function middle_stage_uses_completed_prior_stages_only(): void
    {
        $this->assertSame(31, WorkflowV2Display::fileProgressPercentage(5, 13, false));
        $this->assertSame(85, WorkflowV2Display::fileProgressPercentage(12, 13, false));
    }

    #[Test]
    public function last_workflow_stage_shows_one_hundred_percent(): void
    {
        $this->assertSame(100, WorkflowV2Display::fileProgressPercentage(13, 13, true));
    }

    #[Test]
    public function penultimate_stage_is_unchanged_when_not_marked_as_last(): void
    {
        $this->assertSame(85, WorkflowV2Display::fileProgressPercentage(12, 13, false));
    }

    #[Test]
    public function empty_or_unknown_stage_index_is_zero(): void
    {
        $this->assertSame(0, WorkflowV2Display::fileProgressPercentage(0, 13, false));
        $this->assertSame(0, WorkflowV2Display::fileProgressPercentage(5, 0, false));
    }
}
