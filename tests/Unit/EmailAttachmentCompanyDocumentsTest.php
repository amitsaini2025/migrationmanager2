<?php

namespace Tests\Unit;

use App\Http\Controllers\CRM\EmailUploadController;
use App\Http\Middleware\TrackStaffCrmActivity;
use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Admin;
use App\Models\NominationDocumentType;
use App\Models\PersonalDocumentType;
use App\Models\Staff;
use App\Models\VisaDocumentType;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\Test;
use ReflectionMethod;
use Tests\TestCase;

class EmailAttachmentCompanyDocumentsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            VerifyCsrfToken::class,
            TrackStaffCrmActivity::class,
        ]);

        $this->createSchemas();
    }

    #[Test]
    public function company_client_categories_use_company_and_file_documents(): void
    {
        $staff = $this->makeStaff();
        $company = Admin::query()->create([
            'first_name' => 'Acme',
            'type' => 'client',
            'is_company' => 1,
            'status' => 1,
        ]);

        PersonalDocumentType::query()->create([
            'title' => 'Company General',
            'status' => 1,
            'client_id' => $company->id,
            'type' => 'company',
        ]);
        PersonalDocumentType::query()->create([
            'title' => 'Person Only',
            'status' => 1,
            'client_id' => $company->id,
            'type' => 'personal',
        ]);
        NominationDocumentType::query()->create([
            'title' => 'File General',
            'status' => 1,
            'client_id' => $company->id,
            'client_matter_id' => 88,
        ]);
        VisaDocumentType::query()->create([
            'title' => 'Visa Forms',
            'status' => 1,
            'client_id' => $company->id,
            'client_matter_id' => 88,
        ]);

        $response = $this->actingAs($staff, 'admin')
            ->getJson('/email/attachment-document-categories?client_id='.$company->id.'&client_matter_id=88');

        $response->assertOk();
        $response->assertJson([
            'status' => true,
            'client_kind' => 'company',
        ]);

        $categories = collect($response->json('categories'));
        Assert::assertTrue($categories->contains(fn ($c) => ($c['doc_type'] ?? '') === 'personal' && ($c['name'] ?? '') === 'Company General'));
        Assert::assertTrue($categories->contains(fn ($c) => ($c['doc_type'] ?? '') === 'nomination' && ($c['name'] ?? '') === 'File General'));
        Assert::assertFalse($categories->contains(fn ($c) => ($c['name'] ?? '') === 'Person Only'));
        Assert::assertFalse($categories->contains(fn ($c) => ($c['doc_type'] ?? '') === 'visa'));
    }

    #[Test]
    public function person_client_categories_stay_personal_and_visa(): void
    {
        $staff = $this->makeStaff();
        $person = Admin::query()->create([
            'first_name' => 'Jane',
            'type' => 'client',
            'is_company' => 0,
            'status' => 1,
        ]);

        PersonalDocumentType::query()->create([
            'title' => 'Identity',
            'status' => 1,
            'client_id' => $person->id,
            'type' => 'personal',
        ]);
        PersonalDocumentType::query()->create([
            'title' => 'Company Only',
            'status' => 1,
            'client_id' => $person->id,
            'type' => 'company',
        ]);
        VisaDocumentType::query()->create([
            'title' => 'Visa Forms',
            'status' => 1,
            'client_id' => $person->id,
            'client_matter_id' => 55,
        ]);
        NominationDocumentType::query()->create([
            'title' => 'File General',
            'status' => 1,
            'client_id' => $person->id,
            'client_matter_id' => 55,
        ]);

        $response = $this->actingAs($staff, 'admin')
            ->getJson('/email/attachment-document-categories?client_id='.$person->id.'&client_matter_id=55');

        $response->assertOk();
        $response->assertJson([
            'status' => true,
            'client_kind' => 'person',
        ]);

        $categories = collect($response->json('categories'));
        Assert::assertTrue($categories->contains(fn ($c) => ($c['doc_type'] ?? '') === 'personal' && ($c['name'] ?? '') === 'Identity'));
        Assert::assertTrue($categories->contains(fn ($c) => ($c['doc_type'] ?? '') === 'visa' && ($c['name'] ?? '') === 'Visa Forms'));
        Assert::assertFalse($categories->contains(fn ($c) => ($c['name'] ?? '') === 'Company Only'));
        Assert::assertFalse($categories->contains(fn ($c) => ($c['doc_type'] ?? '') === 'nomination'));
    }

    #[Test]
    public function destination_rules_keep_person_and_company_paths_separate(): void
    {
        $controller = app(EmailUploadController::class);
        $normalize = new ReflectionMethod(EmailUploadController::class, 'normalizeAttachmentDocumentType');
        $normalize->setAccessible(true);
        $allowed = new ReflectionMethod(EmailUploadController::class, 'attachmentDocumentDestinationAllowed');
        $allowed->setAccessible(true);

        Assert::assertSame('personal', $normalize->invoke($controller, 'company'));
        Assert::assertSame('nomination', $normalize->invoke($controller, 'nomination'));

        Assert::assertTrue($allowed->invoke($controller, 'personal', true));
        Assert::assertTrue($allowed->invoke($controller, 'nomination', true));
        Assert::assertFalse($allowed->invoke($controller, 'visa', true));

        Assert::assertTrue($allowed->invoke($controller, 'personal', false));
        Assert::assertTrue($allowed->invoke($controller, 'visa', false));
        Assert::assertFalse($allowed->invoke($controller, 'nomination', false));
    }

    #[Test]
    public function email_upload_flow_labels_company_destinations(): void
    {
        $js = file_get_contents(base_path('public/js/email-upload-flow.js'));
        Assert::assertNotFalse($js);
        Assert::assertStringContainsString("appendGroup(isCompany ? 'Company Documents' : 'Personal Documents', personal, 'personal');", $js);
        Assert::assertStringContainsString("appendGroup('File Documents', nomination, 'nomination');", $js);
        Assert::assertStringContainsString("appendGroup('Visa Documents', visa, 'visa');", $js);
    }

    private function makeStaff(): Staff
    {
        return Staff::query()->create([
            'first_name' => 'Email',
            'last_name' => 'Docs',
            'email' => 'email-docs-'.uniqid('', true).'@test.com',
            'password' => Hash::make('password'),
            'role' => 1,
            'status' => 1,
        ]);
    }

    private function createSchemas(): void
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

        if (! Schema::hasTable('admins')) {
            Schema::create('admins', function (Blueprint $table) {
                $table->id();
                $table->string('first_name')->nullable();
                $table->string('type')->nullable();
                $table->unsignedTinyInteger('is_company')->nullable();
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

        if (! Schema::hasTable('visa_document_types')) {
            Schema::create('visa_document_types', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->unsignedTinyInteger('status')->nullable();
                $table->unsignedBigInteger('client_id')->nullable();
                $table->unsignedBigInteger('client_matter_id')->nullable();
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
    }
}
