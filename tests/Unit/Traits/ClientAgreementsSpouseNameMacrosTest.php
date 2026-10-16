<?php

namespace Tests\Unit\Traits;

use App\Models\ClientRelationship;
use App\Traits\ClientAgreements;
use Tests\TestCase;

class ClientAgreementsSpouseNameMacrosTest extends TestCase
{
    private object $agreements;

    protected function setUp(): void
    {
        parent::setUp();

        $this->agreements = new class
        {
            use ClientAgreements;

            public function spousePartsFromRelationship(ClientRelationship $relationship): array
            {
                return $this->agreementSpouseNamePartsFromRelationship($relationship);
            }

            public function splitDisplayName(string $displayName): array
            {
                return $this->agreementSplitSingleDisplayNameIntoGivenAndSurname($displayName);
            }
        };
    }

    public function test_spouse_name_parts_from_linked_client_profile(): void
    {
        $partner = new AdminStubForSpouseMacro;
        $partner->first_name = 'Priya';
        $partner->last_name = 'Kumar';

        $relationship = new ClientRelationship;
        $relationship->related_client_id = 99;
        $relationship->setRelation('relatedClient', $partner);

        $parts = $this->agreements->spousePartsFromRelationship($relationship);

        $this->assertSame('Priya', $parts['given']);
        $this->assertSame('Kumar', $parts['surname']);
    }

    public function test_spouse_name_parts_from_relationship_name_columns(): void
    {
        $relationship = new ClientRelationship;
        $relationship->first_name = 'Alex';
        $relationship->last_name = 'Taylor';

        $parts = $this->agreements->spousePartsFromRelationship($relationship);

        $this->assertSame('Alex', $parts['given']);
        $this->assertSame('Taylor', $parts['surname']);
    }

    public function test_spouse_name_parts_split_single_details_string(): void
    {
        $relationship = new ClientRelationship;
        $relationship->details = 'Vipul Kumar';

        $parts = $this->agreements->spousePartsFromRelationship($relationship);

        $this->assertSame('Vipul', $parts['given']);
        $this->assertSame('Kumar', $parts['surname']);
    }

    public function test_split_display_name_handles_single_token(): void
    {
        $parts = $this->agreements->splitDisplayName('Madonna');

        $this->assertSame('Madonna', $parts['given']);
        $this->assertSame('', $parts['surname']);
    }
}

/** @internal */
class AdminStubForSpouseMacro
{
    public string $first_name = '';

    public string $last_name = '';
}
