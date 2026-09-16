<?php

namespace Tests\Unit\Models;

use App\Models\ClientRelationship;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ClientRelationshipPartnerTypesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('client_relationships');
        Schema::create('client_relationships', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('related_client_id')->nullable();
            $table->string('details')->nullable();
            $table->string('relationship_type')->nullable();
            $table->string('company_type')->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('client_relationships');
        parent::tearDown();
    }

    public function test_partner_relationship_types_include_engaged_and_ex_husband(): void
    {
        $this->assertSame(
            ['Husband', 'Wife', 'Ex-Husband', 'Ex-Wife', 'Defacto', 'Engaged'],
            ClientRelationship::PARTNER_RELATIONSHIP_TYPES
        );
    }

    public function test_partners_scope_deletes_engaged_without_removing_children(): void
    {
        ClientRelationship::query()->create([
            'admin_id' => 1,
            'client_id' => 100,
            'related_client_id' => 200,
            'details' => 'Engaged partner',
            'relationship_type' => 'Engaged',
        ]);
        ClientRelationship::query()->create([
            'admin_id' => 1,
            'client_id' => 100,
            'related_client_id' => 201,
            'details' => 'Husband partner',
            'relationship_type' => 'Husband',
        ]);
        ClientRelationship::query()->create([
            'admin_id' => 1,
            'client_id' => 100,
            'related_client_id' => 202,
            'details' => 'Child',
            'relationship_type' => 'Son',
        ]);

        ClientRelationship::where('client_id', 100)->partners()->delete();

        $remaining = ClientRelationship::where('client_id', 100)->pluck('relationship_type')->all();

        $this->assertSame(['Son'], $remaining);
        $this->assertDatabaseMissing('client_relationships', [
            'client_id' => 100,
            'relationship_type' => 'Engaged',
        ]);
        $this->assertDatabaseMissing('client_relationships', [
            'client_id' => 100,
            'relationship_type' => 'Husband',
        ]);
    }

    public function test_partners_scope_includes_ex_husband(): void
    {
        ClientRelationship::query()->create([
            'admin_id' => 1,
            'client_id' => 101,
            'related_client_id' => 203,
            'details' => 'Ex husband',
            'relationship_type' => 'Ex-Husband',
        ]);

        $this->assertSame(
            1,
            ClientRelationship::where('client_id', 101)->partners()->count()
        );
    }
}
