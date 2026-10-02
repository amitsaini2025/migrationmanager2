<?php

namespace Tests\Unit;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PortalChecklistRenameTest extends TestCase
{
    #[Test]
    public function client_portal_tab_exposes_rename_checklist_ui_and_route(): void
    {
        $portal = file_get_contents($this->projectPath('resources/views/crm/clients/tabs/client_portal.blade.php'));
        Assert::assertNotFalse($portal);
        Assert::assertStringContainsString('cp-rename-checklist-btn', $portal);
        Assert::assertStringContainsString('id="rename_checklist"', $portal);
        Assert::assertStringContainsString('Rename Portal Checklist', $portal);
        Assert::assertStringContainsString('/rename-portal-checklist', $portal);

        $routes = file_get_contents($this->projectPath('routes/client_portal.php'));
        Assert::assertNotFalse($routes);
        Assert::assertStringContainsString("name('client_portal.renamePortalChecklist')", $routes);
        Assert::assertStringContainsString('renamePortalChecklist', $routes);
    }

    #[Test]
    public function sync_helper_matches_renamed_portal_templates_by_template_name_column(): void
    {
        $sync = file_get_contents($this->projectPath('app/Support/WorkflowStageChecklistSync.php'));
        Assert::assertNotFalse($sync);
        Assert::assertStringContainsString('applyPortalTemplateNameMatch', $sync);
        Assert::assertStringContainsString('portal_template_name', $sync);
        Assert::assertStringContainsString('hasPortalTemplateNameColumn', $sync);
        Assert::assertStringContainsString('resolveClientMatter', $sync);
        Assert::assertStringContainsString('int|object $matter', $sync);
    }

    #[Test]
    public function rename_controller_preserves_template_name_for_sync(): void
    {
        $controller = file_get_contents($this->projectPath('app/Http/Controllers/CRM/ClientPortalController.php'));
        Assert::assertNotFalse($controller);
        Assert::assertStringContainsString('function renamePortalChecklist', $controller);
        Assert::assertStringContainsString("\$update['portal_template_name'] = \$oldName", $controller);
    }

    private function projectPath(string $relative): string
    {
        return dirname(__DIR__, 2).DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relative);
    }
}
