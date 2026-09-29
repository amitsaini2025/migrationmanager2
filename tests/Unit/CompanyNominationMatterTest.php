<?php

namespace Tests\Unit;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CompanyNominationMatterTest extends TestCase
{
    #[Test]
    public function company_edit_nomination_form_includes_optional_matter_dropdown(): void
    {
        $blade = file_get_contents($this->projectPath('resources/views/crm/clients/company_edit.blade.php'));
        Assert::assertNotFalse($blade);

        Assert::assertStringContainsString('nomination_client_matter_ids[]', $blade);
        Assert::assertStringContainsString('Select matter (optional)', $blade);
        Assert::assertStringContainsString('buildNominationMatterSelectHtml', $blade);
        Assert::assertStringContainsString('window.companyActiveClientMatters', $blade);
        Assert::assertStringContainsString('nomination_nominated_client_ids[]', $blade);
    }

    #[Test]
    public function nominations_save_handler_persists_client_matter_id(): void
    {
        $controller = file_get_contents($this->projectPath('app/Http/Controllers/CRM/ClientPersonalDetailsController.php'));
        Assert::assertNotFalse($controller);

        Assert::assertStringContainsString('nomination_client_matter_ids', $controller);
        Assert::assertStringContainsString("'client_matter_id' => \$clientMatterId", $controller);
    }

    #[Test]
    public function company_nomination_model_supports_client_matter_link(): void
    {
        $model = file_get_contents($this->projectPath('app/Models/CompanyNomination.php'));
        Assert::assertNotFalse($model);

        Assert::assertStringContainsString("'client_matter_id'", $model);
        Assert::assertStringContainsString('function clientMatter()', $model);
    }

    #[Test]
    public function company_details_nomination_card_shows_matter_when_set(): void
    {
        $blade = file_get_contents($this->projectPath('resources/views/crm/companies/tabs/company_details.blade.php'));
        Assert::assertNotFalse($blade);

        Assert::assertStringContainsString('$nom->clientMatter', $blade);
        Assert::assertStringContainsString('field-label">Matter:', $blade);
        Assert::assertStringContainsString('dropdownLabel()', $blade);
    }

    #[Test]
    public function client_edit_service_loads_active_matters_for_company_nomination_dropdown(): void
    {
        $service = file_get_contents($this->projectPath('app/Services/ClientEditService.php'));
        Assert::assertNotFalse($service);

        Assert::assertStringContainsString('activeClientMattersForNomination', $service);
        Assert::assertStringContainsString('company.nominations.clientMatter.matter', $service);
        Assert::assertStringContainsString('getActiveClientMattersForNomination', $service);
    }

    private function projectPath(string $relative): string
    {
        return dirname(__DIR__, 2).DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relative);
    }
}
