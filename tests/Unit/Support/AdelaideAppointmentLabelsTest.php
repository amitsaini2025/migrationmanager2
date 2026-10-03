<?php

namespace Tests\Unit\Support;

use App\Models\BookingAppointment;
use App\Support\AdelaideAppointmentLabels;
use PHPUnit\Framework\TestCase;

class AdelaideAppointmentLabelsTest extends TestCase
{
    public function test_normalize_adelaide_gsm_sync_replaces_internal_slugs(): void
    {
        $normalized = AdelaideAppointmentLabels::normalizeSyncedFields(
            'adelaide',
            1,
            'permanent-residency',
            'kunal',
            1
        );

        $this->assertSame('GSM Visas: 491, 190, 189, 191', $normalized['service_type']);
        $this->assertSame('GSM Visas (491, 190, 189, 191)', $normalized['enquiry_type']);
    }

    public function test_normalize_melbourne_sync_is_unchanged(): void
    {
        $normalized = AdelaideAppointmentLabels::normalizeSyncedFields(
            'melbourne',
            1,
            'permanent-residency',
            'kunal'
        );

        $this->assertSame('permanent-residency', $normalized['service_type']);
        $this->assertSame('kunal', $normalized['enquiry_type']);
    }

    public function test_display_adelaide_gsm_hides_legacy_kunal_for_existing_records(): void
    {
        $appointment = new BookingAppointment([
            'location' => 'adelaide',
            'inperson_address' => 1,
            'noe_id' => 1,
            'service_type' => 'permanent-residency',
            'enquiry_type' => 'kunal',
        ]);

        $this->assertSame('GSM Visas: 491, 190, 189, 191', AdelaideAppointmentLabels::displayServiceType($appointment));
        $this->assertSame('GSM Visas (491, 190, 189, 191)', AdelaideAppointmentLabels::displayEnquiryType($appointment));
    }

    public function test_display_melbourne_appointment_is_unchanged(): void
    {
        $appointment = new BookingAppointment([
            'location' => 'melbourne',
            'noe_id' => 1,
            'service_type' => 'GSM Visas: 491, 190, 189, 191',
            'enquiry_type' => 'pr_complex',
        ]);

        $this->assertSame('GSM Visas: 491, 190, 189, 191', AdelaideAppointmentLabels::displayServiceType($appointment));
        $this->assertSame('pr_complex', AdelaideAppointmentLabels::displayEnquiryType($appointment));
    }

    public function test_display_adelaide_keeps_already_friendly_labels(): void
    {
        $appointment = new BookingAppointment([
            'location' => 'adelaide',
            'noe_id' => 1,
            'service_type' => 'GSM Visas: 491, 190, 189, 191',
            'enquiry_type' => 'GSM Visas (491, 190, 189, 191)',
        ]);

        $this->assertSame('GSM Visas: 491, 190, 189, 191', AdelaideAppointmentLabels::displayServiceType($appointment));
        $this->assertSame('GSM Visas (491, 190, 189, 191)', AdelaideAppointmentLabels::displayEnquiryType($appointment));
    }

    public function test_display_adelaide_tourist_uses_friendly_labels_from_noe(): void
    {
        $appointment = new BookingAppointment([
            'location' => 'adelaide',
            'noe_id' => 4,
            'service_type' => 'tourist-visa',
            'enquiry_type' => 'tourist',
        ]);

        $this->assertSame('Tourist Visa', AdelaideAppointmentLabels::displayServiceType($appointment));
        $this->assertSame('Tourist Visa', AdelaideAppointmentLabels::displayEnquiryType($appointment));
    }
}
