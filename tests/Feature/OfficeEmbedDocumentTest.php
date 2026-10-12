<?php

namespace Tests\Feature;

use App\Http\Middleware\TrackStaffCrmActivity;
use App\Models\Document;
use App\Models\Staff;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OfficeEmbedDocumentTest extends TestCase
{
    protected Staff $staff;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([TrackStaffCrmActivity::class]);

        $this->createSchema();

        $this->staff = Staff::create([
            'first_name' => 'Office',
            'last_name' => 'Embed',
            'email' => 'office-embed@test.com',
            'password' => Hash::make('password'),
            'role' => 1,
            'status' => 1,
        ]);
    }

    #[Test]
    public function office_embed_route_is_registered(): void
    {
        $this->assertTrue(Route::has('documents.office_embed_url'));
    }

    #[Test]
    public function guests_cannot_request_office_embed_url(): void
    {
        $document = $this->makeDocument('documents/guest.docx', 'sent', 'guest.docx', 'docx');

        $this->getJson(route('documents.office_embed_url', $document->id))
            ->assertUnauthorized();
    }

    #[Test]
    public function staff_cannot_get_office_embed_url_for_non_office_types(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('documents/sample.pdf', 'PDF');

        $document = $this->makeDocument('documents/sample.pdf', 'sent');

        $this->actingAs($this->staff, 'admin')
            ->getJson(route('documents.office_embed_url', $document->id))
            ->assertUnprocessable();
    }

    #[Test]
    public function staff_can_get_public_url_for_local_docx_on_public_disk(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('documents/sample.docx', 'DOCX');

        $document = $this->makeDocument('documents/sample.docx', 'sent', 'sample.docx', 'docx');

        $response = $this->actingAs($this->staff, 'admin')
            ->getJson(route('documents.office_embed_url', $document->id));

        $response->assertOk();
        $response->assertJsonStructure(['url']);
        $this->assertStringContainsString('documents/sample.docx', (string) $response->json('url'));
    }

    private function makeDocument(string $myfile, string $status, string $fileName = 'jrp.pdf', string $filetype = 'pdf'): Document
    {
        $document = new Document;
        $document->file_name = $fileName;
        $document->filetype = $filetype;
        $document->myfile = $myfile;
        $document->status = $status;
        $document->created_by = $this->staff->id;
        $document->save();

        return $document;
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

        if (! Schema::hasTable('documents')) {
            Schema::create('documents', function (Blueprint $table) {
                $table->id();
                $table->string('file_name')->nullable();
                $table->string('filetype')->nullable();
                $table->string('myfile')->nullable();
                $table->string('myfile_key')->nullable();
                $table->string('status')->nullable();
                $table->string('signed_doc_link')->nullable();
                $table->unsignedBigInteger('client_id')->nullable();
                $table->unsignedBigInteger('lead_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->string('doc_type')->nullable();
                $table->unsignedBigInteger('form956_id')->nullable();
                $table->timestamps();
            });
        }
    }
}
