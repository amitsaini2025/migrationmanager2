<?php

namespace Tests\Feature;

use App\Http\Middleware\TrackStaffCrmActivity;
use App\Models\Admin;
use App\Models\Document;
use App\Models\Staff;
use App\Models\UserRole;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use ZipArchive;

class BulkDownloadClientDocumentsTest extends TestCase
{
    protected Staff $staffWithPermission;

    protected Staff $staffWithoutPermission;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([TrackStaffCrmActivity::class]);

        $this->createSchema();

        UserRole::query()->updateOrCreate(
            ['id' => 1],
            ['name' => 'Super Admin', 'module_access' => json_encode(['20' => 'on', '84' => 'on'])]
        );

        UserRole::query()->updateOrCreate(
            ['id' => 92],
            ['name' => 'No Bulk Download', 'module_access' => json_encode(['20' => 'on'])]
        );

        $this->staffWithPermission = Staff::create([
            'first_name' => 'Allowed',
            'last_name' => 'Staff',
            'email' => 'bulk-download-allowed@test.com',
            'password' => Hash::make('password'),
            'role' => 1,
            'status' => 1,
        ]);

        $this->staffWithoutPermission = Staff::create([
            'first_name' => 'Denied',
            'last_name' => 'Staff',
            'email' => 'bulk-download-denied@test.com',
            'password' => Hash::make('password'),
            'role' => 92,
            'status' => 1,
        ]);
    }

    #[Test]
    public function bulk_download_route_is_registered(): void
    {
        $this->assertTrue(Route::has('clients.documents.bulkDownload'));
    }

    #[Test]
    public function guests_cannot_bulk_download(): void
    {
        $document = $this->makePersonalDocument($this->makeClient()->id, 'guest.pdf');

        $this->post(route('clients.documents.bulkDownload'), [
            'client_id' => $document->client_id,
            'document_ids' => [$document->id],
        ])->assertRedirect();
    }

    #[Test]
    public function staff_without_module_84_receive_forbidden(): void
    {
        $client = $this->makeClient(ownerStaffId: (int) $this->staffWithoutPermission->id);
        $document = $this->makePersonalDocument($client->id, 'denied.pdf');
        Storage::fake('public');
        Storage::disk('public')->put('documents/denied.pdf', 'DENIED');

        $this->actingAs($this->staffWithoutPermission, 'admin')
            ->post(route('clients.documents.bulkDownload'), [
                'client_id' => $client->id,
                'document_ids' => [$document->id],
            ])
            ->assertForbidden();
    }

    #[Test]
    public function staff_with_module_84_receive_zip_containing_selected_file(): void
    {
        $client = $this->makeClient('Jane', 'Doe');
        Storage::fake('public');
        Storage::disk('public')->put('documents/passport.pdf', 'PASSPORT-BYTES');

        $document = $this->makePersonalDocument($client->id, 'documents/passport.pdf');

        $response = $this->actingAs($this->staffWithPermission, 'admin')
            ->post(route('clients.documents.bulkDownload'), [
                'client_id' => $client->id,
                'document_ids' => [$document->id],
            ]);

        $response->assertOk();
        $this->assertStringContainsString('attachment', (string) $response->headers->get('content-disposition'));
        $this->assertStringContainsString('.zip', (string) $response->headers->get('content-disposition'));

        $zipPath = storage_path('app/temp/test_bulk_download_assert.zip');
        if (! is_dir(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0755, true);
        }
        file_put_contents($zipPath, $response->streamedContent());

        $zip = new ZipArchive;
        $this->assertTrue($zip->open($zipPath) === true);
        $this->assertGreaterThanOrEqual(1, $zip->numFiles);
        $names = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $names[] = $zip->getNameIndex($i);
        }
        $zip->close();
        @unlink($zipPath);

        $this->assertTrue(
            collect($names)->contains(fn (string $name) => str_contains(strtolower($name), 'passport')),
            'ZIP should contain passport file, got: '.implode(', ', $names)
        );
    }

    #[Test]
    public function document_ids_for_another_client_are_rejected(): void
    {
        $clientA = $this->makeClient('Client', 'A');
        $clientB = $this->makeClient('Client', 'B');
        $foreignDoc = $this->makePersonalDocument($clientB->id, 'documents/other.pdf');

        Storage::fake('public');
        Storage::disk('public')->put('documents/other.pdf', 'OTHER');

        $this->actingAs($this->staffWithPermission, 'admin')
            ->post(route('clients.documents.bulkDownload'), [
                'client_id' => $clientA->id,
                'document_ids' => [$foreignDoc->id],
            ])
            ->assertForbidden();
    }

    #[Test]
    public function empty_selection_is_rejected(): void
    {
        $client = $this->makeClient();

        $this->actingAs($this->staffWithPermission, 'admin')
            ->postJson(route('clients.documents.bulkDownload'), [
                'client_id' => $client->id,
                'document_ids' => [],
            ])
            ->assertStatus(422);
    }

    #[Test]
    public function dibp_receipt_documents_are_rejected(): void
    {
        $client = $this->makeClient();
        Storage::fake('public');
        Storage::disk('public')->put('documents/receipt.pdf', 'RECEIPT');

        $document = $this->makePersonalDocument($client->id, 'documents/receipt.pdf');
        $document->doc_type = 'dibp_receipt';
        $document->save();

        $this->actingAs($this->staffWithPermission, 'admin')
            ->post(route('clients.documents.bulkDownload'), [
                'client_id' => $client->id,
                'document_ids' => [$document->id],
            ])
            ->assertForbidden();
    }

    #[Test]
    public function missing_files_return_not_found(): void
    {
        Storage::fake('public');
        Storage::fake('s3');
        $client = $this->makeClient();
        $document = $this->makePersonalDocument($client->id, 'documents/missing-bulk.pdf');

        $this->actingAs($this->staffWithPermission, 'admin')
            ->post(route('clients.documents.bulkDownload'), [
                'client_id' => $client->id,
                'document_ids' => [$document->id],
            ])
            ->assertNotFound();
    }

    private function makeClient(string $first = 'Test', string $last = 'Client', ?int $ownerStaffId = null): Admin
    {
        $client = new Admin;
        $client->first_name = $first;
        $client->last_name = $last;
        $client->type = 'client';
        $client->client_id = 'C'.uniqid();
        $client->user_id = $ownerStaffId;
        $client->save();

        return $client;
    }

    private function makePersonalDocument(int $clientId, string $myfile): Document
    {
        $document = new Document;
        $document->file_name = pathinfo($myfile, PATHINFO_FILENAME) ?: 'file';
        $document->filetype = 'pdf';
        $document->myfile = $myfile;
        $document->myfile_key = basename($myfile);
        $document->client_id = $clientId;
        $document->doc_type = 'personal';
        $document->type = 'client';
        $document->folder_name = '1';
        $document->status = 'draft';
        $document->created_by = $this->staffWithPermission->id;
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

        if (! Schema::hasTable('user_roles')) {
            Schema::create('user_roles', function (Blueprint $table) {
                $table->unsignedBigInteger('id')->primary();
                $table->string('name')->nullable();
                $table->text('description')->nullable();
                $table->text('module_access')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('admins')) {
            Schema::create('admins', function (Blueprint $table) {
                $table->id();
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('type')->nullable();
                $table->string('client_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedTinyInteger('is_company')->default(0);
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
                $table->string('type')->nullable();
                $table->string('folder_name')->nullable();
                $table->string('not_used_doc')->nullable();
                $table->timestamps();
            });
        }
    }
}
