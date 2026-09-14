<?php

namespace Tests\Unit\Services;

use App\Models\BookingAppointment;
use App\Services\BansalAppointmentSync\NotificationService;
use App\Services\BookingAppointmentConfirmationReminderService;
use App\Support\BookingAppointmentStatus;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BookingAppointmentConfirmationReminderServiceTest extends TestCase
{
    #[Test]
    public function it_rejects_paid_appointments(): void
    {
        $notifications = Mockery::mock(NotificationService::class);
        $notifications->shouldNotReceive('sendConfirmationReminderEmail');

        $appointment = new BookingAppointment([
            'is_paid' => true,
            'status' => BookingAppointmentStatus::AWAITING_CONFIRMATION,
            'client_email' => 'client@example.test',
        ]);

        $result = (new BookingAppointmentConfirmationReminderService($notifications))
            ->sendConfirmationReminder($appointment);

        $this->assertFalse($result['success']);
        $this->assertSame(
            'This reminder is only available for Free appointments awaiting confirmation.',
            $result['message']
        );
    }

    #[Test]
    public function it_rejects_appointments_that_are_not_awaiting_confirmation(): void
    {
        $notifications = Mockery::mock(NotificationService::class);
        $notifications->shouldNotReceive('sendConfirmationReminderEmail');

        $appointment = new BookingAppointment([
            'is_paid' => false,
            'status' => BookingAppointmentStatus::CONFIRMED,
            'client_email' => 'client@example.test',
        ]);

        $result = (new BookingAppointmentConfirmationReminderService($notifications))
            ->sendConfirmationReminder($appointment);

        $this->assertFalse($result['success']);
        $this->assertSame(
            'This reminder is only available when the appointment is awaiting confirmation.',
            $result['message']
        );
    }

    #[Test]
    public function it_rejects_appointments_without_client_email(): void
    {
        $notifications = Mockery::mock(NotificationService::class);
        $notifications->shouldNotReceive('sendConfirmationReminderEmail');

        $appointment = new BookingAppointment([
            'is_paid' => false,
            'status' => BookingAppointmentStatus::AWAITING_CONFIRMATION,
            'client_email' => '',
        ]);

        $result = (new BookingAppointmentConfirmationReminderService($notifications))
            ->sendConfirmationReminder($appointment);

        $this->assertFalse($result['success']);
        $this->assertSame('This appointment has no client email address.', $result['message']);
    }

    #[Test]
    public function it_sends_reminder_without_changing_status_or_payment(): void
    {
        $appointment = new BookingAppointment([
            'is_paid' => false,
            'status' => BookingAppointmentStatus::AWAITING_CONFIRMATION,
            'payment_status' => null,
            'client_email' => 'client@example.test',
            'confirmation_email_sent' => true,
        ]);

        $notifications = Mockery::mock(NotificationService::class);
        $notifications->shouldReceive('sendConfirmationReminderEmail')
            ->once()
            ->with($appointment)
            ->andReturnTrue();

        $result = (new BookingAppointmentConfirmationReminderService($notifications))
            ->sendConfirmationReminder($appointment);

        $this->assertTrue($result['success']);
        $this->assertFalse($appointment->is_paid);
        $this->assertSame(BookingAppointmentStatus::AWAITING_CONFIRMATION, $appointment->status);
        $this->assertTrue($appointment->confirmation_email_sent);
        $this->assertNull($appointment->payment_status);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
