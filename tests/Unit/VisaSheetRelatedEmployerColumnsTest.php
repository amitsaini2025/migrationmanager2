<?php

namespace Tests\Unit;

use App\Http\Controllers\CRM\VisaTypeSheetController;
use PHPUnit\Framework\Attributes\Test;
use ReflectionMethod;
use Tests\TestCase;

class VisaSheetRelatedEmployerColumnsTest extends TestCase
{
    #[Test]
    public function related_employer_columns_are_gated_to_partner_and_parents_only(): void
    {
        $sheet = file_get_contents(resource_path('views/crm/clients/sheets/visa-type-sheet.blade.php'));
        $this->assertIsString($sheet);

        $this->assertStringContainsString('$showRelatedEmployerColumns', $sheet);
        $this->assertStringContainsString("['partner', 'parents']", $sheet);
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
    public function controller_attaches_related_employers_except_partner_and_parents(): void
    {
        $controller = new class extends VisaTypeSheetController
        {
            public function shouldAttach(string $visaType, array $config): bool
            {
                return $this->shouldAttachRelatedEmployers($visaType, $config);
            }
        };

        $this->assertTrue($controller->shouldAttach('tr', ['sponsor_sheet' => false]));
        $this->assertTrue($controller->shouldAttach('visitor', ['sponsor_sheet' => false]));
        $this->assertTrue($controller->shouldAttach('student', ['sponsor_sheet' => false]));
        $this->assertFalse($controller->shouldAttach('partner', ['sponsor_sheet' => false]));
        $this->assertFalse($controller->shouldAttach('parents', ['sponsor_sheet' => false]));
        $this->assertFalse($controller->shouldAttach('employer', ['sponsor_sheet' => true]));

        $source = file_get_contents(app_path('Http/Controllers/CRM/VisaTypeSheetController.php'));
        $this->assertIsString($source);
        $this->assertStringContainsString('attachRelatedEmployers', $source);

        $method = new ReflectionMethod(VisaTypeSheetController::class, 'attachRelatedEmployers');
        $this->assertTrue($method->isProtected());
    }
}
