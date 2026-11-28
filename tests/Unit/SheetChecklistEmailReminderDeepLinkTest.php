<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SheetChecklistEmailReminderDeepLinkTest extends TestCase
{
    #[Test]
    public function sheet_email_reminder_opens_popup_with_matter_checklist_email(): void
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
        $this->assertStringContainsString('initSheetEmailReminderTinyMce()', $handler);
        $this->assertStringContainsString('getComposeDefaults', $handler);
        $this->assertStringContainsString('sheetEmailReminderPendingBody', $handler);
        $this->assertStringContainsString('firstTemplate', $handler);
        $this->assertStringNotContainsString('window.location.href = emailsComposeUrl', $handler);
    }

    #[Test]
    public function emails_tab_does_not_auto_open_sheet_checklist_reminder_compose(): void
    {
        $js = file_get_contents(public_path('js/crm/clients/emails-tab.js'));
        $this->assertIsString($js);

        $this->assertStringNotContainsString('bootSheetChecklistReminderComposeIfNeeded', $js);
        $this->assertStringNotContainsString("params.get('compose') !== 'checklist_reminder'", $js);
        $this->assertStringContainsString('bootEmailsTabIfNeeded', $js);
    }
}
