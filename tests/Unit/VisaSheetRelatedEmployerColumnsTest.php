<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class VisaSheetRelatedEmployerColumnsTest extends TestCase
{
    #[Test]
    public function non_sponsor_sheets_no_longer_render_related_employer_or_file_status(): void
    {
        $sheet = file_get_contents(resource_path('views/crm/clients/sheets/visa-type-sheet.blade.php'));
        $this->assertIsString($sheet);

        $this->assertStringNotContainsString('<th>Related Employer</th>', $sheet);
        $this->assertStringNotContainsString('<th>File Status</th>', $sheet);
        $this->assertStringNotContainsString('related_employers', $sheet);
        $this->assertStringNotContainsString('employerFileStatus', $sheet);

        // Sponsor-only columns and shared comment column must remain.
        $this->assertStringContainsString('<th>TRN</th>', $sheet);
        $this->assertStringContainsString('<th>Comment</th>', $sheet);
        $this->assertStringContainsString('$showSponsorColumns', $sheet);
    }

    #[Test]
    public function empty_colspan_accounts_for_removed_columns_and_sponsor_extras(): void
    {
        $sheet = file_get_contents(resource_path('views/crm/clients/sheets/visa-type-sheet.blade.php'));
        $this->assertIsString($sheet);

        $this->assertStringContainsString(
            "\$emptyColspan = \$tab === 'checklist' ? 12 : (\$tab === 'discontinue' ? 11 : 9);",
            $sheet
        );
        $this->assertStringContainsString('$emptyColspan += 6;', $sheet);
    }

    #[Test]
    public function controller_no_longer_attaches_related_employers(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/CRM/VisaTypeSheetController.php'));
        $this->assertIsString($controller);

        $this->assertStringNotContainsString('attachRelatedEmployers', $controller);
        $this->assertStringNotContainsString('related_employers', $controller);
    }
}
