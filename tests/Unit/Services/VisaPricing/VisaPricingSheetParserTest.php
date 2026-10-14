<?php

namespace Tests\Unit\Services\VisaPricing;

use App\Services\VisaPricing\VisaPricingSheetParser;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class VisaPricingSheetParserTest extends TestCase
{
    #[Test]
    public function it_parses_legacy_home_affairs_sheet_headers(): void
    {
        $parser = new VisaPricingSheetParser;

        $visas = $parser->parse([
            [
                'Visa Subclass',
                'Base Application Charge',
                'Additional Applicant Charge 18 And Over',
                'Additional Applicant Charge Under 18',
            ],
            [
                'Partner (Provisional and Migrant) visa (subclass 309/100)',
                '9365',
                '4685',
                '2345',
            ],
            [
                'Visitor visa (subclass 600) Tourist stream - In Australia',
                '500',
                'N/A',
                'N/A',
            ],
        ]);

        $this->assertCount(2, $visas);
        $this->assertSame('1', $visas[0]['id']);
        $this->assertSame('$9,365', $visas[0]['main_applicant_fee']);
        $this->assertSame('$4,685', $visas[0]['additional_applicant_18_plus_fee']);
        $this->assertSame('309/100', $visas[0]['subclass']);
        $this->assertNull($visas[1]['additional_applicant_18_plus_fee']);
        $this->assertNull($visas[1]['applicant_under_18_fee']);
    }

    #[Test]
    public function it_rejects_sheets_without_required_columns(): void
    {
        $parser = new VisaPricingSheetParser;

        $this->expectException(\InvalidArgumentException::class);

        $parser->parse([
            ['Wrong Header'],
            ['Some visa'],
        ]);
    }

    #[Test]
    public function it_parses_google_sheet_layout_with_title_rows_before_headers(): void
    {
        $parser = new VisaPricingSheetParser;

        $visas = $parser->parse([
            ['Australian Visa Application Charges'],
            ['Prepared as a working reference.'],
            [],
            [
                'Category',
                'Subclass',
                'Visa / Stream',
                'Main Applicant',
                'Additional Applicant 18+',
                'Applicant Under 18',
                'Second VAC / Extra Charge',
                'Notes',
                'Source',
            ],
            [
                'Visitor / Temporary',
                '600',
                'Visitor – Tourist (offshore)',
                '$250',
                '',
                '',
                '',
                'Note text',
                'https://example.com',
            ],
            [
                'Visitor / Temporary',
                '601',
                'Electronic Travel Authority (ETA)',
                'Nil',
                '',
                '',
                '',
                '',
                '',
            ],
            [
                'Parent',
                '143',
                'Contributory Parent',
                '$48,365',
                '$43,600',
                '$21,800',
                '$43,600 per person',
                'Second instalment',
                'https://example.com',
            ],
        ]);

        $this->assertCount(3, $visas);
        $this->assertSame('1', $visas[0]['id']);
        $this->assertSame('Visitor / Temporary', $visas[0]['category']);
        $this->assertSame('600', $visas[0]['subclass']);
        $this->assertSame('Visitor – Tourist (offshore)', $visas[0]['visa_stream']);
        $this->assertSame('$250', $visas[0]['main_applicant_fee']);
        $this->assertNull($visas[0]['additional_applicant_18_plus_fee']);
        $this->assertSame('Note text', $visas[0]['notes']);
        $this->assertSame('2', $visas[1]['id']);
        $this->assertSame('Electronic Travel Authority (ETA)', $visas[1]['visa_stream']);
        $this->assertSame('$0', $visas[1]['main_applicant_fee']);
        $this->assertSame('$48,365', $visas[2]['main_applicant_fee']);
        $this->assertSame('$43,600', $visas[2]['additional_applicant_18_plus_fee']);
        $this->assertSame('$21,800', $visas[2]['applicant_under_18_fee']);
        $this->assertSame('$43,600 per person', $visas[2]['second_vac_charges']);
    }
}
