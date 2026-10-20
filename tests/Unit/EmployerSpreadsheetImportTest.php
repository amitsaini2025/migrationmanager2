<?php

namespace Tests\Unit;

use App\Support\EmployerSpreadsheetImport\BusinessName;
use App\Support\EmployerSpreadsheetImport\CompanyNameMatcher;
use App\Support\EmployerSpreadsheetImport\ImportPlanner;
use App\Support\EmployerSpreadsheetImport\MatterTypeCatalog;
use App\Support\EmployerSpreadsheetImport\PersonName;
use App\Support\EmployerSpreadsheetImport\SheetRow;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EmployerSpreadsheetImportTest extends TestCase
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
    public function test_matter_labels_map_onto_crm_titles(): void
    {
        $skills = MatterTypeCatalog::match('482- Skills in Demand', 'Migrant', 'direct-entry');
        $this->assertSame('482 - Skills in Demand', $skills['title']);
        $this->assertFalse($skills['for_company']);

        $subclass186 = MatterTypeCatalog::match('186 - Employer Nomination Scheme', 'Migrant', 'trt');
        $this->assertSame('186 - Employer Nomination Scheme - Temporary Residence Transition', $subclass186['title']);

        $this->assertNull(MatterTypeCatalog::match('Temporary Skill Shortage Nomination', 'Sponsor', 'direct-entry'));
        $this->assertSame('Standard Business Sponsorship', MatterTypeCatalog::match('Standard Business Sponsorship', 'Sponsor', 'direct-entry')['title']);
        $this->assertSame(0, MatterTypeCatalog::isActiveFileStatus('File Closed') ? 1 : 0);
    }

    #[Test]
    public function test_full_name_in_the_surname_column_is_split(): void
    {
        $this->assertSame(['Avtar', 'Singh'], PersonName::split('Avtar Singh', null));
        $this->assertSame(['Kirandeep', 'Kaur'], PersonName::split('Kaur', 'Kirandeep'));
    }

    #[Test]
    public function test_plan_reuses_an_existing_person_and_skips_an_imported_matter(): void
    {
        $plan = ImportPlanner::plan(
            rows: [
                $this->row('sponsorship', 'NEWCO.25.0001', 'New Co Pty Ltd', 'Sponsor', 'Standard Business Sponsorship', 'ARUN BANSAL'),
                $this->row('sponsorship', 'OLDCO.25.0002', 'Old Co Pty Ltd', 'Sponsor', 'Standard Business Sponsorship', 'ARUN BANSAL'),
                $this->row('482', 'PERSON.25.0003', 'New Co Pty Ltd', 'Migrant', '482 - Skills in Demand', 'ARUN BANSAL', 'Singh', 'Avtar', ['avtar@example.com']),
                $this->row('482', 'DONE.25.0004', 'New Co Pty Ltd', 'Migrant', '482 - Skills in Demand', 'Vipul Goyal', 'Kaur', 'Kirandeep', ['kirandeep@example.com']),
                $this->row('sponsorship', 'TRENT.25.0005', 'The Cosmopolitan Hotel (Trenthem) Pty Ltd', 'Sponsor', 'Training Nomination', 'ARUN BANSAL'),
                $this->row('sponsorship', 'TRENT.25.0006', 'The Cosmopolitan Hotel (Trentham) Pty Ltd', 'Sponsor', 'Employer Nomination', 'ARUN BANSAL'),
            ],
            companies: [
                ['id' => 9, 'name' => 'Old Co Pty Ltd'],
            ],
            clientsByEmail: [
                'avtar@example.com' => ['id' => 44, 'is_company' => false],
            ],
            clientsByIdentity: [],
            departmentReferences: ['DONE.25.0004' => true],
            openMatterIds: [],
            matterTypeIds: [
                'Standard Business Sponsorship|1' => 181,
                '482 - Skills in Demand|0' => 62,
                'Training Nomination|1' => 186,
                'Employer Nomination Scheme|1' => 183,
            ],
            staffIds: ['arun bansal' => 5, 'vipul goyal' => 6],
            fallbackStaffId: null,
            subclass186Stream: 'direct-entry',
        );

        $this->assertSame(2, $plan['companies_to_create']);
        $this->assertSame(1, $plan['companies_rejected']);
        $this->assertSame(1, $plan['clients_to_create']);
        $this->assertSame(1, $plan['clients_rejected']);
        $this->assertSame(2, $plan['matters_rejected']);
        $this->assertSame([], $plan['blocking']);
        $this->assertCount(1, array_filter(
            $plan['actions'],
            fn (array $action): bool => $action['type'] === 'create_company' && str_contains($action['name'], 'Cosmopolitan'),
        ));
    }

    /**
     * @param  list<string>  $emails
     */
    private function row(
        string $source,
        string $matterId,
        string $business,
        string $role,
        string $matterType,
        string $responsible,
        ?string $surname = null,
        ?string $given = null,
        array $emails = [],
    ): SheetRow {
        return new SheetRow(
            source: $source,
            matterId: $matterId,
            business: $business,
            trading: null,
            role: $role,
            surname: $surname,
            given: $given,
            emails: $emails,
            phone: null,
            matterType: $matterType,
            fileStatus: 'Lodged',
            dob: null,
            position: null,
            anzsco: null,
            responsible: $responsible,
            engagementOn: '2025-03-01',
            passportCountry: null,
            passportExpiry: null,
            maritalStatus: null,
        );
    }
}
