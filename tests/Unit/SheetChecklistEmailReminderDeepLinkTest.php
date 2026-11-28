<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SheetChecklistEmailReminderDeepLinkTest extends TestCase
{
    #[Test]
    public function sheet_email_reminder_opens_popup_and_prefers_last_checklist_email(): void
    {
        $sheet = file_get_contents(resource_path('views/crm/clients/sheets/visa-type-sheet.blade.php'));
        $this->assertIsString($sheet);

        $this->assertStringNotContainsString('data-emails-compose-url=', $sheet);
        $this->assertStringNotContainsString('compose=checklist_reminder', $sheet);

        $start = strpos($sheet, 'window.openSheetEmailReminder = function($btn)');
        $this->assertNotFalse($start);

        $end = strpos($sheet, 'window.openSheetSmsReminder', $start);
        $this->assertNotFalse($end);

        $handler = substr($sheet, $start, $end - $start);

        $this->assertStringContainsString('showSheetEmailModal()', $handler);
        $this->assertStringContainsString('getComposeDefaults', $handler);
        $this->assertStringContainsString('last_checklist_email', $handler);
        $this->assertStringContainsString('sheetEmailReminderPendingBody', $handler);
        $this->assertStringNotContainsString('window.location.href = emailsComposeUrl', $handler);
    }

    #[Test]
    public function compose_defaults_exposes_last_checklist_email_key(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/CRM/CRMUtilityController.php'));
        $this->assertIsString($controller);
        $this->assertStringContainsString("'last_checklist_email' => \$lastChecklistEmail", $controller);
        $this->assertStringContainsString('LastChecklistEmailForMatter', $controller);
    }

    #[Test]
    public function emails_tab_does_not_auto_open_sheet_checklist_reminder_compose(): void
    {
        $js = file_get_contents(public_path('js/crm/clients/emails-tab.js'));
        $this->assertIsString($js);

        $this->assertStringNotContainsString('bootSheetChecklistReminderComposeIfNeeded', $js);
        $this->assertStringContainsString('bootEmailsTabIfNeeded', $js);
    }
}
