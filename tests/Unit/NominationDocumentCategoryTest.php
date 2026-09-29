<?php

namespace Tests\Unit;

use App\Http\Middleware\TrackStaffCrmActivity;
use App\Http\Middleware\VerifyCsrfToken;
use App\Models\NominationDocumentType;
use App\Models\Staff;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NominationDocumentCategoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            VerifyCsrfToken::class,
            TrackStaffCrmActivity::class,
        ]);

        $this->createSchema();
    }

    #[Test]
    public function nomination_blade_hides_edit_delete_for_default_titles(): void
    {
        $blade = file_get_contents(base_path('resources/views/crm/companies/tabs/nomination_documents.blade.php'));
        Assert::assertNotFalse($blade);
        Assert::assertStringContainsString('$isUserManagedNominationCategory', $blade);
        Assert::assertStringContainsString('delete-nomination-cat-title', $blade);
        Assert::assertStringContainsString('nomination_document_category_default_titles', $blade);
    }

    #[Test]
    public function delete_nomination_category_removes_custom_matter_scoped_category(): void
    {
        $staff = Staff::query()->create([
            'first_name' => 'File',
            'last_name' => 'Docs',
            'email' => 'file-docs-delete@test.com',
            'password' => Hash::make('password'),
            'role' => 1,
            'status' => 1,
        ]);

        $category = NominationDocumentType::query()->create([
            'title' => 'TESTFD',
            'status' => 1,
            'client_id' => 42,
            'client_matter_id' => 9,
        ]);

        $response = $this->actingAs($staff, 'admin')
            ->postJson('/documents/delete-nomination-category', [
                'id' => $category->id,
            ]);

        $response->assertOk();
        $response->assertJson([
            'status' => true,
        ]);
        Assert::assertNull(NominationDocumentType::query()->find($category->id));
    }

    #[Test]
    public function delete_nomination_category_rejects_default_general_title(): void
    {
        $staff = Staff::query()->create([
            'first_name' => 'Default',
            'last_name' => 'Guard',
            'email' => 'file-docs-default@test.com',
            'password' => Hash::make('password'),
            'role' => 1,
            'status' => 1,
        ]);

        $category = NominationDocumentType::query()->create([
            'title' => 'General',
            'status' => 1,
            'client_id' => 42,
            'client_matter_id' => 9,
        ]);

        $response = $this->actingAs($staff, 'admin')
            ->postJson('/documents/delete-nomination-category', [
                'id' => $category->id,
            ]);

        $response->assertOk();
        $response->assertJson([
            'status' => false,
            'message' => 'Default categories cannot be deleted.',
        ]);
        Assert::assertNotNull(NominationDocumentType::query()->find($category->id));
    }

    #[Test]
    public function update_nomination_category_rejects_default_general_title(): void
    {
        $staff = Staff::query()->create([
            'first_name' => 'Default',
            'last_name' => 'Update',
            'email' => 'file-docs-update@test.com',
            'password' => Hash::make('password'),
            'role' => 1,
            'status' => 1,
        ]);

        $category = NominationDocumentType::query()->create([
            'title' => 'General',
            'status' => 1,
            'client_id' => 42,
            'client_matter_id' => 9,
        ]);

        $response = $this->actingAs($staff, 'admin')
            ->postJson('/documents/update-nomination-category', [
                'id' => $category->id,
                'title' => 'Renamed General',
            ]);

        $response->assertOk();
        $response->assertJson([
            'status' => false,
            'message' => 'Default categories cannot be updated.',
        ]);
    }

    private function createSchema(): void
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

        if (! Schema::hasTable('nomination_document_types')) {
            Schema::create('nomination_document_types', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->unsignedTinyInteger('status')->nullable();
                $table->unsignedBigInteger('client_id')->nullable();
                $table->unsignedBigInteger('client_matter_id')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('documents')) {
            Schema::create('documents', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('client_matter_id')->nullable();
                $table->string('doc_type')->nullable();
                $table->string('type')->nullable();
                $table->string('folder_name')->nullable();
                $table->string('file_name')->nullable();
                $table->string('checklist')->nullable();
                $table->string('myfile')->nullable();
                $table->string('myfile_key')->nullable();
                $table->unsignedTinyInteger('not_used_doc')->nullable();
                $table->timestamps();
            });
        }
    }
}
