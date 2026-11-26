<?php

namespace Tests\Unit;

use App\Models\ClientMatter;
use App\Models\Matter;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EmployerSponsorSheetTest extends TestCase
{
    #[Test]
    public function employer_sheet_is_separate_from_the_visa_applicant_sheet(): void
    {
        $sheets = config('sheets.visa_types');

        $this->assertArrayHasKey('employer', $sheets);
        $this->assertArrayHasKey('employer-sponsored', $sheets);
        $this->assertTrue($sheets['employer']['sponsor_sheet']);
        $this->assertSame('Employers / Sponsors Sheet', $sheets['employer']['title']);
        $this->assertSame('employer_checklist_status', $sheets['employer']['checklist_status_column']);
        $this->assertSame([], $sheets['employer']['matter_title_patterns']);

        $employerNicks = array_map('strtolower', $sheets['employer']['matter_nick_names']);
        $visaNicks = array_map('strtolower', $sheets['employer-sponsored']['matter_nick_names']);
        $this->assertSame([], array_values(array_intersect($employerNicks, $visaNicks)));

        foreach ($employerNicks as $nick) {
            $this->assertArrayHasKey($nick, $sheets['employer']['sponsor_type_labels']);
        }
    }

    #[Test]
    public function sponsor_matters_resolve_to_the_employer_sheet(): void
    {
        $this->assertSame('employer', $this->sheetType('SBS', 'Standard Business Sponsorship'));
        $this->assertSame('employer', $this->sheetType('SIDCoreSkills', 'Skill in Demand Nomination - Core Skills'));
        $this->assertSame('employer', $this->sheetType('nomination', 'Skill in Demand Nomination - Core Skills'));
        $this->assertSame('employer', $this->sheetType('TN 407', 'Training Nomination'));
        $this->assertSame('employer', $this->sheetType('DAMA', 'Labour Agreement-DAMA'));
        $this->assertSame('employer', $this->sheetType('Nomination ENS', 'Employer Nomination Scheme'));
        $this->assertSame('employer', $this->sheetType('NominationENS', 'Employer Nomination Scheme'));
        $this->assertSame('employer', $this->sheetType('DE', 'Employer Nomination Scheme'));
    }

    #[Test]
    public function visa_applicant_matters_stay_on_the_employer_sponsored_sheet(): void
    {
        $this->assertSame('employer-sponsored', $this->sheetType('482SD', '482 - Skills in Demand'));
        $this->assertSame('employer-sponsored', $this->sheetType('EN186', '186 - Employer Nomination Scheme - Direct Entry'));
        $this->assertSame('employer-sponsored', $this->sheetType('407TV', '407-Training Visa'));
    }

    #[Test]
    public function employer_sponsored_tab_stages_follow_case_stage_mapping(): void
    {
        $config = config('sheets.visa_types.employer-sponsored');

        $this->assertSame(['Checklist'], $config['checklist_early_stages']);
        $this->assertFalse($config['checklist_match_doc_checklists']);
        $this->assertSame([
            'Application Lodged',
            'Immi Request Received',
            'Decision Received',
            'Ready to Close',
        ], $config['lodged_stages']);
        $this->assertSame(['File Closed'], $config['discontinue_stages']);
        $this->assertSame([
            'Cost Agreement, form 956 and First email Sent',
            'Cost Agreement, form 956 Received',
            'Pending documents and payment requested',
            'Documents Completed and Preparing for Lodgement',
            'Verification: Payment, Service Agreement, Forms',
            'Ready for Lodgement/Draft Application sent for confirmation',
            'Draft Application confirmation received',
        ], $config['ongoing_stages']);
    }

    #[Test]
    public function other_visa_sheets_keep_legacy_lodged_and_discontinue_stages(): void
    {
        $tr = config('sheets.visa_types.tr');

        $this->assertSame(['Application Lodged', 'Immi Request Received'], $tr['lodged_stages']);
        $this->assertSame(
            ['Decision Received', 'Ready to Close', 'File Closed', 'Withdrawn', 'Refund', 'Discontinued'],
            $tr['discontinue_stages']
        );
        $this->assertTrue($tr['checklist_match_doc_checklists'] ?? true);
    }

    private function sheetType(string $nickName, string $title): ?string
    {
        $matter = new Matter;
        $matter->nick_name = $nickName;
        $matter->title = $title;

        $clientMatter = new ClientMatter;
        $clientMatter->setRelation('matter', $matter);

        return $clientMatter->getVisaSheetType();
    }
}
