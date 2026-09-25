<?php

namespace Tests\Unit;

use App\Models\Admin;
use App\Services\EmailConfigService;
use App\Services\SignatureService;
use App\Services\SystemEmailLogService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SignatureSuggestAssociationIgnoresArchivedTest extends TestCase
{
    private SignatureService $signatureService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createSchema();

        $this->signatureService = new SignatureService(
            new EmailConfigService,
            app(SystemEmailLogService::class)
        );
    }

    public function test_suggest_association_ignores_archived_lead(): void
    {
        Admin::factory()->create([
            'type' => 'lead',
            'email' => 'viplucmca@yahoo.co.in',
            'first_name' => 'Vipul',
            'last_name' => 'Kumar',
            'is_company' => 0,
            'is_archived' => 1,
        ]);

        $this->assertNull($this->signatureService->suggestAssociation('viplucmca@yahoo.co.in'));
    }

    public function test_suggest_association_returns_active_client(): void
    {
        $active = Admin::factory()->create([
            'type' => 'client',
            'email' => 'viplucmca@yahoo.co.in',
            'first_name' => 'Vipul',
            'last_name' => 'Kumar',
            'is_company' => 0,
            'is_archived' => 0,
        ]);

        Admin::factory()->create([
            'type' => 'lead',
            'email' => 'viplucmca@yahoo.co.in',
            'first_name' => 'Vipul',
            'last_name' => 'Kumar',
            'is_company' => 0,
            'is_archived' => 1,
        ]);

        $suggestion = $this->signatureService->suggestAssociation('viplucmca@yahoo.co.in');

        $this->assertNotNull($suggestion);
        $this->assertSame('client', $suggestion['type']);
        $this->assertSame($active->id, $suggestion['id']);
    }

    public function test_suggest_association_treats_null_is_archived_as_active(): void
    {
        $legacy = Admin::factory()->create([
            'type' => 'lead',
            'email' => 'legacy@test.com',
            'first_name' => 'Legacy',
            'last_name' => 'Lead',
            'is_company' => 0,
            'is_archived' => null,
        ]);

        $suggestion = $this->signatureService->suggestAssociation('legacy@test.com');

        $this->assertNotNull($suggestion);
        $this->assertSame($legacy->id, $suggestion['id']);
    }

    private function createSchema(): void
    {
        if (! Schema::hasTable('admins')) {
            Schema::create('admins', function (Blueprint $table) {
                $table->id();
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->string('password')->nullable();
                $table->string('type')->nullable();
                $table->string('country')->nullable();
                $table->string('state')->nullable();
                $table->string('city')->nullable();
                $table->string('address')->nullable();
                $table->string('zip')->nullable();
                $table->integer('status')->nullable();
                $table->date('dob')->nullable();
                $table->unsignedInteger('is_deleted')->nullable();
                $table->unsignedInteger('is_archived')->nullable();
                $table->unsignedTinyInteger('is_company')->nullable();
                $table->string('client_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('matters')) {
            Schema::create('matters', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('client_matters')) {
            Schema::create('client_matters', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id')->nullable();
                $table->unsignedBigInteger('sel_matter_id')->nullable();
                $table->string('client_unique_matter_no')->nullable();
                $table->integer('matter_status')->nullable();
                $table->timestamps();
            });
        }
    }
}
