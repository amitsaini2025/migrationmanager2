<?php

namespace Tests\Unit\Services\VisaPricing;

use App\Services\VisaPricing\VisaPricingSheetParser;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class VisaPricingSheetParserTest extends TestCase
{
    #[Test]
    public function it_parses_sheet_rows_with_sequential_ids(): void
    {
        $parser = new VisaPricingSheetParser;

        $visas = $parser->parse([
            [
                'Visa Subclass',
                'Base Application Charge',
                'Additional Applicant Charge 18 And Over',
                'Additional Applicant Charge Under 18',
                'page',
            ],
            [
                'Partner (Provisional and Migrant) visa (subclass 309/100)',
                '9365',
                '4685',
                '2345',
                '2',
            ],
            [
                'Visitor visa (subclass 600) Tourist stream - In Australia',
                '500',
                'N/A',
                'N/A',
                '3',
            ],
        ]);

        $this->assertCount(2, $visas);
        $this->assertSame('1', $visas[0]['id']);
        $this->assertSame(9365, $visas[0]['base_charge']);
        $this->assertSame(4685, $visas[0]['additional_18_plus']);
        $this->assertSame('2', $visas[1]['id']);
        $this->assertNull($visas[1]['additional_18_plus']);
        $this->assertNull($visas[1]['additional_u18']);
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
            ],
            [
                'Visitor / Temporary',
                '600',
                'Visitor – Tourist (offshore)',
                '$250',
                '',
                '',
                '',
            ],
            [
                'Visitor / Temporary',
                '601',
                'Electronic Travel Authority (ETA)',
                'Nil',
                '',
                '',
                '',
            ],
        ]);

        $this->assertCount(1, $visas);
        $this->assertSame('1', $visas[0]['id']);
        $this->assertSame('Visitor – Tourist (offshore) (subclass 600)', $visas[0]['label']);
        $this->assertSame(250, $visas[0]['base_charge']);
        $this->assertNull($visas[0]['additional_18_plus']);
    }
}
