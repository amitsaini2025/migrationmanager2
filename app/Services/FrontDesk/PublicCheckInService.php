<?php

namespace App\Services\FrontDesk;

use App\Models\Admin;
use App\Models\BookingAppointment;
use App\Models\Branch;
use App\Models\CheckinHistory;
use App\Models\CheckinLog;
use App\Models\FrontDeskCheckIn;
use App\Models\Staff;
use App\Services\ClientReferenceService;
use App\Services\LeadEnquiryProfileService;
use App\Support\StaffClientVisibility;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class PublicCheckInService
{
    public function __construct(
        private readonly CheckInLookupService $lookup,
        private readonly CheckInAppointmentService $appointments,
        private readonly CheckInNotificationService $notifier,
        private readonly ClientReferenceService $refService,
        private readonly LeadEnquiryProfileService $enquiryProfile,
    ) {}

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function matches(string $phone, ?string $email): Collection
    {
        return $this->lookup->lookup($phone, $email)
            ->reject(fn (Admin $record) => $this->isHidden($record))
            ->map(fn (Admin $record) => $this->lookup->formatForWizard($record))
            ->values();
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function todaysAppointments(string $phone, ?string $email, int $adminId): Collection
    {
        $this->selectableRecord($phone, $email, $adminId, null);

        return $this->appointments->getTodaysAppointments($adminId)
            ->map(fn (BookingAppointment $appointment) => $this->appointments->formatForWizard($appointment))
            ->values();
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array{message: string}
     */
    public function submit(array $validated, ?string $ip): array
    {
        $adminId = isset($validated['admin_id']) && (int) $validated['admin_id'] > 0
            ? (int) $validated['admin_id']
            : null;
        $adminType = isset($validated['admin_type']) && $validated['admin_type'] !== ''
            ? (string) $validated['admin_type']
            : null;

        if (($adminId === null) !== ($adminType === null)) {
            throw ValidationException::withMessages([
                'admin_id' => 'Choose a match, or continue without one.',
            ]);
        }

        if ($adminId !== null && $adminType !== null) {
            $this->selectableRecord(
                (string) $validated['phone'],
                $validated['email'] ?? null,
                $adminId,
                $adminType
            );
        }

        $this->guardOtherReason($validated['visit_reason'] ?? null, $validated['visit_notes'] ?? null);

        $clientId = $adminType === 'client' ? $adminId : null;
        $leadId = $adminType === 'lead' ? $adminId : null;
        $appointmentId = $this->appointmentForRecord($validated['appointment_id'] ?? null, $adminId);

        try {
            DB::beginTransaction();

            $checkIn = FrontDeskCheckIn::create([
                'admin_id' => null,
                'phone_normalized' => $this->lookup->normalizePhone((string) $validated['phone']),
                'email' => $validated['email'] ?? null,
                'client_id' => $clientId,
                'lead_id' => $leadId,
                'appointment_id' => $appointmentId,
                'claimed_appointment' => (bool) ($validated['claimed_appointment'] ?? false),
                'visit_reason' => $validated['visit_reason'] ?? null,
                'visit_notes' => $validated['visit_notes'] ?? null,
                'metadata' => [
                    'submitted_at' => now()->toIso8601String(),
                    'ip' => $ip,
                    'source' => 'public',
                ],
            ]);

            $this->notifier->notifyPublicArrival($checkIn);
            $reasonLabel = $this->reasonLabel($validated['visit_reason'] ?? null);

            $payload = [
                'client_id' => $clientId ?? $leadId,
                'user_id' => null,
                'visit_purpose' => $reasonLabel,
                'office' => $this->officeIdFor(null),
                'contact_type' => $clientId ? 'Client' : ($leadId ? 'Lead' : 'Walk-in'),
                'status' => 0,
                'date' => now()->toDateString(),
            ];
            if ($clientId === null && $leadId === null) {
                $payload['walk_in_phone'] = $this->lookup->normalizePhone((string) $validated['phone']);
                $payload['walk_in_email'] = $validated['email'] ?? null;
            }

            $checkinLog = CheckinLog::create($payload);
            $this->writeHistory('Check-in created via public front-desk page', null, $checkinLog->id);

            $checkIn->update([
                'metadata' => array_merge($checkIn->metadata ?? [], [
                    'checkin_log_id' => $checkinLog->id,
                ]),
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('[PublicCheckIn] Submit failed', [
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }

        return [
            'message' => 'You are checked in. Please take a seat — our team will be with you shortly.',
        ];
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array{message: string}
     */
    public function createLead(array $validated, ?string $ip): array
    {
        $this->guardOtherReason($validated['visit_reason'] ?? null, $validated['visit_notes'] ?? null);

        $phone = (string) $validated['phone'];
        $email = trim((string) ($validated['email'] ?? ''));
        $email = $email !== '' ? $email : null;
        $timestamp = time();
        $errors = [];

        if (Admin::phoneIsTaken($phone)) {
            if (Admin::normalizePhoneDigitsForUniqueness($phone) === '4444444444') {
                $phone = '4444444444_'.$timestamp;
            } else {
                $errors['phone'] = ['This phone number is already registered. Please look up the existing record.'];
            }
        }

        if ($email !== null && Admin::emailIsTaken($email)) {
            if (Admin::normalizeEmailForUniqueness($email) === 'demo@gmail.com') {
                $email = 'demo_'.$timestamp.'@gmail.com';
            } else {
                $errors['email'] = ['This email address is already registered. Please look up the existing record.'];
            }
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }

        $recorderId = $this->receptionStaffId();

        try {
            DB::beginTransaction();

            $ref = $this->refService->generateClientReference($validated['first_name']);

            $lead = Admin::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'phone' => $phone,
                'email' => $email,
                'type' => 'lead',
                'client_id' => $ref['client_id'],
                'client_counter' => $ref['client_counter'],
                'user_id' => null,
                'password' => Hash::make('LEAD_PLACEHOLDER'),
                'status' => 1,
                'lead_status' => 'new',
                'is_archived' => 0,
                'is_deleted' => null,
                'verified' => 0,
                'cp_status' => 0,
                'cp_code_verify' => 0,
                'australian_study' => 0,
                'specialist_education' => 0,
                'regional_study' => 0,
                'is_company' => 0,
                'manual_form_fill' => 1,
                'visa_type' => null,
            ]);

            $lead->forceFill($this->enquiryProfile->adminAttributesFromPayload($validated))->save();
            if ($recorderId !== null) {
                $this->enquiryProfile->syncRelatedRecords($lead, $recorderId, $validated);
            }

            $checkIn = FrontDeskCheckIn::create([
                'admin_id' => null,
                'phone_normalized' => $this->lookup->normalizePhone($phone),
                'email' => $email,
                'lead_id' => $lead->id,
                'visit_reason' => $validated['visit_reason'] ?? null,
                'visit_notes' => $validated['visit_notes'] ?? null,
                'metadata' => [
                    'submitted_at' => now()->toIso8601String(),
                    'ip' => $ip,
                    'source' => 'public',
                    'new_lead' => true,
                ],
            ]);

            $this->notifier->notifyPublicArrival($checkIn);
            $reasonLabel = $this->reasonLabel($validated['visit_reason'] ?? null);

            $checkinLog = CheckinLog::create([
                'client_id' => $lead->id,
                'user_id' => null,
                'visit_purpose' => $reasonLabel,
                'office' => $this->officeIdFor(null),
                'contact_type' => 'Lead',
                'status' => 0,
                'date' => now()->toDateString(),
            ]);

            $this->writeHistory('Check-in created via public front-desk page (new lead)', null, $checkinLog->id);

            $checkIn->update([
                'metadata' => array_merge($checkIn->metadata ?? [], [
                    'checkin_log_id' => $checkinLog->id,
                ]),
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('[PublicCheckIn] createLead failed', [
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }

        return [
            'message' => 'You are checked in. Please take a seat — our team will be with you shortly.',
        ];
    }

    private function selectableRecord(string $phone, ?string $email, int $adminId, ?string $adminType): Admin
    {
        $match = $this->lookup->lookup($phone, $email)->first(function (Admin $record) use ($adminId, $adminType) {
            if ((int) $record->id !== $adminId) {
                return false;
            }

            return $adminType === null || $record->type === $adminType;
        });

        if (! $match || $this->isHidden($match)) {
            throw ValidationException::withMessages([
                'admin_id' => 'That record does not match the phone number entered.',
            ]);
        }

        return $match;
    }

    private function isHidden(Admin $record): bool
    {
        return StaffClientVisibility::isSuperAdminOnlyLockedClient($record->type, $record->client_id);
    }

    private function guardOtherReason(?string $reason, ?string $notes): void
    {
        if ($reason === 'other' && trim((string) $notes) === '') {
            throw ValidationException::withMessages([
                'visit_notes' => 'Please add a note when you choose Other.',
            ]);
        }
    }

    private function appointmentForRecord(mixed $appointmentId, ?int $adminId): ?int
    {
        if (empty($appointmentId) || $adminId === null) {
            return null;
        }

        $appointment = BookingAppointment::query()->find((int) $appointmentId);
        if ($appointment && (int) $appointment->client_id === $adminId) {
            return (int) $appointment->id;
        }

        return null;
    }

    private function reasonLabel(?string $reason): string
    {
        if ($reason === null || $reason === '') {
            return 'Front-desk check-in';
        }

        return FrontDeskCheckIn::visitReasons()[$reason] ?? $reason;
    }

    private function receptionStaffId(): ?int
    {
        $receptionId = (int) config('constants.reception_user_id', 0);
        if ($receptionId > 0 && Staff::query()->whereKey($receptionId)->exists()) {
            return $receptionId;
        }

        return null;
    }

    private function officeIdFor(?int $staffId): ?int
    {
        if ($staffId !== null) {
            $officeId = Staff::query()->whereKey($staffId)->value('office_id');
            if ($officeId) {
                return (int) $officeId;
            }
        }

        $configured = config('lead_migrate_invite.default_office_id');
        if ($configured !== null && $configured !== '') {
            $officeId = (int) $configured;
            if ($officeId > 0 && Branch::query()->whereKey($officeId)->exists()) {
                return $officeId;
            }
        }

        if (! Schema::hasTable('branches')) {
            return null;
        }

        $first = Branch::query()->orderBy('id')->value('id');

        return $first ? (int) $first : null;
    }

    private function writeHistory(string $subject, ?int $staffId, int $checkinId): void
    {
        if (! Schema::hasTable('checkin_histories')) {
            return;
        }

        CheckinHistory::create([
            'subject' => $subject,
            'created_by' => $staffId,
            'checkin_id' => $checkinId,
        ]);
    }
}
