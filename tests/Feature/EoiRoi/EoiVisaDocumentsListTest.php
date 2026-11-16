<?php

namespace Tests\Feature\EoiRoi;

use App\Http\Middleware\TrackStaffCrmActivity;
use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Admin;
use App\Models\Document;
use App\Models\Staff;
use App\Models\VisaDocumentType;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Mockery;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EoiVisaDocumentsListTest extends TestCase
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
    public function visa_documents_list_uses_db_file_size_and_skips_s3(): void
    {
        $disk = Mockery::mock(Filesystem::class);
        $disk->shouldReceive('exists')->never();
        $disk->shouldReceive('size')->never();
        Storage::set('s3', $disk);

        $staff = $this->makeStaff();
        $client = Admin::query()->create([
            'first_name' => 'Harjinder',
            'last_name' => 'Pal',
            'type' => 'client',
            'status' => 1,
        ]);

        $category = VisaDocumentType::query()->create([
            'title' => 'EOI Summary - E0042718076',
            'status' => 1,
            'client_id' => $client->id,
        ]);

        $otherCategory = VisaDocumentType::query()->create([
            'title' => 'Passport',
            'status' => 1,
            'client_id' => $client->id,
        ]);

        $eoiDoc = Document::query()->create([
            'client_id' => $client->id,
            'user_id' => $staff->id,
            'doc_type' => 'visa',
            'type' => 'client',
            'folder_name' => (string) $category->id,
            'file_name' => 'eoi-summary.pdf',
            'myfile' => 'https://example.test/eoi-summary.pdf',
            'myfile_key' => 'eoi-summary.pdf',
            'file_size' => 2097152, // 2 MB
            'not_used_doc' => null,
        ]);

        Document::query()->create([
            'client_id' => $client->id,
            'user_id' => $staff->id,
            'doc_type' => 'visa',
            'type' => 'client',
            'folder_name' => (string) $otherCategory->id,
            'file_name' => 'passport.pdf',
            'myfile' => 'https://example.test/passport.pdf',
            'myfile_key' => 'passport.pdf',
            'file_size' => 1048576, // 1 MB
            'not_used_doc' => null,
        ]);

        // Excluded: personal / not used / missing myfile
        Document::query()->create([
            'client_id' => $client->id,
            'user_id' => $staff->id,
            'doc_type' => 'personal',
            'type' => 'client',
            'folder_name' => (string) $otherCategory->id,
            'file_name' => 'personal.pdf',
            'myfile' => 'https://example.test/personal.pdf',
            'file_size' => 1000,
        ]);
        Document::query()->create([
            'client_id' => $client->id,
            'user_id' => $staff->id,
            'doc_type' => 'visa',
            'type' => 'client',
            'folder_name' => (string) $otherCategory->id,
            'file_name' => 'unused.pdf',
            'myfile' => 'https://example.test/unused.pdf',
            'file_size' => 1000,
            'not_used_doc' => 1,
        ]);

        $response = $this->actingAs($staff, 'admin')
            ->getJson('/clients/'.$client->id.'/eoi-roi/visa-documents?eoi_number=E0042718076');

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'data' => [
                    'total_count' => 2,
                ],
            ]);

        $eoiRefs = collect($response->json('data.eoi_references'));
        $others = collect($response->json('data.other_documents'));

        Assert::assertCount(1, $eoiRefs);
        Assert::assertSame($eoiDoc->id, $eoiRefs->first()['id']);
        Assert::assertSame('EOI Summary - E0042718076', $eoiRefs->first()['category']);
        Assert::assertSame(2.0, (float) $eoiRefs->first()['file_size_mb']);

        Assert::assertCount(1, $others);
        Assert::assertSame('Passport', $others->first()['category']);
        Assert::assertSame(1.0, (float) $others->first()['file_size_mb']);
    }

    #[Test]
    public function get_visa_documents_source_does_not_call_s3_for_listing(): void
    {
        $source = file_get_contents(base_path('app/Http/Controllers/CRM/ClientEoiRoiController.php'));
        Assert::assertNotFalse($source);

        $methodStart = strpos($source, 'public function getVisaDocuments');
        Assert::assertNotFalse($methodStart);
        $methodEnd = strpos($source, 'public function getEmailPreview', $methodStart);
        Assert::assertNotFalse($methodEnd);
        $methodBody = substr($source, $methodStart, $methodEnd - $methodStart);

        Assert::assertStringNotContainsString("Storage::disk('s3')", $methodBody);
        Assert::assertStringContainsString('(float) $doc->file_size', $methodBody);
        Assert::assertStringContainsString("created_at' => \$doc->created_at?->toIso8601String()", $methodBody);
    }

    private function makeStaff(): Staff
    {
        return Staff::query()->create([
            'first_name' => 'EOI',
            'last_name' => 'Docs',
            'email' => 'eoi-docs-'.uniqid('', true).'@test.com',
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
                $table->string('last_name')->nullable();
                $table->string('type')->nullable();
                $table->unsignedInteger('status')->nullable();
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

        if (! Schema::hasTable('documents')) {
            Schema::create('documents', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('doc_type')->nullable();
                $table->string('type')->nullable();
                $table->string('folder_name')->nullable();
                $table->string('file_name')->nullable();
                $table->string('myfile')->nullable();
                $table->string('myfile_key')->nullable();
                $table->string('file_size')->nullable();
                $table->unsignedTinyInteger('not_used_doc')->nullable();
                $table->timestamps();
            });
        }
    }
}
