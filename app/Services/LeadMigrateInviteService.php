<?php

namespace App\Services;

use App\Mail\LeadMigrateInviteMail;
use App\Models\Admin;
use App\Models\ClientContact;
use App\Models\ClientEmail;
use App\Models\ClientExperience;
use App\Models\ClientQualification;
use App\Models\ClientVisaCountry;
use App\Models\Lead;
use App\Models\LeadMigrateInvite;
use App\Models\Matter;
use App\Models\Staff;
use App\Services\Sms\UnifiedSmsManager;
use App\Support\LeadMigrateInviteChannels;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class LeadMigrateInviteService
{
    public function __construct(
        private UnifiedSmsManager $smsManager,
        private ClientReferenceService $clientReferenceService,
        private PublicLeadInquiryService $publicLeadInquiryService,
    ) {}

    /**
     * @return array{success: bool, message: string}
     */
    public function sendInvite(string $channel, ?string $email, ?string $phone, ?int $sentBy): array
    {
        if (! in_array($channel, LeadMigrateInviteChannels::channels(), true)) {
            return [
                'success' => false,
                'message' => 'Please choose Lead email or Lead contact number.',
            ];
        }

        if ($channel === LeadMigrateInviteChannels::CHANNEL_EMAIL) {
            return $this->sendEmailInvite($email, $sentBy);
        }

        return $this->sendSmsInvite($phone, $sentBy);
    }

    /**
     * @return array{success: bool, message: string}
     */
    private function sendEmailInvite(?string $email, ?int $sentBy): array
    {
        $email = Admin::sanitizeEmailAddress($email);
        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'Please enter a valid lead email address.',
            ];
        }

        if (Admin::emailIsTaken($email, null, true)) {
            return [
                'success' => false,
                'message' => 'Email already exist in our crm. Pls try other.',
            ];
        }

        $plainToken = LeadMigrateInvite::generateToken();
        $invite = LeadMigrateInvite::query()->create([
            'token_hash' => LeadMigrateInvite::hashToken($plainToken),
            'channel' => LeadMigrateInviteChannels::CHANNEL_EMAIL,
            'sent_to_email' => $email,
            'sent_to_phone' => null,
            'sent_to_phone_e164' => null,
            'sent_by' => $sentBy,
            'source' => LeadMigrateInviteChannels::SOURCE_EMAIL,
            'expires_at' => now()->addHours((int) config('lead_migrate_invite.token_ttl_hours', 168)),
        ]);

        $url = route('public.lead-migrate-invite.show', ['token' => $plainToken]);

        try {
            $this->sendInviteMailable($email, new LeadMigrateInviteMail($url));
        } catch (\Throwable $e) {
            Log::error('Failed to send migrate lead invite email', [
                'invite_id' => $invite->id,
                'error' => $e->getMessage(),
            ]);
            $invite->update(['invalidated_at' => now()]);

            return [
                'success' => false,
                'message' => 'Failed to send the invite email. Please try again.',
            ];
        }

        $this->invalidateUnusedForContact(
            LeadMigrateInviteChannels::CHANNEL_EMAIL,
            $email,
            null,
            (int) $invite->id,
        );

        return [
            'success' => true,
            'message' => 'Migration invite link sent to '.$email,
        ];
    }

    /**
     * @return array{success: bool, message: string}
     */
    private function sendSmsInvite(?string $phone, ?int $sentBy): array
    {
        $national = LeadMigrateInviteChannels::normalizeAustralianMobile($phone);
        if ($national === null) {
            return [
                'success' => false,
                'message' => 'Please enter a valid Australian mobile number (e.g. 04XX XXX XXX).',
            ];
        }

        if (Admin::phoneIsTaken($national, null, true)) {
            return [
                'success' => false,
                'message' => 'Contact number already exist in our crm. Pls try other.',
            ];
        }

        $destination = LeadMigrateInviteChannels::smsDestinationFromNational($national);
        $plainToken = LeadMigrateInvite::generateToken();
        $invite = LeadMigrateInvite::query()->create([
            'token_hash' => LeadMigrateInvite::hashToken($plainToken),
            'channel' => LeadMigrateInviteChannels::CHANNEL_SMS,
            'sent_to_email' => null,
            'sent_to_phone' => $national,
            'sent_to_phone_e164' => $destination,
            'sent_by' => $sentBy,
            'source' => LeadMigrateInviteChannels::SOURCE_SMS,
            'expires_at' => now()->addHours((int) config('lead_migrate_invite.token_ttl_hours', 168)),
        ]);

        $url = route('public.lead-migrate-invite.show', ['token' => $plainToken]);
        $message = LeadMigrateInviteChannels::smsText($url);

        try {
            $result = $this->smsManager->sendSms($destination, $message, 'notification', [
                'sender_id' => $sentBy,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to send migrate lead invite SMS', [
                'invite_id' => $invite->id,
                'error' => $e->getMessage(),
            ]);
            $invite->update(['invalidated_at' => now()]);

            return [
                'success' => false,
                'message' => 'Failed to send the invite SMS. Please try again.',
            ];
        }

        if (! ($result['success'] ?? false)) {
            $invite->update(['invalidated_at' => now()]);

            return [
                'success' => false,
                'message' => (string) ($result['message'] ?? 'Failed to send the invite SMS. Please try again.'),
            ];
        }

        $this->invalidateUnusedForContact(
            LeadMigrateInviteChannels::CHANNEL_SMS,
            null,
            $national,
            (int) $invite->id,
        );

        return [
            'success' => true,
            'message' => 'Migration invite link sent to '.$national,
        ];
    }

    private function sendInviteMailable(string $email, LeadMigrateInviteMail $mailable): void
    {
        try {
            Mail::mailer('failover')->to($email)->send($mailable);
        } catch (\Throwable $e) {
            Log::warning('Migrate lead invite SES/failover send failed; retrying via Zoho', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);

            Mail::mailer('zoho')->to($email)->send($mailable);
        }
    }

    public function findUsableByToken(string $token): ?LeadMigrateInvite
    {
        if ($token === '') {
            return null;
        }

        $invite = LeadMigrateInvite::query()
            ->where('token_hash', LeadMigrateInvite::hashToken($token))
            ->first();

        if (! $invite || ! $invite->isUsable()) {
            return null;
        }

        return $invite;
    }

    public function findByToken(string $token): ?LeadMigrateInvite
    {
        if ($token === '') {
            return null;
        }

        return LeadMigrateInvite::query()
            ->where('token_hash', LeadMigrateInvite::hashToken($token))
            ->first();
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function submit(LeadMigrateInvite $invite, array $payload, ?string $ip = null, ?string $userAgent = null): Admin
    {
        return DB::transaction(function () use ($invite, $payload, $ip, $userAgent): Admin {
            $locked = LeadMigrateInvite::query()
                ->where('id', $invite->id)
                ->lockForUpdate()
                ->first();

            if (! $locked || ! $locked->isUsable()) {
                throw ValidationException::withMessages([
                    'token' => ['This migration link has expired or has already been used.'],
                ]);
            }

            $email = Admin::sanitizeEmailAddress((string) ($payload['email'] ?? ''));
            $phoneNational = LeadMigrateInviteChannels::normalizeAustralianMobile(
                (string) ($payload['phone'] ?? '')
            );

            if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw ValidationException::withMessages([
                    'email' => ['A valid email address is required.'],
                ]);
            }

            if ($phoneNational === null || $phoneNational === '') {
                throw ValidationException::withMessages([
                    'phone' => ['A valid Australian mobile number is required.'],
                ]);
            }

            if (Admin::emailIsTaken($email, null, true)) {
                throw ValidationException::withMessages([
                    'email' => ['Email already exist in our crm. Pls try other.'],
                ]);
            }

            if (Admin::phoneIsTaken($phoneNational, null, true)) {
                throw ValidationException::withMessages([
                    'phone' => ['Contact number already exist in our crm. Pls try other.'],
                ]);
            }

            $payload['email'] = $email;
            $payload['phone'] = $phoneNational;

            $admin = $this->createLeadFromPayload($locked, $payload);

            $locked->update([
                'lead_id' => $admin->id,
                'submitted_payload' => $payload,
                'used_at' => now(),
                'submitted_at' => now(),
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);

            return $admin;
        });
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function createLeadFromPayload(LeadMigrateInvite $invite, array $payload): Admin
    {
        $firstName = trim((string) ($payload['first_name'] ?? ''));
        $lastName = trim((string) ($payload['last_name'] ?? ''));
        $email = (string) $payload['email'];
        $phone = (string) $payload['phone'];
        $assigneeStaffId = $this->resolveAssigneeStaffId($invite->sent_by);

        $ref = $this->clientReferenceService->generateClientReference($firstName !== '' ? $firstName : 'Lead');

        $enquiryBits = array_filter([
            trim((string) ($payload['city'] ?? '')),
            trim((string) ($payload['enquiry_notes'] ?? '')),
        ]);

        $adminData = [
            'user_id' => $assigneeStaffId,
            'password' => Hash::make('LEAD_PLACEHOLDER'),
            'client_counter' => $ref['client_counter'],
            'client_id' => $ref['client_id'],
            'status' => LeadFollowUpNoteService::adminsStatusForLeadStatus('new'),
            'lead_status' => 'new',
            'followup_date' => null,
            'type' => 'lead',
            'is_archived' => 0,
            'is_deleted' => null,
            'verified' => 0,
            'cp_status' => 0,
            'cp_code_verify' => 0,
            'australian_study' => 0,
            'specialist_education' => 0,
            'regional_study' => 0,
            'is_company' => 0,
            'first_name' => $firstName !== '' ? $firstName : 'Unknown',
            'last_name' => $lastName,
            'gender' => null,
            'dob' => $this->nullableDate($payload['dob'] ?? null),
            'marital_status' => $this->nullableString($payload['marital_status'] ?? null),
            'phone' => $phone,
            'country_code' => '+61',
            'email' => $email,
            'address' => $enquiryBits !== [] ? implode(' | ', $enquiryBits) : null,
            'city' => $this->nullableString($payload['city'] ?? null),
            'country' => $this->mapLivingInCountry($payload['currently_living_in'] ?? null),
            'country_passport' => null,
            'visa_type' => null,
            'manual_form_fill' => 1,
            'source' => $invite->source,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $adminId = DB::table('admins')->insertGetId($adminData);
        $admin = Admin::query()->findOrFail($adminId);

        ClientContact::create([
            'admin_id' => $assigneeStaffId,
            'client_id' => $admin->id,
            'contact_type' => PublicLeadInquiryService::FORM_CONTACT_TYPE,
            'phone' => $phone,
            'country_code' => '+61',
            'is_verified' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        ClientEmail::create([
            'admin_id' => $assigneeStaffId,
            'client_id' => $admin->id,
            'email_type' => PublicLeadInquiryService::FORM_EMAIL_TYPE,
            'email' => $email,
            'is_verified' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->createQualificationFromPayload($admin->id, $assigneeStaffId, $payload);
        $this->createExperienceFromPayload($admin->id, $assigneeStaffId, $payload);
        $this->createVisaFromPayload($admin, $assigneeStaffId, $payload);

        $lead = Lead::query()->find($admin->id);
        if ($lead) {
            app(LeadFollowUpNoteService::class)->syncNotesForLead($lead, null);
        }

        return $admin->fresh();
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function createQualificationFromPayload(int $clientId, int $assigneeStaffId, array $payload): void
    {
        $level = trim((string) ($payload['qualification'] ?? ''));
        if ($level === '' || ! in_array($level, LeadMigrateInviteChannels::qualificationLevels(), true)) {
            return;
        }

        ClientQualification::create([
            'admin_id' => $assigneeStaffId,
            'client_id' => $clientId,
            'level' => $level,
            'name' => null,
            'finish_date' => $this->nullableDate($payload['qualification_date'] ?? null),
            'relevant_qualification' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Map migrate-form work fields into CRM Work Experience (client_experiences).
     *
     * @param  array<string, mixed>  $payload
     */
    private function createExperienceFromPayload(int $clientId, int $assigneeStaffId, array $payload): void
    {
        $occupation = trim((string) ($payload['occupation'] ?? ''));
        $workLocation = trim((string) ($payload['work_location'] ?? ''));
        $yearsExperience = trim((string) ($payload['years_experience'] ?? ''));

        if ($occupation === '' && $workLocation === '' && $yearsExperience === '') {
            return;
        }

        ClientExperience::create([
            'admin_id' => $assigneeStaffId,
            'client_id' => $clientId,
            'job_title' => $occupation !== '' ? $occupation : null,
            'job_country' => LeadMigrateInviteChannels::jobCountryFromWorkLocation($workLocation),
            'job_state' => $workLocation !== '' ? $workLocation : null,
            'job_type' => $yearsExperience !== '' ? $yearsExperience : null,
            'relevant_experience' => 0,
            'fte_multiplier' => 1.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Map migrate-form visa fields into CRM Visa Information (client_visa_countries).
     *
     * @param  array<string, mixed>  $payload
     */
    private function createVisaFromPayload(Admin $admin, int $assigneeStaffId, array $payload): void
    {
        $currentVisa = trim((string) ($payload['current_visa'] ?? ''));
        $expiry = $this->nullableDate($payload['visa_expiry'] ?? null);

        if ($currentVisa === '' && $expiry === null) {
            return;
        }

        $matterId = $this->resolveMatterIdForCurrentVisa($currentVisa);

        ClientVisaCountry::create([
            'admin_id' => $assigneeStaffId,
            'client_id' => $admin->id,
            'visa_type' => $matterId,
            'visa_description' => $currentVisa !== '' ? $currentVisa : null,
            'visa_expiry_date' => $expiry,
            'visa_grant_date' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($matterId !== null) {
            DB::table('admins')->where('id', $admin->id)->update([
                'visa_type' => $matterId,
                'updated_at' => now(),
            ]);
        }
    }

    private function resolveMatterIdForCurrentVisa(string $currentVisa): ?int
    {
        if ($currentVisa === '' || ! Schema::hasTable('matters')) {
            return null;
        }

        foreach (LeadMigrateInviteChannels::matterMatchHintsForCurrentVisa($currentVisa) as $hint) {
            $matter = Matter::query()
                ->where(function ($q) use ($hint): void {
                    $q->where('nick_name', $hint)
                        ->orWhere('title', $hint)
                        ->orWhere('nick_name', 'like', '%'.$hint.'%')
                        ->orWhere('title', 'like', '%'.$hint.'%');
                })
                ->orderBy('id')
                ->first();

            if ($matter) {
                return (int) $matter->id;
            }
        }

        return null;
    }

    private function resolveAssigneeStaffId(?int $sentBy): int
    {
        if ($sentBy !== null && Staff::query()->whereKey($sentBy)->exists()) {
            return $sentBy;
        }

        return $this->publicLeadInquiryService->resolveDefaultAssigneeStaffId();
    }

    private function invalidateUnusedForContact(
        string $channel,
        ?string $email,
        ?string $phone,
        int $exceptId,
    ): void {
        $query = LeadMigrateInvite::query()
            ->where('channel', $channel)
            ->whereNull('used_at')
            ->whereNull('invalidated_at')
            ->where('id', '!=', $exceptId);

        if ($channel === LeadMigrateInviteChannels::CHANNEL_EMAIL && $email !== null) {
            $query->whereRaw('LOWER(TRIM(sent_to_email)) = ?', [Admin::normalizeEmailForUniqueness($email)]);
        }

        if ($channel === LeadMigrateInviteChannels::CHANNEL_SMS && $phone !== null) {
            $query->where('sent_to_phone', $phone);
        }

        $query->update(['invalidated_at' => now()]);
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) ($value ?? ''));

        return $value !== '' ? $value : null;
    }

    private function nullableDate(mixed $value): ?string
    {
        $value = trim((string) ($value ?? ''));
        if ($value === '') {
            return null;
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable) {
            return $value;
        }
    }

    private function mapLivingInCountry(mixed $value): ?string
    {
        $value = trim((string) ($value ?? ''));

        return match ($value) {
            'Australia' => 'Australia',
            'Outside Australia' => null,
            default => $this->nullableString($value),
        };
    }

    /**
     * @return array<string, mixed>
     */
    public static function publicFormRules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:70'],
            'last_name' => ['required', 'string', 'max:70'],
            'phone' => ['required', 'string', 'max:25'],
            'email' => ['required', 'email', 'max:150'],
            'dob' => ['nullable', 'date'],
            'marital_status' => ['nullable', 'string', 'max:70'],
            'currently_living_in' => ['required', 'string', 'max:70'],
            'city' => ['nullable', 'string', 'max:120'],
            'current_visa' => ['nullable', 'string', Rule::in(LeadMigrateInviteChannels::currentVisaOptions())],
            'visa_expiry' => ['nullable', 'date'],
            'qualification' => ['nullable', 'string', Rule::in(LeadMigrateInviteChannels::qualificationLevels())],
            'qualification_date' => ['nullable', 'date'],
            'occupation' => ['nullable', 'string', 'max:130'],
            'work_location' => ['nullable', 'string', Rule::in(LeadMigrateInviteChannels::workExperienceLocations())],
            'years_experience' => ['nullable', 'string', Rule::in(LeadMigrateInviteChannels::yearsOfExperienceOptions())],
            'enquiry_notes' => ['nullable', 'string', 'max:1500'],
        ];
    }
}
