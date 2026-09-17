<?php

namespace Tests\Unit\Support;

use App\Support\ClientDetailVerificationFields;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ClientDetailVerificationFieldsTest extends TestCase
{
    #[Test]
    public function build_snapshot_uses_primary_email_and_phone_only(): void
    {
        $snapshot = ClientDetailVerificationFields::buildSnapshot([
            'first_name' => 'Vipul',
            'last_name' => 'Kumar',
            'dob' => '1986-03-15',
            'gender' => 'Other',
            'marital_status' => 'Married',
            'primary_email' => 'primary@example.com',
            'primary_phone' => '0412 345 678',
            'address' => 'Melbourne, Australia',
            'visa_type' => '408 - Temporary Activity',
            'visa_expiry' => '2026-12-01',
            'passport_country' => 'India',
            'location_status' => 'Onshore - Australia',
        ]);

        $this->assertSame('Vipul Kumar', $snapshot['full_name']);
        $this->assertSame('15/03/1986', $snapshot['dob']);
        $this->assertSame('primary@example.com', $snapshot['email']);
        $this->assertSame('0412 345 678', $snapshot['phone']);
        $this->assertSame('01/12/2026', $snapshot['visa_expiry']);
        $this->assertArrayNotHasKey('work_email', $snapshot);
        $this->assertCount(11, $snapshot);
    }

    #[Test]
    public function validate_submitted_fields_requires_every_known_key(): void
    {
        $fields = array_map(static fn (string $key): array => [
            'key' => $key,
            'status' => ClientDetailVerificationFields::STATUS_CONFIRMED,
        ], ClientDetailVerificationFields::keys());

        $this->assertTrue(ClientDetailVerificationFields::validateSubmittedFields($fields)['ok']);

        array_pop($fields);
        $result = ClientDetailVerificationFields::validateSubmittedFields($fields);
        $this->assertFalse($result['ok']);
        $this->assertStringContainsString('Missing field', $result['message'] ?? '');
    }

    #[Test]
    public function change_request_requires_a_new_value(): void
    {
        $result = ClientDetailVerificationFields::validateSubmittedField([
            'key' => 'email',
            'status' => ClientDetailVerificationFields::STATUS_CHANGE_REQUESTED,
            'requested_value' => '',
        ]);

        $this->assertFalse($result['ok']);
    }

    #[Test]
    public function split_full_name_and_location_helpers_work(): void
    {
        $this->assertSame(['Vipul', 'Kumar'], ClientDetailVerificationFields::splitFullName('Vipul Kumar'));
        $this->assertSame('Onshore - Australia', ClientDetailVerificationFields::locationFromCountry('Australia'));
        $this->assertSame('Offshore - Outside Australia', ClientDetailVerificationFields::locationFromCountry('India'));
    }

    #[Test]
    public function sms_text_uses_the_requested_copy_and_link_only(): void
    {
        $url = 'https://example.test/verify-details/abc123abc123abc123abc123abc123ab';
        $text = ClientDetailVerificationFields::smsText('Vipul', $url);

        $this->assertSame(
            "Hi Vipul, Bansal Immigration Consultants requests you to verify your Personal & Visa details currently recorded on your file.\n\nPlease review and confirm or request any corrections using the secure link below:\n\n{$url}\n\nIt should only take 1–2 minutes. Please do not forward this personalised link to anyone else.",
            $text
        );
        $this->assertStringNotContainsString('Bansal Immigration Team', $text);
        $this->assertStringNotContainsString('Open verification form', $text);
    }

    #[Test]
    public function visa_type_label_matches_client_edit_dropdown_text(): void
    {
        $this->assertSame('151 - Former Resident (FR)', ClientDetailVerificationFields::visaTypeLabel('151 - Former Resident', 'FR'));
        $this->assertTrue(ClientDetailVerificationFields::visaTypeMatchesRequested(
            '151 - Former Resident',
            'FR',
            '151 - Former Resident (FR)',
        ));
        $this->assertTrue(ClientDetailVerificationFields::visaTypeMatchesRequested(
            '151 - Former Resident',
            'FR',
            '151 - Former Resident(FR)',
        ));
        $this->assertTrue(ClientDetailVerificationFields::visaTypeMatchesRequested('151 - Former Resident', 'FR', '151 - Former Resident'));
        $this->assertFalse(ClientDetailVerificationFields::visaTypeMatchesRequested('151 - Former Resident', 'FR', 'Student 500'));
    }

    #[Test]
    public function result_heading_is_always_verification_confirmed(): void
    {
        $this->assertSame('Verification Confirmed', ClientDetailVerificationFields::resultHeading(11, 0));
        $this->assertSame('Verification Confirmed', ClientDetailVerificationFields::resultHeading(0, 11));
        $this->assertSame('Verification Confirmed', ClientDetailVerificationFields::resultHeading(10, 1));
        $this->assertSame('14/09/2026 4:44 PM', ClientDetailVerificationFields::formatVerifiedAt(Carbon::parse('2026-09-14 16:44:00')));
    }

    #[Test]
    public function compose_address_joins_structured_parts_in_edit_page_order(): void
    {
        $this->assertSame(
            '10 Oliva Approach, Piara Waters, WA, Australia, 6112',
            ClientDetailVerificationFields::composeAddress(
                '10 Oliva Approach',
                null,
                'Piara Waters',
                'WA',
                'Australia',
                '6112',
            ),
        );
    }
}
