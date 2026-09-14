<?php

namespace Tests\Unit\Services;

use App\Mail\AppointmentDetailedConfirmation;
use App\Models\BookingAppointment;
use App\Services\BansalAppointmentSync\NotificationService;
use App\Services\Sms\UnifiedSmsManager;
use App\Services\SystemEmailLogService;
use Illuminate\Mail\Mailable;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NotificationServiceConfirmationReminderTest extends TestCase
{
    #[Test]
    public function it_sends_confirmation_template_with_reminder_subject_without_marking_confirmation_sent(): void
    {
        $sends = [];
        $emailLog = Mockery::mock(SystemEmailLogService::class);
        $emailLog->shouldReceive('logAndSendMailable')
            ->once()
            ->andReturnUsing(function (array $meta, Mailable $mailable, mixed $to) use (&$sends): void {
                $sends[] = [
                    'meta' => $meta,
                    'mailable' => $mailable,
                    'to' => $to,
                ];
            });

        $appointment = Mockery::mock(BookingAppointment::class)->makePartial();
        $appointment->forceFill([
            'client_name' => 'Test Client',
            'client_email' => 'client@example.test',
            'client_id' => 1,
            'appointment_datetime' => now()->addDay(),
            'timeslot_full' => '10:00 AM - 10:30 AM',
            'location' => 'melbourne',
            'meeting_type' => 'in_person',
            'service_type' => 'Consultation',
            'confirmation_email_sent' => true,
        ]);
        $appointment->id = 42;
        $appointment->shouldNotReceive('update');

        $service = new NotificationService(
            Mockery::mock(UnifiedSmsManager::class),
            $emailLog,
        );

        $this->assertTrue($service->sendConfirmationReminderEmail($appointment));
        $this->assertCount(1, $sends);
        $this->assertSame('client@example.test', $sends[0]['to']);
        $this->assertSame(AppointmentDetailedConfirmation::REMINDER_SUBJECT, $sends[0]['meta']['subject']);
        $this->assertSame('appointment', $sends[0]['meta']['category']);
        $this->assertInstanceOf(AppointmentDetailedConfirmation::class, $sends[0]['mailable']);
        $sends[0]['mailable']->assertHasSubject(AppointmentDetailedConfirmation::REMINDER_SUBJECT);
        $this->assertTrue($appointment->confirmation_email_sent);
    }

    #[Test]
    public function send_detailed_confirmation_still_skips_when_already_sent(): void
    {
        $emailLog = Mockery::mock(SystemEmailLogService::class);
        $emailLog->shouldNotReceive('logAndSendMailable');

        $appointment = new BookingAppointment([
            'client_email' => 'client@example.test',
            'confirmation_email_sent' => true,
        ]);

        $service = new NotificationService(
            Mockery::mock(UnifiedSmsManager::class),
            $emailLog,
        );

        $this->assertTrue($service->sendDetailedConfirmationEmail($appointment));
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
