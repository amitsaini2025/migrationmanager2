<?php

namespace Tests\Unit;

use App\Support\EmployerSpreadsheetImport\BusinessName;
use App\Support\EmployerSpreadsheetImport\CompanyContactBackfillPlanner;
use App\Support\EmployerSpreadsheetImport\CompanyContactRow;
use App\Support\EmployerSpreadsheetImport\CompanyContactWorkbook;
use App\Support\EmployerSpreadsheetImport\CompanyNameMatcher;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CompanyContactBackfillTest extends TestCase
{
    #[Test]
    public function test_business_names_ignore_punctuation_and_the_trenthem_spelling(): void
    {
        $this->assertSame(
            BusinessName::groupKey('Adelaide Taxi Trucks Pty. Ltd.'),
            BusinessName::groupKey('Adelaide Taxi Trucks Pty.Ltd.'),
        );
        $this->assertSame(
            BusinessName::groupKey('The Cosmopolitan Hotel (Trenthem) Pty Ltd'),
            BusinessName::groupKey('The Cosmopolitan Hotel (Trentham) Pty Ltd'),
        );
    }

    #[Test]
    public function test_company_matcher_accepts_a_digit_suffix_and_a_single_typo(): void
    {
        $companies = [
            ['id' => 1, 'name' => "MELBOURNE'S FINEST FLOORS PTY LTD6402"],
            ['id' => 2, 'name' => 'Parmar & Sons Co. Pty Ltd'],
            ['id' => 3, 'name' => 'HRJ Painting & Decorating Service Pty Ltd'],
            ['id' => 4, 'name' => 'DWHO PTY LTD'],
        ];

        $this->assertSame(1, CompanyNameMatcher::match("Melbourne's Finest Floors Pty Ltd", $companies)['id']);
        $this->assertSame(2, CompanyNameMatcher::match('Parrmar & Sons Co. Pty Ltd', $companies)['id']);
        $this->assertNull(CompanyNameMatcher::match('Admire Painting and Decorating Service Pty Ltd.', $companies)['id']);
        $this->assertNull(CompanyNameMatcher::match('DHMO Pty Ltd', $companies)['id']);
    }

    #[Test]
    public function test_matter_match_fills_only_blank_fields(): void
    {
        $plan = CompanyContactBackfillPlanner::plan(
            [$this->row('ACE.26.0001', 'Ace Brush & Repairs Pty Ltd', '51683745333', '683745333')],
            $this->catalog([
                'ACE.26.0001' => 5,
            ], [
                $this->company(5, 'Ace Brush & Repairs Pty Ltd', [
                    'trading_name' => 'Ace Brush',
                    'abn' => '11111111111',
                ]),
            ]),
        );

        $this->assertSame(1, $plan['matched_by_matter']);
        $this->assertSame(0, $plan['not_found']);
        $this->assertSame(['ACN' => '683745333'], $plan['actions'][0]['changes']);
    }

    #[Test]
    public function test_name_disagreement_does_not_update_either_company(): void
    {
        $plan = CompanyContactBackfillPlanner::plan(
            [$this->row('OLD.26.0001', 'New Co Pty Ltd')],
            $this->catalog([
                'OLD.26.0001' => 1,
            ], [
                $this->company(1, 'Old Co Pty Ltd'),
                $this->company(2, 'New Co Pty Ltd'),
            ]),
        );

        $this->assertSame([], $plan['actions']);
        $this->assertSame(1, $plan['conflicts']);
    }

    #[Test]
    public function test_unique_name_matches_when_the_matter_number_is_absent(): void
    {
        $plan = CompanyContactBackfillPlanner::plan(
            [$this->row('MISSING.26.0009', 'Cafe Mambo Bar & Lounge Pty Ltd', null, null, '8414593541')],
            $this->catalog([], [
                $this->company(3, 'Cafe Mambo Bar & Lounge Pty Ltd'),
            ]),
        );

        $this->assertSame(1, $plan['matched_by_name']);
        $this->assertSame([], $plan['actions']);
        $this->assertSame(['Cafe Mambo Bar & Lounge Pty Ltd: ABN was not 11 digits, so it was left blank.'], $plan['warnings']);
        $this->assertSame(1, $plan['companies_unchanged']);
    }

    #[Test]
    public function test_supplier_contact_is_not_added_as_a_director(): void
    {
        $row = $this->row('GILL.26.0002', 'Gills Developers Pty Ltd');
        $row = new CompanyContactRow(
            matterId: $row->matterId,
            business: $row->business,
            trading: null,
            matterPhone: '0405400329',
            contactBusinessName: null,
            contactTrading: null,
            abn: null,
            acn: null,
            structure: 'Proprietary company',
            industry: 'Construction',
            startDate: null,
            businessPhone: null,
            businessMobile: null,
            businessEmail: 'office@example.com',
            street1: '1 Main Street',
            street2: null,
            suburb: 'Adelaide',
            state: 'SA',
            postcode: '5000',
            country: 'Australia',
            contacts: [[
                'surname' => 'Khan',
                'given' => 'Yasir',
                'position' => null,
                'type' => 'Supplier',
                'dob' => null,
                'phone' => '0405400329',
                'email' => 'yasir@example.com',
            ]],
        );

        $plan = CompanyContactBackfillPlanner::plan(
            [$row],
            $this->catalog(['GILL.26.0002' => 8], [
                $this->company(8, 'Gills Developers Pty Ltd'),
            ], [
                'people_by_email' => ['yasir@example.com' => 77],
                'email_owners' => ['office@example.com' => 999],
            ]),
        );

        $changes = $plan['actions'][0]['changes'];
        $this->assertSame('Proprietary Company', $changes['company_type']);
        $this->assertSame('Construction', $changes['main_business_activity']);
        $this->assertSame('1 Main Street', $changes['address']['address_line_1']);
        $this->assertArrayNotHasKey('email', $changes);
        $this->assertArrayNotHasKey('directors', $changes);
        $this->assertArrayNotHasKey('contact_person_id', $changes);
    }

    #[Test]
    public function test_only_the_first_contact_is_kept(): void
    {
        $row = new CompanyContactRow(
            matterId: 'ABES.26.0001',
            business: 'ABES. ADL. PTY LTD',
            trading: null,
            matterPhone: null,
            contactBusinessName: 'ABES. ADL. PTY LTD',
            contactTrading: null,
            abn: null,
            acn: null,
            structure: null,
            industry: null,
            startDate: null,
            businessPhone: null,
            businessMobile: null,
            businessEmail: null,
            street1: null,
            street2: null,
            suburb: null,
            state: null,
            postcode: null,
            country: null,
            contacts: [
                [
                    'surname' => 'Aggarwal',
                    'given' => 'Chahat',
                    'position' => 'Director',
                    'type' => 'Business Sponsor',
                    'dob' => null,
                    'phone' => '0435208129',
                    'email' => 'hari@example.com',
                ],
                [
                    'surname' => 'Alfares',
                    'given' => 'Ahmed',
                    'position' => 'Owner',
                    'type' => 'Business Sponsor',
                    'dob' => null,
                    'phone' => '0412092125',
                    'email' => 'ahmed@example.com',
                ],
            ],
        );

        $plan = CompanyContactBackfillPlanner::plan(
            [$row],
            $this->catalog(['ABES.26.0001' => 4], [
                $this->company(4, 'ABES. ADL. PTY LTD'),
            ], [
                'people_by_email' => [
                    'hari@example.com' => 21,
                    'ahmed@example.com' => 22,
                ],
            ]),
        );

        $changes = $plan['actions'][0]['changes'];
        $this->assertSame('Chahat Aggarwal', $changes['directors'][0]['name']);
        $this->assertCount(1, $changes['directors']);
        $this->assertSame(21, $changes['contact_person_id']);
        $this->assertSame('Director', $changes['contact_person_position']);
    }

    #[Test]
    public function test_workbook_reads_the_sponsor_contact_sheet(): void
    {
        $rows = (new CompanyContactWorkbook)->read(public_path('sponsorship_with_contacts.xlsx'));

        $this->assertCount(243, $rows);
        $this->assertSame('ABDULW.26.0042', $rows[0]->matterId);
        $this->assertSame('Aggarwal', $rows[1]->contacts[0]['surname']);
        $this->assertSame('51683745333', $rows[2]->abn);
    }

    /**
     * @param  array<string, int>  $matters
     * @param  list<array<string, mixed>>  $companies
     * @param  array<string, mixed>  $extra
     * @return array<string, mixed>
     */
    private function catalog(array $matters, array $companies, array $extra = []): array
    {
        $indexed = [];
        foreach ($companies as $company) {
            $indexed[$company['id']] = $company;
        }

        return array_merge([
            'matters' => $matters,
            'companies' => $indexed,
            'email_owners' => [],
            'phone_owners' => [],
            'people_by_email' => [],
            'people_by_phone' => [],
        ], $extra);
    }

    /**
     * @param  array<string, mixed>  $extra
     * @return array<string, mixed>
     */
    private function company(int $id, string $name, array $extra = []): array
    {
        return array_merge([
            'id' => $id,
            'admin_id' => $id + 100,
            'legal_name' => $name,
            'names' => [$name],
            'trading_name' => null,
            'abn' => null,
            'acn' => null,
            'company_type' => null,
            'industry' => null,
            'operating_since' => null,
            'phone' => null,
            'email' => null,
            'has_address' => false,
            'directors' => [],
            'contact_person_id' => null,
            'contact_person_position' => null,
        ], $extra);
    }

    private function row(
        string $matterId,
        string $business,
        ?string $abn = null,
        ?string $acn = null,
        ?string $rawAbn = null,
    ): CompanyContactRow {
        return new CompanyContactRow(
            matterId: $matterId,
            business: $business,
            trading: null,
            matterPhone: null,
            contactBusinessName: $business,
            contactTrading: null,
            abn: $rawAbn ?? $abn,
            acn: $acn,
            structure: null,
            industry: null,
            startDate: null,
            businessPhone: null,
            businessMobile: null,
            businessEmail: null,
            street1: null,
            street2: null,
            suburb: null,
            state: null,
            postcode: null,
            country: null,
            contacts: [],
        );
    }
}
