<?php

namespace App\Services\BansalAppointmentSync;

use App\Models\BookingAppointment;
use App\Models\AppointmentSyncLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class AppointmentSyncService
{
    protected BansalApiClient $apiClient;
    protected ClientMatchingService $clientMatcher;
    protected ConsultantAssignmentService $consultantAssigner;
    
    protected AppointmentSyncLog $syncLog;

    public function __construct(
        BansalApiClient $apiClient,
        ClientMatchingService $clientMatcher,
        ConsultantAssignmentService $consultantAssigner
    ) {
        $this->apiClient = $apiClient;
        $this->clientMatcher = $clientMatcher;
        $this->consultantAssigner = $consultantAssigner;
    }

    /**
     * Sync recent appointments (main polling method)
     */
    public function syncRecentAppointments(int $minutes = 10): array
    {
        // Create sync log
        $this->syncLog = AppointmentSyncLog::create([
            'sync_type' => 'polling',
            'started_at' => now(),
            'status' => 'running'
        ]);

        $stats = [
            'fetched' => 0,
            'new' => 0,
            'updated' => 0,
            'skipped' => 0,
            'failed' => 0,
            'errors' => []
        ];

        try {
            Log::info('Starting appointment sync', ['minutes' => $minutes]);

            // Fetch appointments from Bansal API
            $appointments = $this->apiClient->getRecentAppointments($minutes);
            $stats['fetched'] = count($appointments);

            Log::info("Fetched {$stats['fetched']} appointments from Bansal API");

            // Process each appointment
            foreach ($appointments as $appointmentData) {
                try {
                    $result = $this->processAppointment($appointmentData);
                    
                    if ($result === 'new') {
                        $stats['new']++;
                    } elseif ($result === 'updated') {
                        $stats['updated']++;
                    } elseif ($result === 'skipped') {
                        $stats['skipped']++;
                    }
                } catch (Exception $e) {
                    $stats['failed']++;
                    $stats['errors'][] = [
                        'appointment_id' => $appointmentData['id'] ?? 'unknown',
                        'error' => $e->getMessage()
                    ];
                    
                    Log::error('Failed to process appointment', [
                        'appointment_id' => $appointmentData['id'] ?? null,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            // Update sync log
            $this->syncLog->update([
                'completed_at' => now(),
                'status' => $stats['failed'] > 0 ? 'failed' : 'success',
                'appointments_fetched' => $stats['fetched'],
                'appointments_new' => $stats['new'],
                'appointments_updated' => $stats['updated'],
                'appointments_skipped' => $stats['skipped'],
                'appointments_failed' => $stats['failed'],
                'sync_details' => json_encode($stats)
            ]);

            Log::info('Appointment sync completed', $stats);

            return $stats;
        } catch (Exception $e) {
            $this->syncLog->update([
                'completed_at' => now(),
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'appointments_fetched' => $stats['fetched'],
                'appointments_failed' => $stats['failed']
            ]);

            Log::error('Appointment sync failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Process single appointment
     */
    protected function processAppointment(array $appointmentData): string
    {
        $bansalId = $appointmentData['id'];

        // Check if already exists
        $existingAppointment = BookingAppointment::where('bansal_appointment_id', $bansalId)->first();

        if ($existingAppointment) {
            // Update if needed (optional - you might want to skip updates)
            Log::info('Appointment already exists, skipping', ['bansal_id' => $bansalId]);
            return 'skipped';
        }

        // Match or create client
        $client = $this->clientMatcher->findOrCreateClient($appointmentData);

        // Assign consultant
        $consultant = $this->consultantAssigner->assignConsultant($appointmentData);

        // Map status
        $status = $this->mapStatus($appointmentData['status'] ?? 'pending');

        // Create appointment record
        $appointment = BookingAppointment::create([
            'bansal_appointment_id' => $bansalId,
            'order_hash' => $appointmentData['order_hash'] ?? null,
            
            'client_id' => $client?->id,
            'consultant_id' => $consultant?->id,
            
            'client_name' => $appointmentData['full_name'],
            'client_email' => $appointmentData['email'],
            'client_phone' => $appointmentData['phone'] ?? null,
            'client_timezone' => 'Australia/Melbourne',
            
            'appointment_datetime' => Carbon::parse($appointmentData['appointment_datetime']),
            'timeslot_full' => $appointmentData['appointment_time'] ?? null,
            'duration_minutes' => $appointmentData['duration_minutes'] ?? 15,
            'location' => $appointmentData['location'],
            'inperson_address' => $this->mapInpersonAddress($appointmentData['location']),
            'meeting_type' => $appointmentData['meeting_type'] ?? 'in_person',
            'preferred_language' => $appointmentData['preferred_language'] ?? 'English',
            
            'service_id' => $this->mapServiceId($appointmentData),
            'noe_id' => $this->mapNoeId($appointmentData),
            'enquiry_type' => $appointmentData['enquiry_type'] ?? null,
            'service_type' => $appointmentData['service_type'] ?? null,
            'enquiry_details' => $appointmentData['enquiry_details'] ?? null,
            
            'status' => $status,
            'confirmed_at' => $status === 'confirmed' ? now() : null,
            
            'is_paid' => $appointmentData['is_paid'] ?? false,
            'amount' => $appointmentData['amount'] ?? 0,
            'discount_amount' => $appointmentData['discount_amount'] ?? 0,
            'final_amount' => $appointmentData['final_amount'] ?? 0,
            'promo_code' => $appointmentData['promo_code'] ?? null,
            'payment_status' => $this->mapPaymentStatus($appointmentData),
            'payment_method' => $appointmentData['payment']['payment_method'] ?? null,
            'paid_at' => !empty($appointmentData['payment']['paid_at']) 
                ? Carbon::parse($appointmentData['payment']['paid_at']) 
                : null,
            
            'synced_from_bansal_at' => now(),
            'last_synced_at' => now(),
            'sync_status' => 'synced',
        ]);

        Log::info('Created new appointment', [
            'bansal_id' => $bansalId,
            'crm_id' => $appointment->id,
            'client_id' => $client?->id,
            'consultant_id' => $consultant?->id
        ]);

        return 'new';
    }

    /**
     * Map location to inperson_address (legacy compatibility)
     */
    protected function mapInpersonAddress(string $location): ?int
    {
        return match($location) {
            'adelaide' => 1,
            'melbourne' => 2,
            default => null
        };
    }

    /**
     * Map service_id from Bansal data
     */
    protected function mapServiceId(array $appointmentData): ?int
    {
        // Check if paid
        if (!empty($appointmentData['is_paid']) && $appointmentData['is_paid'] === true) {
            return 1; // Paid
        }
        
        if (!empty($appointmentData['final_amount']) && $appointmentData['final_amount'] > 0) {
            return 1; // Paid
        }

        return 2; // Free
    }

    /**
     * Map noe_id from enquiry_type
     */
    protected function mapNoeId(array $appointmentData): ?int
    {
        $enquiryType = $appointmentData['enquiry_type'] ?? null;

        return match($enquiryType) {
            'tr' => 1,
            'jrp' => 2,
            'skill_assessment' => 3,
            'tourist' => 4,
            'education' => 5,
            'pr_complex' => 6,
            default => null
        };
    }

    /**
     * Map status from Bansal to CRM
     */
    protected function mapStatus(string $bansalStatus): string
    {
        return match($bansalStatus) {
            'pending' => 'pending',
            'confirmed' => 'confirmed',
            'completed' => 'completed',
            'cancelled' => 'cancelled',
            'no_show' => 'no_show',
            default => 'pending'
        };
    }

    /**
     * Map payment status
     */
    protected function mapPaymentStatus(array $appointmentData): ?string
    {
        if (empty($appointmentData['payment'])) {
            return null;
        }

        $paymentStatus = $appointmentData['payment']['status'] ?? null;

        return match($paymentStatus) {
            'completed', 'succeeded' => 'completed',
            'pending', 'processing' => 'pending',
            'failed' => 'failed',
            'refunded' => 'refunded',
            default => null
        };
    }

    /**
     * Backfill historical appointments
     */
    public function backfillHistoricalData(Carbon $startDate, Carbon $endDate): array
    {
        $this->syncLog = AppointmentSyncLog::create([
            'sync_type' => 'backfill',
            'started_at' => now(),
            'status' => 'running'
        ]);

        $stats = [
            'fetched' => 0,
            'new' => 0,
            'skipped' => 0,
            'failed' => 0
        ];

        try {
            Log::info('Starting backfill', [
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString()
            ]);

            $page = 1;
            $hasMore = true;

            while ($hasMore) {
                $response = $this->apiClient->getAppointmentsByDateRange(
                    $startDate->toDateString(),
                    $endDate->toDateString(),
                    $page
                );

                $appointments = $response['data'] ?? [];
                $pagination = $response['pagination'] ?? [];

                $stats['fetched'] += count($appointments);

                foreach ($appointments as $appointmentData) {
                    try {
                        $result = $this->processAppointment($appointmentData);
                        if ($result === 'new') {
                            $stats['new']++;
                        } else {
                            $stats['skipped']++;
                        }
                    } catch (Exception $e) {
                        $stats['failed']++;
                        Log::error('Backfill: Failed to process appointment', [
                            'appointment_id' => $appointmentData['id'] ?? null,
                            'error' => $e->getMessage()
                        ]);
                    }
                }

                // Check if more pages
                $hasMore = !empty($pagination['current_page']) && 
                          !empty($pagination['last_page']) && 
                          $pagination['current_page'] < $pagination['last_page'];
                $page++;

                // Add delay between pages to avoid rate limiting
                if ($hasMore) {
                    sleep(2);
                }
            }

            $this->syncLog->update([
                'completed_at' => now(),
                'status' => 'success',
                'appointments_fetched' => $stats['fetched'],
                'appointments_new' => $stats['new'],
                'appointments_skipped' => $stats['skipped'],
                'appointments_failed' => $stats['failed']
            ]);

            Log::info('Backfill completed', $stats);

            return $stats;
        } catch (Exception $e) {
            $this->syncLog->update([
                'completed_at' => now(),
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Push status update to Bansal API.
     */
    public function pushStatusUpdate(BookingAppointment $appointment, string $status, ?string $reason = null): ?array
    {
        if (empty($appointment->bansal_appointment_id)) {
            Log::warning('Skipping Bansal status update: missing bansal_appointment_id', [
                'appointment_id' => $appointment->id,
                'status' => $status,
            ]);
            return null;
        }

        $type = match ($status) {
            'cancelled' => 'cancel',
            'completed' => 'complete',
            'confirmed' => 'confirm',
            default => null,
        };

        if ($type === null) {
            Log::warning('Skipping Bansal status update: unsupported status type', [
                'appointment_id' => $appointment->id,
                'status' => $status,
            ]);
            return null;
        }

        try {
            $response = $this->apiClient->updateAppointmentStatus(
                $appointment->bansal_appointment_id,
                $type,
                $reason
            );

            Log::info('Bansal appointment status updated', [
                'appointment_id' => $appointment->id,
                'bansal_appointment_id' => $appointment->bansal_appointment_id,
                'status' => $status,
                'response' => $response,
            ]);

            return $response;
        } catch (Exception $e) {
            Log::error('Failed to push status update to Bansal API', [
                'appointment_id' => $appointment->id,
                'bansal_appointment_id' => $appointment->bansal_appointment_id,
                'status' => $status,
                'reason' => $reason,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}

