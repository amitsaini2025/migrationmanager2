<?php

namespace Tests\Unit;

use App\Support\ArtAppealSubject;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ArtAppealSubjectTest extends TestCase
{
    #[Test]
    public function catalogue_nick_picks_the_person_or_company_list(): void
    {
        $this->assertSame(ArtAppealSubject::PERSON, ArtAppealSubject::kindFromNick('ART'));
        $this->assertSame(ArtAppealSubject::COMPANY, ArtAppealSubject::kindFromNick('EmpART'));
        $this->assertNull(ArtAppealSubject::kindFromNick('SBS'));
        $this->assertNull(ArtAppealSubject::kindFromNick('Partner'));
    }

    #[Test]
    public function person_list_keeps_a_persons_own_482_and_excludes_sponsorship(): void
    {
        $person = ArtAppealSubject::personOptions();
        $company = ArtAppealSubject::companyOptions();

        $this->assertSame('482, 494, 186 or 407', $person['employer-sponsored']);
        $this->assertArrayNotHasKey('sbs', $person);
        $this->assertArrayNotHasKey('nomination', $person);
        $this->assertArrayNotHasKey('employer-sponsored', $company);
        $this->assertSame('Standard Business Sponsorship', $company['sbs']);
        $this->assertSame('Nomination (Core Skills)', $company['nomination-core-skills']);
        $this->assertSame('Training Nomination', $company['training-nomination']);
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
        $this->assertNull(ArtAppealSubject::rejectionForNick('ART', 'employer-sponsored'));
        $this->assertNull(ArtAppealSubject::rejectionForNick('EmpART', 'ens'));
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
    }
}
