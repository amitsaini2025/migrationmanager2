<?php

namespace App\Services;

use App\Models\DeviceToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FCMService
{
    private $serverKey;
    private $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

    public function __construct()
    {
        $this->serverKey = config('services.fcm.server_key');
    }

    /**
     * Send notification to a specific user
     */
    public function sendToUser($userId, $title, $body, $data = [])
    {
        $deviceTokens = DeviceToken::active()
            ->forUser($userId)
            ->pluck('device_token')
            ->toArray();

        if (empty($deviceTokens)) {
            Log::warning('No active device tokens found for user', ['user_id' => $userId]);
            return false;
        }

        return $this->sendToMultipleDevices($deviceTokens, $title, $body, $data);
    }

    /**
     * Send notification to multiple devices
     */
    public function sendToMultipleDevices($deviceTokens, $title, $body, $data = [])
    {
        if (empty($deviceTokens)) {
            return false;
        }

        $payload = [
            'registration_ids' => $deviceTokens,
            'notification' => [
                'title' => $title,
                'body' => $body,
                'sound' => 'default',
                'badge' => 1,
            ],
            'data' => $data,
            'priority' => 'high',
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $this->serverKey,
                'Content-Type' => 'application/json',
            ])->post($this->fcmUrl, $payload);

            $responseData = $response->json();

            // Handle failed tokens
            if (isset($responseData['results'])) {
                $this->handleFailedTokens($deviceTokens, $responseData['results']);
            }

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('FCM notification failed', [
                'error' => $e->getMessage(),
                'device_tokens_count' => count($deviceTokens)
            ]);
            return false;
        }
    }

    /**
     * Handle failed device tokens
     */
    private function handleFailedTokens($deviceTokens, $results)
    {
        foreach ($results as $index => $result) {
            if (isset($result['error'])) {
                $deviceToken = $deviceTokens[$index];
                
                // Deactivate invalid tokens
                if (in_array($result['error'], ['NotRegistered', 'InvalidRegistration'])) {
                    DeviceToken::where('device_token', $deviceToken)
                        ->update(['is_active' => false]);
                    
                    Log::info('Deactivated invalid device token', [
                        'device_token' => substr($deviceToken, 0, 20) . '...',
                        'error' => $result['error']
                    ]);
                }
            }
        }
    }

    /**
     * Send appointment reminder
     */
    public function sendAppointmentReminder($userId, $appointmentData)
    {
        $title = 'Appointment Reminder';
        $body = "You have an appointment scheduled for {$appointmentData['date']} at {$appointmentData['time']}";
        
        $data = [
            'type' => 'appointment_reminder',
            'appointment_id' => $appointmentData['id'],
            'action' => 'view_appointment'
        ];

        return $this->sendToUser($userId, $title, $body, $data);
    }

    /**
     * Send document approval notification
     */
    public function sendDocumentApproval($userId, $documentData)
    {
        $title = 'Document Approved';
        $body = "Your document '{$documentData['title']}' has been approved";
        
        $data = [
            'type' => 'document_approval',
            'document_id' => $documentData['id'],
            'action' => 'view_document'
        ];

        return $this->sendToUser($userId, $title, $body, $data);
    }

    /**
     * Send case status update
     */
    public function sendCaseStatusUpdate($userId, $caseData)
    {
        $title = 'Case Status Update';
        $body = "Your case '{$caseData['title']}' status has been updated to {$caseData['status']}";
        
        $data = [
            'type' => 'case_status_update',
            'case_id' => $caseData['id'],
            'action' => 'view_case'
        ];

        return $this->sendToUser($userId, $title, $body, $data);
    }
}
