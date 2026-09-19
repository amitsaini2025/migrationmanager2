<?php

namespace Tests\Unit\Models;

use App\Models\ClientMatter;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ClientMatterFileSearchTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('client_matters');
        Schema::dropIfExists('matters');
        Schema::dropIfExists('admins');

        Schema::create('admins', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type')->nullable();
            $table->string('client_id')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->timestamps();
        });

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

        DB::table('admins')->insert([
            'id' => 10,
            'type' => 'client',
            'client_id' => 'VIPL2400001',
            'first_name' => 'Jane',
            'last_name' => 'Client',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('admins')->insert([
            'id' => 11,
            'type' => 'client',
            'client_id' => 'JARN2504926',
            'first_name' => 'Sam',
            'last_name' => 'Other',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('matters')->insert([
            'id' => 3,
            'title' => 'Partner visa',
            'nick_name' => '820',
        ]);
        DB::table('client_matters')->insert([
            'id' => 5,
            'client_id' => 10,
            'sel_matter_id' => 3,
            'client_unique_matter_no' => 'APC_8',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('client_matters')->insert([
            'id' => 6,
            'client_id' => 11,
            'sel_matter_id' => 3,
            'client_unique_matter_no' => 'JARN2504926-485_1',
            'created_at' => now()->subMinute(),
            'updated_at' => now()->subMinute(),
        ]);
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('client_matters');
        Schema::dropIfExists('matters');
        Schema::dropIfExists('admins');

        parent::tearDown();
    }

    #[Test]
    public function matching_file_search_finds_matter_suffix_and_combined_client_ref(): void
    {
        $bySuffix = ClientMatter::query()->matchingFileSearch('APC_8')->pluck('id')->all();
        $byFull = ClientMatter::query()->matchingFileSearch('VIPL2400001-APC_8')->pluck('id')->all();
        $byClientCode = ClientMatter::query()->matchingFileSearch('VIPL2400001')->pluck('id')->all();

        $this->assertSame([5], $bySuffix);
        $this->assertSame([5], $byFull);
        $this->assertSame([5], $byClientCode);
    }

    #[Test]
    public function matching_file_search_still_matches_stored_unique_no_name_and_title(): void
    {
        $byStored = ClientMatter::query()->matchingFileSearch('JARN2504926-485_1')->pluck('id')->all();
        $byName = ClientMatter::query()->matchingFileSearch('Jane')->pluck('id')->all();
        $byTitle = ClientMatter::query()->matchingFileSearch('Partner visa')->pluck('id')->all();

        $this->assertSame([6], $byStored);
        $this->assertSame([5], $byName);
        $this->assertContains(5, $byTitle);
        $this->assertContains(6, $byTitle);
    }

    #[Test]
    public function matching_file_search_does_not_match_unrelated_query(): void
    {
        $ids = ClientMatter::query()->matchingFileSearch('ZZZZ9999')->pluck('id')->all();

        $this->assertSame([], $ids);
    }
}
