<?php

namespace Tests\Unit\Support;

use App\Models\BookingAppointment;
use App\Support\AppointmentActivityDescription;
use Carbon\Carbon;
use Tests\TestCase;

class AppointmentActivityDescriptionTest extends TestCase
{
    public function test_build_description_includes_labelled_category_appt_type_and_query(): void
    {
        $appointment = new BookingAppointment([
            'service_id' => 2,
            'noe_id' => 11,
            'service_type' => 'Family Visas (Parent Visa, Partner Visa, Child Visa)',
            'meeting_type' => 'in_person',
            'preferred_language' => 'Punjabi',
            'enquiry_details' => 'Test. Pls ignore',
            'location' => 'melbourne',
            'appointment_datetime' => Carbon::parse('2026-08-13 12:00:00'),
            'timeslot_full' => '12:00 PM-12:20 PM',
        ]);

        $html = AppointmentActivityDescription::buildDescription($appointment);

        $this->assertStringContainsString('appointment-activity-detail__chip', $html);
        $this->assertStringContainsString('appointment-activity-detail__row--datetime', $html);
        $this->assertStringContainsString('data-field="datetime"', $html);
        $this->assertStringContainsString('Category:', $html);
        $this->assertStringContainsString('Family Visas (Parent Visa, Partner Visa, Child Visa)', $html);
        $this->assertStringContainsString('Appt. Type:', $html);
        $this->assertStringContainsString('Free Consultation · In Person', $html);
        $this->assertStringContainsString('Query:', $html);
        $this->assertStringContainsString('Test. Pls ignore', $html);
        $this->assertStringContainsString('Language:', $html);
        $this->assertStringContainsString('Punjabi', $html);
        $this->assertStringContainsString('Location:', $html);
        $this->assertStringContainsString('Melbourne Free PR', $html);
        $this->assertStringContainsString('Date &amp; Time:', $html);
        $this->assertStringContainsString('13 Aug 2026 · 12:00 PM-12:20 PM', $html);
    }

    public function test_activity_subject_uses_correct_grammar(): void
    {
        $this->assertSame('scheduled a free appointment', AppointmentActivityDescription::activitySubject(2));
        $this->assertSame('scheduled a paid appointment', AppointmentActivityDescription::activitySubject(1));
        $this->assertSame('scheduled an appointment', AppointmentActivityDescription::activitySubject(null));
    }

    public function test_update_activity_subject_uses_correct_grammar(): void
    {
        $this->assertSame('updated a free appointment', AppointmentActivityDescription::updateActivitySubject(2));
        $this->assertSame('updated a paid appointment', AppointmentActivityDescription::updateActivitySubject(1));
        $this->assertSame('updated an appointment', AppointmentActivityDescription::updateActivitySubject(null));
    }

    public function test_build_update_description_uses_appointment_card_with_change_rows(): void
    {
        $appointment = new BookingAppointment([
            'service_id' => 2,
            'noe_id' => 4,
            'service_type' => 'Tourist Visa',
            'meeting_type' => 'in_person',
            'preferred_language' => 'English',
            'enquiry_details' => 'test .Pls ignore',
            'location' => 'melbourne',
            'appointment_datetime' => Carbon::parse('2026-10-22 10:00:00'),
            'timeslot_full' => '10:00 AM-10:20 AM',
        ]);

        $html = AppointmentActivityDescription::buildUpdateDescription($appointment, [
            'datetime' => [
                'from' => '23 Oct 2026, 10:00 AM',
                'to' => '22 Oct 2026, 10:00 AM',
            ],
        ]);

        $this->assertStringContainsString('appointment-activity-detail', $html);
        $this->assertStringContainsString('Appointment Updated', $html);
        $this->assertStringContainsString('Rescheduled:', $html);
        $this->assertStringContainsString('23 Oct 2026, 10:00 AM → 22 Oct 2026, 10:00 AM', $html);
        $this->assertStringContainsString('Tourist Visa', $html);
        $this->assertStringContainsString('22 Oct 2026 · 10:00 AM-10:20 AM', $html);
        $this->assertStringContainsString('Melbourne Free PR', $html);
    }

    public function test_build_update_activity_log_payload_matches_create_structure(): void
    {
        $appointment = new BookingAppointment([
            'client_id' => 99,
            'service_id' => 2,
            'appointment_datetime' => Carbon::parse('2026-10-22 10:00:00'),
        ]);

        $payload = AppointmentActivityDescription::buildUpdateActivityLogPayload(
            $appointment,
            7,
            [
                'preferred_language' => [
                    'from' => 'English',
                    'to' => 'Hindi',
                ],
            ]
        );

        $this->assertSame(99, $payload['client_id']);
        $this->assertSame(7, $payload['created_by']);
        $this->assertSame('updated a free appointment', $payload['subject']);
        $this->assertSame('activity', $payload['activity_type']);
        $this->assertStringContainsString('appointment-activity-detail', $payload['description']);
        $this->assertStringContainsString('Preferred language:', $payload['description']);
    }

    public function test_category_falls_back_to_noe_id_when_service_type_missing(): void
    {
        $appointment = new BookingAppointment([
            'noe_id' => 9,
            'service_type' => null,
        ]);

        $this->assertSame('EOI/ROI', AppointmentActivityDescription::categoryLabel($appointment));
    }

    public function test_category_falls_back_to_ajay_and_arun_noe_labels(): void
    {
        $ajay = new BookingAppointment([
            'noe_id' => 13,
            'service_type' => null,
        ]);
        $arun = new BookingAppointment([
            'noe_id' => 14,
            'service_type' => null,
        ]);

        $this->assertSame('Ajay Bansal', AppointmentActivityDescription::categoryLabel($ajay));
        $this->assertSame('Arun Bansal', AppointmentActivityDescription::categoryLabel($arun));
    }
}
