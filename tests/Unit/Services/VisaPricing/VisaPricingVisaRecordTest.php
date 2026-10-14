<?php

namespace Tests\Unit\Services\VisaPricing;

use App\Services\VisaPricing\VisaPricingVisaRecord;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class VisaPricingVisaRecordTest extends TestCase
{
    #[Test]
    public function it_normalizes_legacy_config_rows(): void
    {
        $row = VisaPricingVisaRecord::normalize([
            'id' => '71',
            'label' => 'Partner (Provisional and Migrant) visa (subclass 309/100)',
            'base_charge' => 9365,
            'additional_18_plus' => 4685,
            'additional_u18' => 2345,
            'subsequent_temp_app_charge' => null,
        ]);

        $this->assertSame('309/100', $row['subclass']);
        $this->assertSame('$9,365', $row['main_applicant_fee']);
        $this->assertSame('$4,685', $row['additional_applicant_18_plus_fee']);
        $this->assertSame(9365, VisaPricingVisaRecord::mainApplicantFee($row));
        $this->assertSame(4685, VisaPricingVisaRecord::additionalApplicant18PlusFee($row));
    }

    #[Test]
    public function it_builds_display_label_for_sheet_rows(): void
    {
        $label = VisaPricingVisaRecord::displayLabel([
            'id' => '1',
            'category' => 'Visitor / Temporary',
            'subclass' => '600',
            'visa_stream' => 'Visitor – Tourist (offshore)',
            'main_applicant_fee' => '$250',
            'additional_applicant_18_plus_fee' => null,
            'applicant_under_18_fee' => null,
            'second_vac_charges' => null,
            'notes' => null,
            'source' => null,
        ]);

        $this->assertSame('Visitor – Tourist (offshore) (subclass 600)', $label);
    }

    #[Test]
    public function it_builds_visa_list_response_row_matching_config_columns(): void
    {
        $row = VisaPricingVisaRecord::toListResponseRow([
            'id' => '57',
            'category' => 'Parent',
            'subclass' => '143',
            'visa_stream' => 'Contributory Parent',
            'main_applicant_fee' => '$6,300',
            'additional_applicant_18_plus_fee' => '$2,125',
            'applicant_under_18_fee' => '$1,065',
            'second_vac_charges' => '$43,600 per person',
            'notes' => 'Large second VAC',
            'source' => 'https://example.com',
        ]);

        $this->assertSame(
            [
                'id',
                'category',
                'subclass',
                'visa_stream',
                'main_applicant_fee',
                'additional_applicant_18_plus_fee',
                'applicant_under_18_fee',
                'second_vac_charges',
                'notes',
                'source',
            ],
            array_keys($row),
        );
        $this->assertSame('$43,600 per person', $row['second_vac_charges']);
    }
}
