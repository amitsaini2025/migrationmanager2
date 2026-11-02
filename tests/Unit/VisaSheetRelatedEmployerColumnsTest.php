<?php

namespace Tests\Unit;

use App\Http\Controllers\CRM\VisaTypeSheetController;
use PHPUnit\Framework\Attributes\Test;
use ReflectionMethod;
use Tests\TestCase;

class VisaSheetRelatedEmployerColumnsTest extends TestCase
{
    #[Test]
    public function related_employer_columns_remain_in_shared_sheet_markup(): void
    {
        $sheet = file_get_contents(resource_path('views/crm/clients/sheets/visa-type-sheet.blade.php'));
        $this->assertIsString($sheet);

        $this->assertStringContainsString('$showRelatedEmployerColumns', $sheet);
        $this->assertStringContainsString('<th>Related Employer</th>', $sheet);
        $this->assertStringContainsString('<th>File Status</th>', $sheet);
        $this->assertStringContainsString('related_employers', $sheet);

        // Sponsor-only columns and shared comment column must remain.
        $this->assertStringContainsString('<th>TRN</th>', $sheet);
        $this->assertStringContainsString('<th>Comment</th>', $sheet);
    }

    #[Test]
    public function empty_colspan_adds_related_employer_columns_when_shown(): void
    {
        $sheet = file_get_contents(resource_path('views/crm/clients/sheets/visa-type-sheet.blade.php'));
        $this->assertIsString($sheet);

        $this->assertStringContainsString(
            "\$emptyColspan = \$tab === 'checklist' ? 12 : (\$tab === 'discontinue' ? 11 : 9);",
            $sheet
        );
        $this->assertStringContainsString('$emptyColspan += 6;', $sheet);
        $this->assertStringContainsString('elseif ($showRelatedEmployerColumns)', $sheet);
        $this->assertStringContainsString('$emptyColspan += 2;', $sheet);
    }

    #[Test]
    public function related_employer_columns_hidden_only_on_requested_sheets(): void
    {
        $controller = new class extends VisaTypeSheetController
        {
            public function shouldShow(string $visaType, array $config): bool
            {
                return $this->shouldShowRelatedEmployerColumns($visaType, $config);
            }
        };

        $nonSponsor = ['sponsor_sheet' => false];

        // Hidden on the requested sheets (plus Partner/Parents already gated).
        $this->assertFalse($controller->shouldShow('tr', $nonSponsor));
        $this->assertFalse($controller->shouldShow('visitor', $nonSponsor));
        $this->assertFalse($controller->shouldShow('student', $nonSponsor));
        $this->assertFalse($controller->shouldShow('partner', $nonSponsor));
        $this->assertFalse($controller->shouldShow('parents', $nonSponsor));

        // Still shown on other non-sponsor sheets.
        $this->assertTrue($controller->shouldShow('pr', $nonSponsor));
        $this->assertTrue($controller->shouldShow('art-matters', $nonSponsor));
        $this->assertTrue($controller->shouldShow('employer-sponsored', $nonSponsor));

        // Sponsor sheet never shows these columns.
        $this->assertFalse($controller->shouldShow('employer', ['sponsor_sheet' => true]));

        $source = file_get_contents(app_path('Http/Controllers/CRM/VisaTypeSheetController.php'));
        $this->assertIsString($source);
        $this->assertStringContainsString("['tr', 'visitor', 'student', 'partner', 'parents']", $source);
        $this->assertStringContainsString('attachRelatedEmployers', $source);

        $method = new ReflectionMethod(VisaTypeSheetController::class, 'attachRelatedEmployers');
        $this->assertTrue($method->isProtected());
    }
}
