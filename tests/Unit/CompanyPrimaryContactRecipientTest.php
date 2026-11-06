<?php

namespace Tests\Unit;

use App\Models\Admin;
use App\Models\Company;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CompanyPrimaryContactRecipientTest extends TestCase
{
    #[Test]
    public function person_client_does_not_expose_a_company_contact_recipient(): void
    {
        $person = new Admin;
        $person->is_company = false;
        $person->email = 'person@example.com';

        Assert::assertNull($person->composePrimaryContactRecipient());
    }

    #[Test]
    public function company_without_a_contact_email_keeps_the_existing_recipient(): void
    {
        $companyClient = $this->companyClientWithContact(null, '');

        Assert::assertNull($companyClient->composePrimaryContactRecipient());
    }

    #[Test]
    public function company_with_a_placeholder_contact_email_keeps_the_existing_recipient(): void
    {
        $companyClient = $this->companyClientWithContact(9, 'director@lead.internal');

        Assert::assertNull($companyClient->composePrimaryContactRecipient());
    }

    #[Test]
    public function company_uses_the_primary_contact_email_and_id(): void
    {
        $companyClient = $this->companyClientWithContact(42, 'garrycrm2026@gmail.com', 'John', 'Testing');

        Assert::assertSame([
            'id' => 42,
            'name' => 'John Testing',
            'email' => 'garrycrm2026@gmail.com',
            'status' => 'Client',
        ], $companyClient->composePrimaryContactRecipient());
    }

    private function companyClientWithContact(?int $contactId, string $email, string $firstName = '', string $lastName = ''): Admin
    {
        $contact = null;
        if ($contactId !== null) {
            $contact = new Admin;
            $contact->id = $contactId;
            $contact->is_company = false;
            $contact->email = $email;
            $contact->first_name = $firstName;
            $contact->last_name = $lastName;
            $contact->type = 'client';
        }

        $company = new Company;
        $company->setRelation('contactPerson', $contact);

        $companyClient = new Admin;
        $companyClient->id = 7;
        $companyClient->is_company = true;
        $companyClient->email = 'old-director@example.com';
        $companyClient->first_name = 'Ravi';
        $companyClient->last_name = 'Rai';
        $companyClient->setRelation('company', $company);

        return $companyClient;
    }
}
