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

class PreviewOriginalDocumentTest extends TestCase
{
    protected Staff $staff;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([TrackStaffCrmActivity::class]);

        $this->createSchema();

        $this->staff = Staff::create([
            'first_name' => 'Preview',
            'last_name' => 'Tester',
            'email' => 'preview-original@test.com',
            'password' => Hash::make('password'),
            'role' => 1,
            'status' => 1,
        ]);
    }

    #[Test]
    public function original_preview_route_is_registered(): void
    {
        $this->assertTrue(Route::has('documents.preview.original'));
    }

    #[Test]
    public function guests_cannot_preview_the_original(): void
    {
        $document = $this->makeDocument('documents/guest.pdf', 'sent');
        Storage::fake('public');
        Storage::disk('public')->put('documents/guest.pdf', 'ORIGINAL');

        $this->get(route('documents.preview.original', $document->id))
            ->assertRedirect();
    }

    #[Test]
    public function staff_can_preview_original_inline_without_changing_document(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('documents/jrp.pdf', 'UNSIGNED-PDF');

        $document = $this->makeDocument('documents/jrp.pdf', 'sent');

        $response = $this->actingAs($this->staff, 'admin')
            ->get(route('documents.preview.original', $document->id));

        $response->assertOk();
        $this->assertStringContainsString('UNSIGNED-PDF', $response->streamedContent());
        $this->assertStringContainsString('inline', (string) $response->headers->get('content-disposition'));
        $this->assertSame('application/pdf', $response->headers->get('content-type'));

        $this->assertSame('documents/jrp.pdf', $document->fresh()->myfile);
    }

    #[Test]
    public function missing_original_file_returns_not_found_for_preview(): void
    {
        Storage::fake('public');

        $document = $this->makeDocument('documents/missing.pdf', 'sent');

        $this->actingAs($this->staff, 'admin')
            ->get(route('documents.preview.original', $document->id))
            ->assertNotFound();
    }

    private function makeDocument(string $myfile, string $status): Document
    {
        $document = new Document;
        $document->file_name = 'jrp.pdf';
        $document->filetype = 'pdf';
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
