<?php

namespace Tests\Unit;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OfficeReceiptInvoiceOptionalTest extends TestCase
{
    #[Test]
    public function create_and_update_office_receipt_do_not_require_invoice_number(): void
    {
        $controller = file_get_contents($this->projectPath('app/Http/Controllers/CRM/ClientAccountsController.php'));
        Assert::assertNotFalse($controller);
        Assert::assertStringNotContainsString(
            'An invoice number is required for office receipts that are not refunds.',
            $controller
        );

        $accountTab = file_get_contents($this->projectPath('resources/views/crm/clients/tabs/account.blade.php'));
        Assert::assertNotFalse($accountTab);
        Assert::assertStringContainsString('Select Invoice (Optional)', $accountTab);
        Assert::assertStringContainsString('$invoiceSelect.removeAttr(\'data-valid\');', $accountTab);
        Assert::assertStringNotContainsString(
            'An invoice number is required for office receipts that are not refunds.',
            $accountTab
        );
        Assert::assertDoesNotMatchRegularExpression(
            "/syncOfficeReceiptInvoiceRequirement[\s\S]*?attr\('data-valid',\s*'required'\)/",
            $accountTab
        );

        $validationJs = file_get_contents($this->projectPath('public/js/custom-form-validation.js'));
        Assert::assertNotFalse($validationJs);
        Assert::assertStringNotContainsString(
            'An invoice number is required for office receipts that are not refunds.',
            $validationJs
        );
    }

    #[Test]
    public function fee_transfer_and_journal_still_require_invoice_selection(): void
    {
        $receiptsModal = file_get_contents($this->projectPath('resources/views/crm/clients/modals/receipts.blade.php'));
        Assert::assertNotFalse($receiptsModal);
        Assert::assertStringContainsString(
            '<select data-valid="required" class="form-control invoice_no_cls"  name="invoice_no[]">',
            $receiptsModal
        );

        $detailMain = file_get_contents($this->projectPath('public/js/crm/clients/detail-main.js'));
        Assert::assertNotFalse($detailMain);
        Assert::assertStringContainsString("ledgerType === 'Fee Transfer'", $detailMain);
        Assert::assertStringContainsString('$invoiceInput.show().attr(\'data-valid\', \'required\');', $detailMain);
    }

    private function projectPath(string $relative): string
    {
        return dirname(__DIR__, 2).DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relative);
    }
}
