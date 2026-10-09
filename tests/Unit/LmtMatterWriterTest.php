<?php

namespace Tests\Unit;

use App\Models\Admin;
use App\Models\ClientMatter;
use App\Models\Document;
use App\Models\NominationDocumentType;
use App\Support\LmtAdvertisementFiles;
use App\Support\LmtMatterWriter;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LmtMatterWriterTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->createSchema();
    }

    #[Test]
    public function save_writes_lmt_fields_onto_the_active_company_matter(): void
    {
        $company = new Admin;
        $company->id = 15;
        $company->is_company = 1;
        $company->type = 'client';
        $company->exists = true;

        $matter = new ClientMatter;
        $matter->client_id = 15;
        $matter->matter_status = 1;
        $matter->save();

        $result = (new LmtMatterWriter)->save($company, [
            'client_matter_id' => $matter->id,
            'lmt_required' => '1',
            'lmt_start_date' => '2026-10-01',
            'lmt_end_date' => '2026-10-29',
            'lmt_notes' => 'Seek and Workforce Australia',
            'lmt_password' => 'ads-pass',
        ]);

        $matter->refresh();
        $this->assertTrue($result['ok']);
        $this->assertTrue($matter->lmt_required);
        $this->assertSame('2026-10-01', $matter->lmt_start_date->format('Y-m-d'));
        $this->assertSame('2026-10-29', $matter->lmt_end_date->format('Y-m-d'));
        $this->assertSame('Seek and Workforce Australia', $matter->lmt_notes);
        $this->assertSame('ads-pass', $matter->lmt_password);
    }

    #[Test]
    public function save_rejects_an_end_date_before_the_start_date(): void
    {
        $company = new Admin;
        $company->id = 15;
        $company->is_company = 1;
        $company->type = 'client';
        $company->exists = true;

        $matter = new ClientMatter;
        $matter->client_id = 15;
        $matter->matter_status = 1;
        $matter->save();

        $result = (new LmtMatterWriter)->save($company, [
            'client_matter_id' => $matter->id,
            'lmt_required' => '1',
            'lmt_start_date' => '2026-10-20',
            'lmt_end_date' => '2026-10-01',
        ]);

        $this->assertFalse($result['ok']);
        $this->assertSame(422, $result['status']);
        $this->assertSame('LMT end date must be on or after the start date.', $result['message']);
    }

    #[Test]
    public function advertisement_upload_is_stored_in_the_matter_lmt_folder(): void
    {
        Storage::fake('s3');

        $company = new Admin;
        $company->forceFill([
            'id' => 15,
            'is_company' => 1,
            'type' => 'client',
            'client_id' => 'C15',
            'first_name' => 'Acme',
        ]);
        $company->exists = true;

        $matter = new ClientMatter;
        $matter->client_id = 15;
        $matter->matter_status = 1;
        $matter->save();

        $file = UploadedFile::fake()->create('Seek-ad.pdf', 20, 'application/pdf');
        (new LmtAdvertisementFiles)->store($company, $matter, [$file], 3);

        $folder = NominationDocumentType::query()
            ->where('client_id', 15)
            ->where('client_matter_id', $matter->id)
            ->where('title', 'LMT')
            ->first();
        $this->assertNotNull($folder);

        $document = Document::query()->where('client_matter_id', $matter->id)->first();
        $this->assertNotNull($document);
        $this->assertSame('nomination', $document->doc_type);
        $this->assertSame((string) $folder->id, (string) $document->folder_name);
        $this->assertSame('Seek-ad', $document->checklist);
        Storage::disk('s3')->assertExists('C15/nomination/'.$document->myfile_key);

        $listed = (new LmtAdvertisementFiles)->filesForMatters([(int) $matter->id]);
        $this->assertSame('Seek-ad', $listed[(int) $matter->id][0]['name']);
    }

    #[Test]
    public function advertisement_upload_rejects_a_file_that_did_not_upload(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'lmt');
        file_put_contents($path, 'pdf');
        $file = new UploadedFile($path, 'Seek-ad.pdf', 'application/pdf', UPLOAD_ERR_INI_SIZE, true);

        $errors = (new LmtAdvertisementFiles)->validateFiles([$file]);

        $this->assertNotSame([], $errors);
        $this->assertSame([], (new LmtAdvertisementFiles)->acceptedUploads([$file]));
        @unlink($path);
    }

    #[Test]
    public function advertisement_upload_refuses_a_company_without_a_client_reference(): void
    {
        Storage::fake('s3');

        $company = new Admin;
        $company->forceFill([
            'id' => 16,
            'is_company' => 1,
            'type' => 'client',
            'client_id' => ' ',
            'first_name' => 'Acme',
        ]);
        $company->exists = true;

        $matter = new ClientMatter;
        $matter->client_id = 16;
        $matter->matter_status = 1;
        $matter->save();

        $file = UploadedFile::fake()->create('Seek-ad.pdf', 20, 'application/pdf');

        try {
            (new LmtAdvertisementFiles)->store($company, $matter, [$file], 3);
            $this->fail('Expected a missing client reference to stop the upload.');
        } catch (\InvalidArgumentException $e) {
            $this->assertSame(0, Document::query()->count());
        }
    }

    private function createSchema(): void
    {
        Schema::dropIfExists('documents');
        Schema::dropIfExists('nomination_document_types');
        Schema::dropIfExists('companies');
        Schema::dropIfExists('client_matters');
        Schema::dropIfExists('admins');

        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('client_id')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('type')->nullable();
            $table->boolean('is_company')->nullable();
            $table->string('email')->nullable();
            $table->string('status')->nullable();
            $table->string('lead_status')->nullable();
            $table->timestamps();
        });

        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->string('company_name')->nullable();
            $table->timestamps();
        });

        Schema::create('client_matters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedTinyInteger('matter_status')->nullable();
            $table->boolean('lmt_required')->nullable();
            $table->date('lmt_start_date')->nullable();
            $table->date('lmt_end_date')->nullable();
            $table->text('lmt_notes')->nullable();
            $table->string('lmt_password')->nullable();
            $table->timestamps();
        });

        Schema::create('nomination_document_types', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->unsignedTinyInteger('status')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('client_matter_id')->nullable();
            $table->timestamps();
        });

        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('client_matter_id')->nullable();
            $table->string('type')->nullable();
            $table->string('doc_type')->nullable();
            $table->string('folder_name')->nullable();
            $table->string('checklist')->nullable();
            $table->string('file_name')->nullable();
            $table->string('filetype')->nullable();
            $table->string('myfile')->nullable();
            $table->string('myfile_key')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->unsignedTinyInteger('not_used_doc')->nullable();
            $table->timestamps();
        });
    }
}
