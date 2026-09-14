<?php

namespace Tests\Unit\Support;

use App\Support\ClientDetailVerificationFields;
use App\Support\ClientDetailVerificationUi;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ClientDetailVerificationUiTest extends TestCase
{
    #[Test]
    public function change_request_icon_uses_request_change_hover_title(): void
    {
        $html = ClientDetailVerificationUi::icon([
            'id' => 12,
            'field_key' => 'gender',
            'status' => ClientDetailVerificationFields::STATUS_CHANGE_REQUESTED,
            'original_value' => 'Other',
            'requested_value' => 'Male',
        ]);

        $this->assertStringContainsString('title="Request Change"', $html);
        $this->assertStringContainsString('data-change-request="1"', $html);
        $this->assertStringNotContainsString('title="Change requested', $html);
    }

    #[Test]
    public function confirmed_icon_uses_confirmed_hover_title(): void
    {
        $html = ClientDetailVerificationUi::icon([
            'field_key' => 'marital_status',
            'status' => ClientDetailVerificationFields::STATUS_CONFIRMED,
        ]);

        $this->assertStringContainsString('title="Confirmed"', $html);
        $this->assertStringNotContainsString('data-change-request', $html);
    }

    #[Test]
    public function verify_link_script_lets_staff_choose_email_or_phone_without_touching_other_actions(): void
    {
        $js = file_get_contents(base_path('public/js/crm/clients/verify-link.js'));
        $this->assertNotFalse($js);
        $this->assertStringContainsString("on('click', '.send-verify-link'", $js);
        $this->assertStringContainsString('verifyLinkChannelModal', $js);
        $this->assertStringContainsString('Primary Email Address - ', $js);
        $this->assertStringContainsString('Primary Phone no - ', $js);
        $this->assertStringContainsString('primaryEmail', $js);
        $this->assertStringContainsString('primaryPhone', $js);
        $this->assertStringContainsString('updateChannelLabels', $js);
        $this->assertStringContainsString('textContent', $js);
        $this->assertStringContainsString('name="verify_link_channel"', $js);
        $this->assertStringContainsString('channel: channel', $js);
        $this->assertStringContainsString('Sending...', $js);
        $this->assertStringContainsString('setSendingState', $js);
        $this->assertStringNotContainsString('window.confirm', $js);
        $this->assertStringNotContainsString('.send-sms-btn', $js);
        $this->assertStringNotContainsString('#create_appoint', $js);
        $this->assertStringNotContainsString('clients.verifyDetails', $js);
    }
}
