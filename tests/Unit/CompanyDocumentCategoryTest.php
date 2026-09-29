<?php

namespace Tests\Unit;

use App\Http\Middleware\TrackStaffCrmActivity;
use App\Http\Middleware\VerifyCsrfToken;
use App\Models\PersonalDocumentType;
use App\Models\Staff;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CompanyDocumentCategoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            VerifyCsrfToken::class,
            TrackStaffCrmActivity::class,
        ]);

        $this->createPersonalDocumentTypeSchema();
    }

    #[Test]
    public function company_detail_shows_add_category_for_company_documents(): void
    {
        $companyDetail = file_get_contents(base_path('resources/views/crm/companies/detail.blade.php'));
        Assert::assertNotFalse($companyDetail);
        Assert::assertStringContainsString("['companyDocumentsOnlyGeneral' => true]", $companyDetail);

        $personalDocumentsBlade = file_get_contents(base_path('resources/views/crm/clients/tabs/personal_documents.blade.php'));
        Assert::assertNotFalse($personalDocumentsBlade);
        Assert::assertStringContainsString("data-type=\"{{ \$companyDocumentsOnlyGeneral ? 'company' : 'personal' }}\"", $personalDocumentsBlade);
        Assert::assertStringNotContainsString('@if (!$companyDocumentsOnlyGeneral)', $personalDocumentsBlade);
    }

    #[Test]
    public function add_personal_category_endpoint_saves_company_type_when_requested(): void
    {
        $staff = Staff::query()->create([
            'first_name' => 'Company',
            'last_name' => 'Docs',
            'email' => 'company-docs-category@test.com',
            'password' => Hash::make('password'),
            'role' => 1,
            'status' => 1,
        ]);

        $response = $this->actingAs($staff, 'admin')
            ->postJson('/documents/add-personal-category', [
                'personal_doc_category' => 'Compliance',
                'clientid' => 42,
                'category_type' => 'company',
            ]);

        $response->assertOk();
        $response->assertJson([
            'status' => true,
            'title' => 'Compliance',
        ]);

        $category = PersonalDocumentType::query()->where('title', 'Compliance')->first();
        Assert::assertNotNull($category);
        Assert::assertSame('company', $category->type);
        Assert::assertSame(42, (int) $category->client_id);
    }

    #[Test]
    public function add_personal_category_defaults_to_personal_type_for_existing_clients(): void
    {
        $staff = Staff::query()->create([
            'first_name' => 'Personal',
            'last_name' => 'Docs',
            'email' => 'personal-docs-category@test.com',
            'password' => Hash::make('password'),
            'role' => 1,
            'status' => 1,
        ]);

        $response = $this->actingAs($staff, 'admin')
            ->postJson('/documents/add-personal-category', [
                'personal_doc_category' => 'Identity',
                'clientid' => 7,
            ]);

        $response->assertOk();
        $response->assertJson([
            'status' => true,
            'title' => 'Identity',
        ]);

        $category = PersonalDocumentType::query()->where('title', 'Identity')->first();
        Assert::assertNotNull($category);
        Assert::assertSame('personal', $category->type);
    }

    #[Test]
    public function same_title_can_exist_for_personal_and_company_on_same_client(): void
    {
        $staff = Staff::query()->create([
            'first_name' => 'Scope',
            'last_name' => 'Tester',
            'email' => 'scope-docs-category@test.com',
            'password' => Hash::make('password'),
            'role' => 1,
            'status' => 1,
        ]);

        PersonalDocumentType::query()->create([
            'title' => 'Contracts',
            'status' => 1,
            'client_id' => 99,
            'type' => 'personal',
        ]);

        $response = $this->actingAs($staff, 'admin')
            ->postJson('/documents/add-personal-category', [
                'personal_doc_category' => 'Contracts',
                'clientid' => 99,
                'category_type' => 'company',
            ]);

        $response->assertOk();
        $response->assertJson([
            'status' => true,
            'title' => 'Contracts',
        ]);

        Assert::assertSame(2, PersonalDocumentType::query()->where('title', 'Contracts')->where('client_id', 99)->count());
    }

    private function createPersonalDocumentTypeSchema(): void
    {
        if (! Schema::hasTable('staff')) {
            Schema::create('staff', function (Blueprint $table) {
                $table->id();
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('email')->nullable();
                $table->string('password')->nullable();
                $table->unsignedInteger('role')->nullable();
                $table->unsignedInteger('status')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('personal_document_types')) {
            Schema::create('personal_document_types', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->unsignedTinyInteger('status')->nullable();
                $table->unsignedBigInteger('client_id')->nullable();
                $table->string('type')->nullable();
                $table->timestamps();
            });
        }
    }
}
