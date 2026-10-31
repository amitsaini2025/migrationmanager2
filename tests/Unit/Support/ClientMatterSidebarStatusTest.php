<?php

namespace Tests\Unit\Support;

use App\Support\ClientMatterSidebarStatus;
use PHPUnit\Framework\TestCase;

class ClientMatterSidebarStatusTest extends TestCase
{
    public function test_open_matter_badge_is_the_current_stage(): void
    {
        $this->assertSame(
            'Checklist',
            ClientMatterSidebarStatus::badge(1, 'Checklist')
        );
        $this->assertSame('', ClientMatterSidebarStatus::optionSuffix(1));
        $this->assertSame(0, ClientMatterSidebarStatus::sortRank(1, 'Checklist'));
    }

    public function test_closed_stage_keeps_its_name(): void
    {
        $this->assertSame(
            'File Closed',
            ClientMatterSidebarStatus::badge(1, 'File Closed')
        );
        $this->assertSame('', ClientMatterSidebarStatus::optionSuffix(1));
        $this->assertSame(1, ClientMatterSidebarStatus::sortRank(1, 'File Closed'));
        $this->assertSame(1, ClientMatterSidebarStatus::sortRank(1, 'Withdrawn'));
        $this->assertSame(1, ClientMatterSidebarStatus::sortRank(1, 'Refund'));
    }

    public function test_discontinued_matter_names_the_stage_it_stopped_at(): void
    {
        $this->assertSame(
            'Discontinued - stopped at Checklist',
            ClientMatterSidebarStatus::badge(0, 'Checklist')
        );
        $this->assertSame(' - Discontinued', ClientMatterSidebarStatus::optionSuffix(0));
        $this->assertSame(2, ClientMatterSidebarStatus::sortRank(0, 'Checklist'));
    }

    public function test_discontinued_without_a_stage_is_just_discontinued(): void
    {
        $this->assertSame('Discontinued', ClientMatterSidebarStatus::badge(0, null));
        $this->assertSame('Discontinued', ClientMatterSidebarStatus::badge(0, 'Discontinued'));
        $this->assertSame('N/A', ClientMatterSidebarStatus::badge(1, null));
    }
}
