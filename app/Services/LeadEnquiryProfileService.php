<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\ClientExperience;
use App\Models\ClientQualification;
use App\Models\ClientTestScore;
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
     * "How did you hear about us?" options for enquiry / check-in visit forms.
     *
     * @return list<string>
     */
    public static function heardAboutOptions(): array
    {
        return [
            'Facebook Advert',
            'Whatsapp',
            'Social Media',
            'Google',
            'Other Online Sources',
            'Tiktok',
            'Friends & Relatives',
            'Visiting card QR',
            'TV Advertisement',
            'Newspaper Advertisement',
            'Agent',
            'Walk-In',
            'Email',
            'Phone',
            'Existing Client',
            'Others',
        ];
    }

    /**
     * Validation rules for enquiry profile fields (excluding contact identity fields).
     *
     * @return array<string, mixed>
     */
    public static function detailRules(): array
    {
        return [
            'dob' => ['required', 'date'],
            'marital_status' => ['required', 'string', Rule::in(['Single', 'Married', 'De facto', 'Separated / Divorced', 'Widowed'])],
            'gender' => ['required', 'string', Rule::in(['Male', 'Female', 'Other'])],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:120'],
            'current_visa' => ['nullable', 'string', Rule::in(LeadMigrateInviteChannels::currentVisaOptions())],
            'visa_expiry' => ['nullable', 'date'],
            'qualification' => ['nullable', 'string', Rule::in(LeadMigrateInviteChannels::qualificationLevels())],
            'qualification_type' => ['nullable', 'string', Rule::in(['completed', 'pursuing'])],
            'qualification_date' => ['nullable', 'date'],
            'occupation' => ['nullable', 'string', 'max:130'],
            'has_job_experience' => ['nullable', 'string', Rule::in(['yes', 'no'])],
            'job_experience_years' => ['nullable', 'required_if:has_job_experience,yes', 'numeric', 'min:0', 'max:60'],
            'ielts_passed' => ['nullable', 'string', Rule::in(['yes', 'no'])],
            'ielts_score' => ['nullable', 'required_if:ielts_passed,yes', 'numeric', 'min:0', 'max:9'],
            'pte_passed' => ['nullable', 'string', Rule::in(['yes', 'no'])],
            'pte_score' => ['nullable', 'required_if:pte_passed,yes', 'numeric', 'min:10', 'max:90'],
            'source' => ['required', 'string', Rule::in(self::heardAboutOptions())],
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
        $address = $this->nullableString($payload['address'] ?? null);

        return [
            'dob' => $this->nullableDate($payload['dob'] ?? null),
            'marital_status' => $this->nullableString($payload['marital_status'] ?? null),
            'gender' => $this->nullableString($payload['gender'] ?? null),
            'city' => $city,
            // Prefer explicit address; fall back to city so older submissions still populate address.
            'address' => $address ?? $city,
            'source' => $this->nullableString($payload['source'] ?? null),
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
        $this->createEnglishTestScores($admin->id, $assigneeStaffId, $payload);
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
        $hasExperience = ($payload['has_job_experience'] ?? null) === 'yes';
        $years = $payload['job_experience_years'] ?? null;
        $yearsLabel = ($hasExperience && $years !== null && $years !== '')
            ? trim((string) $years).' years'
            : null;

        if ($occupation === '' && $yearsLabel === null) {
            return;
        }

        ClientExperience::create([
            'admin_id' => $assigneeStaffId,
            'client_id' => $clientId,
            'job_title' => $occupation !== '' ? $occupation : 'Job experience',
            'job_country' => null,
            'job_state' => null,
            'job_type' => $yearsLabel,
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

    /**
     * @param  array<string, mixed>  $payload
     */
    private function createEnglishTestScores(int $clientId, int $assigneeStaffId, array $payload): void
    {
        if (! Schema::hasTable('client_testscore')) {
            return;
        }

        if (($payload['ielts_passed'] ?? null) === 'yes') {
            $score = $payload['ielts_score'] ?? null;
            if ($score !== null && $score !== '') {
                ClientTestScore::create([
                    'admin_id' => $assigneeStaffId,
                    'client_id' => $clientId,
                    'test_type' => 'IELTS',
                    'overall_score' => $score,
                    'relevant_test' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        if (($payload['pte_passed'] ?? null) === 'yes') {
            $score = $payload['pte_score'] ?? null;
            if ($score !== null && $score !== '') {
                ClientTestScore::create([
                    'admin_id' => $assigneeStaffId,
                    'client_id' => $clientId,
                    'test_type' => 'PTE',
                    'overall_score' => $score,
                    'relevant_test' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
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
