<?php

namespace App\Services;

use App\Models\BookingAppointment;
use App\Services\BansalAppointmentSync\NotificationService;
use App\Support\BookingAppointmentStatus;

class BookingAppointmentConfirmationReminderService
{
    public function __construct(
        protected NotificationService $notificationService,
    ) {}

    /**
     * Email the existing confirmation template again. Does not change status, payment, or confirmation_email_sent.
     *
     * @return array{success: bool, message: string}
     */
    public function sendConfirmationReminder(BookingAppointment $appointment): array
    {
        if ($appointment->is_paid) {
            return [
                'success' => false,
                'message' => 'This reminder is only available for Free appointments awaiting confirmation.',
            ];
        }

        if ($appointment->status !== BookingAppointmentStatus::AWAITING_CONFIRMATION) {
            return [
                'success' => false,
                'message' => 'This reminder is only available when the appointment is awaiting confirmation.',
            ];
        }

        if (empty($appointment->client_email)) {
            return [
                'success' => false,
                'message' => 'This appointment has no client email address.',
            ];
        }

        $sent = $this->notificationService->sendConfirmationReminderEmail($appointment);

        if (! $sent) {
            return [
                'success' => false,
                'message' => 'Could not send the reminder email. Please try again.',
            ];
        }

        return [
            'success' => true,
            'message' => 'Reminder for appointment confirmation sent to the client.',
        ];
    }
}
