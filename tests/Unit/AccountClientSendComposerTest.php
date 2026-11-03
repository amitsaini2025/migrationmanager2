<?php

namespace Tests\Unit;

use App\Support\AccountClientSendComposer;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AccountClientSendComposerTest extends TestCase
{
    #[Test]
    public function draft_uses_the_document_number_and_client_name(): void
    {
        $draft = AccountClientSendComposer::draft('client-fund', 'John Testing', '482SD_1');

        $this->assertSame('Client Fund Receipt #482SD_1 from Bansal Immigration', $draft['subject']);
        $this->assertStringContainsString('Dear John Testing,', $draft['message']);
        $this->assertStringContainsString('client fund receipt #482SD_1', $draft['message']);
        $this->assertSame('Receipt-482SD_1.pdf', $draft['attachment_name']);
    }

    #[Test]
    public function office_and_invoice_drafts_use_their_own_labels(): void
    {
        $office = AccountClientSendComposer::draft('office', 'Jane', 'OR-9');
        $invoice = AccountClientSendComposer::draft('invoice', 'Jane', 'INV-3');

        $this->assertSame('Office Receipt #OR-9 from Bansal Immigration', $office['subject']);
        $this->assertSame('Office-Receipt-OR-9.pdf', $office['attachment_name']);
        $this->assertSame('Invoice #INV-3 from Bansal Immigration', $invoice['subject']);
        $this->assertSame('Invoice-INV-3.pdf', $invoice['attachment_name']);
    }

    #[Test]
    public function plain_text_is_escaped_and_line_breaks_are_kept(): void
    {
        $html = AccountClientSendComposer::plainToHtml("Hello <script>\n\nNext line");

        $this->assertStringContainsString("Hello &lt;script&gt;<br>\n<br>\nNext line", $html);
        $this->assertStringNotContainsString('<script>', $html);
    }

    #[Test]
    public function only_a_listed_sender_can_be_selected(): void
    {
        $senders = [
            ['email' => 'invoice@bansalimmigration.com.au', 'name' => 'Accounts'],
            ['email' => 'info@bansalimmigration.com.au', 'name' => ''],
        ];

        $invoice = AccountClientSendComposer::resolveSender('Invoice@bansalimmigration.com.au', $senders);
        $this->assertSame('invoice@bansalimmigration.com.au', $invoice['email']);
        $this->assertSame('Accounts', $invoice['name']);

        $info = AccountClientSendComposer::resolveSender('info@bansalimmigration.com.au', $senders);
        $this->assertSame('Bansal Immigration', $info['name']);

        $this->assertNull(AccountClientSendComposer::resolveSender('other@example.com', $senders));
        $this->assertSame(
            'invoice@bansalimmigration.com.au',
            AccountClientSendComposer::suggestedFrom($senders)
        );
    }
}
