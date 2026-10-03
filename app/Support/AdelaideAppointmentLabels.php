<?php

namespace App\Support;

use App\Models\BookingAppointment;

/**
 * Friendly service/enquiry labels for Adelaide bookings, matching Bansal website admin.
 * Used when syncing website appointments and when displaying Adelaide rows in CRM.
 */
class AdelaideAppointmentLabels
{
    /**
     * @var array<int, string>
     */
    private const NOE_ENQUIRY_LABELS = [
        1 => 'GSM Visas (491, 190, 189, 191)',
        2 => 'TR: 485 visa',
        3 => 'JRP/Skill Assessment',
        4 => 'Tourist Visa',
        5 => 'Education/Student Visa',
        6 => 'Complex Matters (ART, Protection visa, Federal Case)',
        7 => 'Visa Cancellation/NOICC/Refusals',
        8 => 'Anyone who is outside Australia',
        9 => 'EOI/ROI',
        10 => 'Employer Sponsored Visas: 494, 482, 186, DAMA',
        11 => 'Family Visas (Parent Visa, Partner Visa, Child Visa)',
        12 => 'Citizenship',
    ];

    /**
     * @var array<int, string>
     */
    private const NOE_SERVICE_LABELS = [
        1 => 'GSM Visas: 491, 190, 189, 191',
        2 => 'TR: 485 visa',
        3 => 'JRP/Skill Assessment',
        4 => 'Tourist Visa',
        5 => 'Education/Course Change/Student Visa/Student Dependent Visa (for education selection for Australian onshore clients only)',
        6 => 'Complex matters: ART, Protection visa, Federal Case',
        7 => 'Visa Cancellation/ NOICC/ Visa refusals',
        8 => 'Anyone who is outside Australia',
        9 => 'EOI/ROI',
        10 => 'Employer Sponsored Visas: 494, 482, 186, DAMA',
        11 => 'Family Visas (Parent Visa, Partner Visa, Child Visa)',
        12 => 'Citizenship',
    ];

    /**
     * Internal Bansal API slugs that should not be shown for Adelaide bookings.
     *
     * @var list<string>
     */
    private const INTERNAL_ENQUIRY_SLUGS = [
        'kunal',
        'ajay',
        'arun',
        'pr_complex',
        'pr',
        'jrp',
        'complex',
        'eoi',
        'international',
        'employer_sponsored',
        'family_visas',
        'citizenship',
        'cancellation',
        'tourist',
        'education',
        'tr',
    ];

    /**
     * @var list<string>
     */
    private const INTERNAL_SERVICE_SLUGS = [
        'permanent-residency',
        'gsm-visas',
        'temporary-residency',
        'jrp-skill-assessment',
        'tourist-visa',
        'education-visa',
        'complex-matters',
        'visa-cancellation',
        'international-migration',
        'eoi-roi',
        'employer-sponsored',
        'family-visas',
        'citizenship',
        'ajay',
        'arun',
    ];

    public static function isAdelaide(?string $location, mixed $inpersonAddress = null): bool
    {
        if ($inpersonAddress === 1 || $inpersonAddress === '1') {
            return true;
        }

        return strtolower(trim((string) $location)) === 'adelaide';
    }

    /**
     * @return array{enquiry_type: string, service_type: string}|null
     */
    public static function labelsForNoe(?int $noeId): ?array
    {
        if ($noeId === null || ! isset(self::NOE_ENQUIRY_LABELS[$noeId])) {
            return null;
        }

        return [
            'enquiry_type' => self::NOE_ENQUIRY_LABELS[$noeId],
            'service_type' => self::NOE_SERVICE_LABELS[$noeId],
        ];
    }

    /**
     * Normalize Adelaide website-sync fields to Bansal admin labels.
     *
     * @return array{service_type: ?string, enquiry_type: ?string}
     */
    public static function normalizeSyncedFields(
        ?string $location,
        ?int $noeId,
        ?string $serviceType,
        ?string $enquiryType,
        mixed $inpersonAddress = null
    ): array {
        if (! self::isAdelaide($location, $inpersonAddress)) {
            return [
                'service_type' => $serviceType,
                'enquiry_type' => $enquiryType,
            ];
        }

        $labels = self::labelsForNoe($noeId);
        if ($labels === null) {
            return [
                'service_type' => $serviceType,
                'enquiry_type' => $enquiryType,
            ];
        }

        return [
            'service_type' => self::shouldReplaceServiceType($serviceType)
                ? $labels['service_type']
                : ($serviceType ?? $labels['service_type']),
            'enquiry_type' => self::shouldReplaceEnquiryType($enquiryType)
                ? $labels['enquiry_type']
                : ($enquiryType ?? $labels['enquiry_type']),
        ];
    }

    public static function displayServiceType(BookingAppointment $appointment): ?string
    {
        if (! self::isAdelaide($appointment->location, $appointment->inperson_address)) {
            return $appointment->service_type;
        }

        $labels = self::labelsForNoe($appointment->noe_id !== null ? (int) $appointment->noe_id : null);
        if ($labels === null) {
            return $appointment->service_type;
        }

        if (self::shouldReplaceServiceType($appointment->service_type)) {
            return $labels['service_type'];
        }

        return $appointment->service_type;
    }

    public static function displayEnquiryType(BookingAppointment $appointment): ?string
    {
        if (! self::isAdelaide($appointment->location, $appointment->inperson_address)) {
            return $appointment->enquiry_type;
        }

        $labels = self::labelsForNoe($appointment->noe_id !== null ? (int) $appointment->noe_id : null);
        if ($labels === null) {
            return $appointment->enquiry_type;
        }

        if (self::shouldReplaceEnquiryType($appointment->enquiry_type)) {
            return $labels['enquiry_type'];
        }

        return $appointment->enquiry_type;
    }

    private static function shouldReplaceEnquiryType(?string $value): bool
    {
        if ($value === null || trim($value) === '') {
            return true;
        }

        return in_array(strtolower(trim($value)), self::INTERNAL_ENQUIRY_SLUGS, true);
    }

    private static function shouldReplaceServiceType(?string $value): bool
    {
        if ($value === null || trim($value) === '') {
            return true;
        }

        $normalized = strtolower(trim($value));

        if (in_array($normalized, self::INTERNAL_SERVICE_SLUGS, true)) {
            return true;
        }

        return str_contains($normalized, '-') && ! str_contains($normalized, ':');
    }
}
