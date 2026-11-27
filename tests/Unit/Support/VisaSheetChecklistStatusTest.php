<?php

namespace Tests\Unit\Support;

use App\Support\VisaSheetChecklistStatus;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class VisaSheetChecklistStatusTest extends TestCase
{
    #[Test]
    public function restores_only_when_returning_to_checklist_from_convert_to_client(): void
    {
        $this->assertTrue(
            VisaSheetChecklistStatus::shouldRestoreActiveAfterReturnToChecklist('Checklist', 'convert_to_client')
        );
        $this->assertTrue(
            VisaSheetChecklistStatus::shouldRestoreActiveAfterReturnToChecklist(' checklist ', 'Convert_To_Client')
        );

        $this->assertFalse(
            VisaSheetChecklistStatus::shouldRestoreActiveAfterReturnToChecklist('Checklist', 'active')
        );
        $this->assertFalse(
            VisaSheetChecklistStatus::shouldRestoreActiveAfterReturnToChecklist('Checklist', 'hold')
        );
        $this->assertFalse(
            VisaSheetChecklistStatus::shouldRestoreActiveAfterReturnToChecklist('Checklist', 'discontinue')
        );
        $this->assertFalse(
            VisaSheetChecklistStatus::shouldRestoreActiveAfterReturnToChecklist(
                'Cost Agreement, form 956 and First email Sent',
                'convert_to_client'
            )
        );
    }
}
