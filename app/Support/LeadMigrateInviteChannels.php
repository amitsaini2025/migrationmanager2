<?php

namespace App\Support;

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
}
