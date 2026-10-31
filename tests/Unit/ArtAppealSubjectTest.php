<?php

namespace Tests\Unit;

use App\Support\ArtAppealSubject;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ArtAppealSubjectTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        ArtAppealSubject::setPersonCatalogueForTests(ArtAppealSubject::optionsFromMatters([
            ['id' => 62, 'title' => '482 - Skills in Demand', 'nick_name' => '482SD'],
            ['id' => 72, 'title' => '494 - Skilled Employer Sponsored Regional (Provisional) - Employer Sponsored', 'nick_name' => 'ES494'],
            ['id' => 19, 'title' => '186 - Employer Nomination Scheme - Direct Entry', 'nick_name' => 'EN186'],
            ['id' => 116, 'title' => '407-Training Visa', 'nick_name' => '407TV'],
            ['id' => 37, 'title' => '400 - Temporary Work (Short Stay Activity)', 'nick_name' => 'TW'],
            ['id' => 39, 'title' => '400 - Temporary Work (Short Stay Activity)', 'nick_name' => 'TT'],
            ['id' => 74, 'title' => '500 - Student', 'nick_name' => 'S'],
            ['id' => 110, 'title' => 'Bridging Visa A- (010)', 'nick_name' => 'BVA'],
            ['id' => 114, 'title' => 'Administrative Review Tribunal', 'nick_name' => 'ART'],
            ['id' => 109, 'title' => 'Skill Assessment Vetassess', 'nick_name' => 'VETASSES'],
        ]));
        ArtAppealSubject::setCompanyCatalogueForTests(ArtAppealSubject::optionsFromMatters([
            ['id' => 181, 'title' => 'Standard Business Sponsorship', 'nick_name' => 'SBS'],
            ['id' => 182, 'title' => 'Skill in Demand Nomination - Core Skills', 'nick_name' => 'Nomination'],
            ['id' => 183, 'title' => 'Employer Nomination Scheme', 'nick_name' => 'NominationENS'],
            ['id' => 186, 'title' => 'Training Nomination', 'nick_name' => 'TN 407'],
            ['id' => 190, 'title' => 'Administrative Review Tribunal', 'nick_name' => 'EmpART'],
        ]));
    }

    protected function tearDown(): void
    {
        ArtAppealSubject::setPersonCatalogueForTests(null);
        ArtAppealSubject::setCompanyCatalogueForTests(null);
        parent::tearDown();
    }

    #[Test]
    public function catalogue_nick_picks_the_person_or_company_list(): void
    {
        $this->assertSame(ArtAppealSubject::PERSON, ArtAppealSubject::kindFromNick('ART'));
        $this->assertSame(ArtAppealSubject::COMPANY, ArtAppealSubject::kindFromNick('EmpART'));
        $this->assertNull(ArtAppealSubject::kindFromNick('SBS'));
        $this->assertNull(ArtAppealSubject::kindFromNick('Partner'));
    }

    #[Test]
    public function lists_come_from_the_matter_catalogue(): void
    {
        $person = ArtAppealSubject::personOptions();
        $company = ArtAppealSubject::companyOptions();

        $this->assertSame('482 - Skills in Demand', $person['matter:62']);
        $this->assertSame('186 - Employer Nomination Scheme - Direct Entry', $person['matter:19']);
        $this->assertSame('407-Training Visa', $person['matter:116']);
        $this->assertSame('500 - Student', $person['matter:74']);
        $this->assertSame('Bridging Visa A- (010)', $person['matter:110']);
        $this->assertSame('Skill Assessment Vetassess', $person['matter:109']);
        $this->assertSame('400 - Temporary Work (Short Stay Activity) (TT)', $person['matter:39']);
        $this->assertArrayNotHasKey('employer-sponsored', $person);
        $this->assertArrayNotHasKey('other', $person);
        $this->assertArrayNotHasKey('matter:114', $person);
        $this->assertArrayNotHasKey('sbs', $person);
        $this->assertSame('Standard Business Sponsorship', $company['matter:181']);
        $this->assertSame('Skill in Demand Nomination - Core Skills', $company['matter:182']);
        $this->assertSame('Training Nomination', $company['matter:186']);
        $this->assertArrayNotHasKey('matter:190', $company);
        $this->assertArrayNotHasKey('sbs', $company);
    }

    #[Test]
    public function save_rejects_an_empty_or_cross_list_choice_only_for_art_files(): void
    {
        $this->assertNull(ArtAppealSubject::rejectionForNick('SBS', ''));
        $this->assertSame(
            'Choose the visa this ART is about.',
            ArtAppealSubject::rejectionForNick('ART', '')
        );
        $this->assertSame(
            'Choose the company matter this ART is about.',
            ArtAppealSubject::rejectionForNick('EmpART', 'student')
        );
        $this->assertSame(
            'Choose the visa this ART is about.',
            ArtAppealSubject::rejectionForNick('ART', 'employer-sponsored')
        );
        $this->assertNull(ArtAppealSubject::rejectionForNick('ART', 'matter:62'));
        $this->assertNull(ArtAppealSubject::rejectionForNick('ART', 'matter:109'));
        $this->assertSame(
            'Choose the visa this ART is about.',
            ArtAppealSubject::rejectionForNick('ART', 'other')
        );
        $this->assertNull(ArtAppealSubject::rejectionForNick('EmpART', 'matter:181'));
        $this->assertSame(
            'Choose the company matter this ART is about.',
            ArtAppealSubject::rejectionForNick('EmpART', 'ens')
        );
    }

    #[Test]
    public function sheet_filter_uses_both_lists_and_still_labels_older_stored_values(): void
    {
        $groups = ArtAppealSubject::sheetGroups();
        $options = ArtAppealSubject::sheetOptions();

        $this->assertSame(ArtAppealSubject::personOptions(), $groups['Person']);
        $this->assertSame(ArtAppealSubject::companyOptions(), $groups['Company']);
        $this->assertArrayNotHasKey('nomination', $groups['Company']);
        $this->assertSame('Nomination', $options['nomination']);
        $this->assertSame('Student 500', $options['student']);
        $this->assertSame('482, 494, 186 or 407', $options['employer-sponsored']);
        $this->assertSame('482 - Skills in Demand', $options['matter:62']);
    }

    #[Test]
    public function client_file_keeps_an_older_value_visible_and_rejects_the_other_list(): void
    {
        $options = ArtAppealSubject::detailOptions(ArtAppealSubject::COMPANY, 'nomination');

        $this->assertSame('Nomination', $options['nomination']);
        $this->assertArrayHasKey('matter:181', $options);
        $this->assertArrayNotHasKey('sbs', $options);
        $this->assertNull(ArtAppealSubject::rejectionForDetail('EmpART', 'nomination', 'nomination'));
        $this->assertNull(ArtAppealSubject::rejectionForDetail('EmpART', 'nomination', 'matter:181'));
        $this->assertSame(
            'Choose the company matter this ART is about.',
            ArtAppealSubject::rejectionForDetail('EmpART', 'nomination', 'sbs')
        );
        $this->assertSame(
            'Choose the company matter this ART is about.',
            ArtAppealSubject::rejectionForDetail('EmpART', null, 'student')
        );
        $this->assertSame(
            'This matter is not an ART file.',
            ArtAppealSubject::rejectionForDetail('SBS', null, 'sbs')
        );

        $personOptions = ArtAppealSubject::detailOptions(ArtAppealSubject::PERSON, 'employer-sponsored');
        $this->assertSame('482, 494, 186 or 407', $personOptions['employer-sponsored']);
        $this->assertSame('482 - Skills in Demand', $personOptions['matter:62']);
        $this->assertNull(ArtAppealSubject::rejectionForDetail('ART', 'employer-sponsored', 'employer-sponsored'));
        $this->assertNull(ArtAppealSubject::rejectionForDetail('ART', 'employer-sponsored', 'matter:19'));
    }
}
