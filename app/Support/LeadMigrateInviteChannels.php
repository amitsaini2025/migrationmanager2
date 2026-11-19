<?php

namespace App\Support;

use App\Models\Staff;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class LeadMigrateInviteChannels
{
    public const CHANNEL_EMAIL = 'email';

    public const CHANNEL_SMS = 'sms';

    public const SOURCE_EMAIL = 'migrate_lead_email';

    public const SOURCE_SMS = 'migrate_lead_sms';

    /**
     * @return list<string>
     */
    public static function channels(): array
    {
        return [self::CHANNEL_EMAIL, self::CHANNEL_SMS];
    }

    public static function sourceForChannel(string $channel): string
    {
        return $channel === self::CHANNEL_SMS
            ? self::SOURCE_SMS
            : self::SOURCE_EMAIL;
    }

    public static function displaySourceLabel(?string $source): ?string
    {
        return match ($source) {
            self::SOURCE_EMAIL => 'Migrate Lead (Email)',
            self::SOURCE_SMS => 'Migrate Lead (SMS)',
            default => $source,
        };
    }

    /**
     * Normalize AU mobile into national 04XXXXXXXX digits, or null if invalid.
     */
    public static function normalizeAustralianMobile(?string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone) ?? '';
        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '61') && strlen($digits) === 11) {
            $digits = '0'.substr($digits, 2);
        }

        if (preg_match('/^04\d{8}$/', $digits) !== 1) {
            return null;
        }

        return $digits;
    }

    /**
     * SMS destination matching existing CRM convention (country code + national number).
     */
    public static function smsDestinationFromNational(string $national04): string
    {
        return '+61'.$national04;
    }

    public static function smsText(string $url): string
    {
        return 'Hi, Bansal Immigration Consultants invites you to complete your migration enquiry form. '
            .'Please use this personalised link (do not forward): '.$url;
    }

    /**
     * Same Qualification Level options as lead/client Educational Qualifications.
     *
     * @return list<string>
     */
    public static function qualificationLevels(): array
    {
        return [
            'Certificate I',
            'Certificate II',
            'Certificate III',
            'Certificate IV',
            'Diploma',
            'Advanced Diploma',
            'Associate Degree',
            'Bachelor Degree',
            'Bachelor Honours Degree',
            'Graduate Certificate',
            'Graduate Diploma',
            'Masters Degree',
            'Doctoral Degree',
            '11',
        ];
    }

    public static function qualificationLevelLabel(string $level): string
    {
        return $level === '11' ? '+2' : $level;
    }

    /**
     * @return list<string>
     */
    public static function currentVisaOptions(): array
    {
        return [
            'Student (500)',
            'Temporary Graduate (485)',
            'Visitor (600 / other)',
            'Employer Sponsored / Work Visa',
            'Bridging Visa',
            'Working Holiday (417 / 462)',
            'Partner Visa',
            'Permanent Resident',
            'Australian Citizen',
            'Other / Unsure',
            'No Australian visa',
        ];
    }

    /**
     * Hint nicknames/keywords used to soft-match CRM Matter rows for visa_type.
     *
     * @return list<string>
     */
    public static function matterMatchHintsForCurrentVisa(?string $currentVisa): array
    {
        return match (trim((string) $currentVisa)) {
            'Student (500)' => ['500', 'Student'],
            'Temporary Graduate (485)' => ['485', 'Temporary Graduate'],
            'Visitor (600 / other)' => ['600', 'Visitor'],
            'Employer Sponsored / Work Visa' => ['482', '186', '494', 'Employer'],
            'Bridging Visa' => ['Bridging'],
            'Working Holiday (417 / 462)' => ['417', '462', 'Working Holiday'],
            'Partner Visa' => ['Partner'],
            'Permanent Resident' => ['PR', 'Permanent Resident'],
            'Australian Citizen' => ['Citizen'],
            default => [],
        };
    }

    public static function visitPurposePrefix(): string
    {
        $purpose = trim((string) config('lead_migrate_invite.visit_purpose', 'Migration Client Enquiry'));

        return $purpose !== '' ? $purpose : 'Migration Client Enquiry';
    }

    public static function isMigrateEnquiryVisit(?string $visitPurpose): bool
    {
        $purpose = trim((string) $visitPurpose);
        if ($purpose === '') {
            return false;
        }

        $prefix = self::visitPurposePrefix();

        return strcasecmp($purpose, $prefix) === 0
            || str_starts_with(mb_strtolower($purpose), mb_strtolower($prefix));
    }

    /**
     * @return list<string>
     */
    public static function migrateAssigneeStaffKeys(): array
    {
        $keys = config('lead_migrate_invite.assignee_staff_keys', []);
        if (! is_array($keys)) {
            return [];
        }

        $out = [];
        foreach ($keys as $key) {
            $key = strtolower(trim((string) $key));
            if ($key !== '' && ! in_array($key, $out, true)) {
                $out[] = $key;
            }
        }

        return $out;
    }

    /**
     * @return list<string>
     */
    public static function aliasesForMigrateAssigneeKey(string $key): array
    {
        $key = strtolower(trim($key));
        $aliases = config('lead_migrate_invite.assignee_staff_aliases.'.$key, [$key]);
        if (! is_array($aliases)) {
            $aliases = [$key];
        }

        $out = [];
        foreach ($aliases as $alias) {
            $alias = strtolower(trim((string) $alias));
            if ($alias !== '' && ! in_array($alias, $out, true)) {
                $out[] = $alias;
            }
        }

        if ($out === []) {
            $out[] = $key;
        }

        return $out;
    }

    /**
     * Active staff matching the migrate-enquiry Set Assignee allowlist.
     *
     * @return Collection<int, Staff>
     */
    public static function resolveMigrateAssigneeStaff(): Collection
    {
        if (! Schema::hasTable('staff')) {
            return collect();
        }

        $keys = self::migrateAssigneeStaffKeys();
        if ($keys === []) {
            return collect();
        }

        $aliasSet = [];
        foreach ($keys as $key) {
            foreach (self::aliasesForMigrateAssigneeKey($key) as $alias) {
                $aliasSet[$alias] = true;
            }
        }
        $aliases = array_keys($aliasSet);

        return Staff::query()
            ->active()
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get(['id', 'first_name', 'last_name', 'email', 'status'])
            ->filter(function (Staff $staff) use ($aliases): bool {
                return self::staffMatchesMigrateAssigneeAliases($staff, $aliases);
            })
            ->values();
    }

    /**
     * @param  list<string>  $aliases
     */
    public static function staffMatchesMigrateAssigneeAliases(Staff $staff, array $aliases): bool
    {
        $firstName = strtolower(trim((string) $staff->first_name));
        $emailLocal = strtolower(trim((string) explode('@', (string) $staff->email, 2)[0]));

        foreach ($aliases as $alias) {
            if ($firstName === $alias) {
                return true;
            }

            if ($firstName !== '' && str_starts_with($firstName, $alias.' ')) {
                return true;
            }

            if ($emailLocal === $alias || str_starts_with($emailLocal, $alias.'.')) {
                return true;
            }
        }

        return false;
    }

    public static function isAllowedMigrateAssigneeStaffId(int $staffId): bool
    {
        if ($staffId <= 0) {
            return false;
        }

        return self::resolveMigrateAssigneeStaff()->contains(fn (Staff $staff): bool => (int) $staff->id === $staffId);
    }
}
