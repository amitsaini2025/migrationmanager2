<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\ClientExperience;
use App\Models\ClientQualification;
use App\Models\ClientVisaCountry;
use App\Models\Matter;
use App\Support\LeadMigrateInviteChannels;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

/**
 * Shared mapping for Migration Client Enquiry-style fields onto a CRM lead.
 */
class LeadEnquiryProfileService
{
    /**
     * Validation rules for enquiry profile fields (excluding contact identity fields).
     *
     * @return array<string, mixed>
     */
    public static function detailRules(): array
    {
        return [
            'dob' => ['nullable', 'date'],
            'marital_status' => ['nullable', 'string', 'max:70'],
            'city' => ['nullable', 'string', 'max:120'],
            'current_visa' => ['nullable', 'string', Rule::in(LeadMigrateInviteChannels::currentVisaOptions())],
            'visa_expiry' => ['nullable', 'date'],
            'qualification' => ['nullable', 'string', Rule::in(LeadMigrateInviteChannels::qualificationLevels())],
            'qualification_date' => ['nullable', 'date'],
            'occupation' => ['nullable', 'string', 'max:130'],
        ];
    }

    /**
     * Admin column values derived from enquiry payload.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function adminAttributesFromPayload(array $payload): array
    {
        $city = $this->nullableString($payload['city'] ?? null);

        return [
            'dob' => $this->nullableDate($payload['dob'] ?? null),
            'marital_status' => $this->nullableString($payload['marital_status'] ?? null),
            'city' => $city,
            'address' => $city,
            'country' => null,
            'country_code' => '+61',
        ];
    }

    /**
     * Persist qualification / experience / visa rows for the lead.
     *
     * @param  array<string, mixed>  $payload
     */
    public function syncRelatedRecords(Admin $admin, int $assigneeStaffId, array $payload): void
    {
        $this->createQualification($admin->id, $assigneeStaffId, $payload);
        $this->createExperience($admin->id, $assigneeStaffId, $payload);
        $this->createVisa($admin, $assigneeStaffId, $payload);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function createQualification(int $clientId, int $assigneeStaffId, array $payload): void
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
     * @param  array<string, mixed>  $payload
     */
    private function createExperience(int $clientId, int $assigneeStaffId, array $payload): void
    {
        $occupation = trim((string) ($payload['occupation'] ?? ''));
        if ($occupation === '') {
            return;
        }

        ClientExperience::create([
            'admin_id' => $assigneeStaffId,
            'client_id' => $clientId,
            'job_title' => $occupation,
            'job_country' => null,
            'job_state' => null,
            'job_type' => null,
            'relevant_experience' => 0,
            'fte_multiplier' => 1.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function createVisa(Admin $admin, int $assigneeStaffId, array $payload): void
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
}
