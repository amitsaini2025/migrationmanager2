<?php

namespace Tests\Unit;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NomineeNominationsVisibilityTest extends TestCase
{
    #[Test]
    public function personal_details_shows_nominee_nominations_without_requiring_employer_file_access(): void
    {
        $trait = file_get_contents($this->projectPath('app/Traits/ClientCrmFollowups.php'));
        Assert::assertNotFalse($trait);

        Assert::assertStringContainsString('function visibleNomineeNominationsForViewer', $trait);
        Assert::assertStringContainsString(
            '[$visibleNomineeNominations, $openableEmployerAdminIds] = $this->visibleNomineeNominationsForViewer',
            $trait
        );
        Assert::assertStringContainsString(
            'StaffClientVisibility::canViewClientProfile($companyAdminId, $user)',
            $trait
        );
        Assert::assertStringContainsString("return [\$nominations, array_map('intval', array_keys(\$openableIds))];", $trait);

        $blade = file_get_contents($this->projectPath('resources/views/crm/clients/tabs/personal_details.blade.php'));
        Assert::assertNotFalse($blade);

        Assert::assertStringContainsString('Nominated by employer', $blade);
        Assert::assertStringContainsString('$canOpenEmployer', $blade);
        Assert::assertStringContainsString('in_array((int) $companyAdminId, $openableEmployerAdminIds, true)', $blade);
        Assert::assertStringContainsString(
            'Employer profile links are shown only for companies you are allowed to open.',
            $blade
        );
    }

    private function projectPath(string $relative): string
    {
        return dirname(__DIR__, 2).DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relative);
    }
}
