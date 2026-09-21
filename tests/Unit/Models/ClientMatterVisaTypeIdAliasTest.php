<?php

namespace Tests\Unit\Models;

use App\Models\ClientMatter;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ClientMatterVisaTypeIdAliasTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('client_matters');
        Schema::dropIfExists('matters');

        Schema::create('matters', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->nullable();
            $table->string('nick_name')->nullable();
        });

        Schema::create('client_matters', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('client_id')->nullable();
            $table->unsignedInteger('sel_matter_id')->nullable();
            $table->string('client_unique_matter_no')->nullable();
            $table->timestamps();
        });

        DB::table('matters')->insert([
            'id' => 7,
            'title' => 'Employer Nomination Scheme',
            'nick_name' => 'EOL',
        ]);

        DB::table('client_matters')->insert([
            'id' => 21,
            'client_id' => 15,
            'sel_matter_id' => 7,
            'client_unique_matter_no' => 'EOL_1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('client_matters');
        Schema::dropIfExists('matters');

        parent::tearDown();
    }

    #[Test]
    public function selecting_visa_type_id_reads_sel_matter_id(): void
    {
        $matter = ClientMatter::query()
            ->select('id', 'client_id', 'client_unique_matter_no', 'visa_type_id')
            ->where('client_unique_matter_no', 'EOL_1')
            ->first();

        $this->assertNotNull($matter);
        $this->assertSame(21, (int) $matter->id);
        $this->assertSame(15, (int) $matter->client_id);
        $this->assertSame('EOL_1', $matter->client_unique_matter_no);
        $this->assertSame(7, (int) $matter->visa_type_id);
        $this->assertSame(7, (int) $matter->sel_matter_id);
    }

    #[Test]
    public function visa_type_relation_uses_sel_matter_id(): void
    {
        $relation = (new ClientMatter)->visaType();

        $this->assertSame('sel_matter_id', $relation->getForeignKeyName());
        $this->assertSame((new ClientMatter)->matter()->getForeignKeyName(), $relation->getForeignKeyName());

        $matter = ClientMatter::query()
            ->where('client_unique_matter_no', 'EOL_1')
            ->with('visaType')
            ->first();

        $this->assertNotNull($matter);
        $this->assertSame('Employer Nomination Scheme', $matter->visaType?->title);
        $this->assertSame($matter->matter?->id, $matter->visaType?->id);
    }

    #[Test]
    public function existing_sel_matter_id_queries_still_work(): void
    {
        $matter = ClientMatter::query()
            ->select('id', 'sel_matter_id', 'client_unique_matter_no')
            ->where('client_unique_matter_no', 'EOL_1')
            ->first();

        $this->assertNotNull($matter);
        $this->assertSame(7, (int) $matter->sel_matter_id);
        $this->assertSame(7, (int) $matter->visa_type_id);
    }
}
