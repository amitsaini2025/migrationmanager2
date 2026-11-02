<?php

namespace Tests\Unit;

use App\Models\Document;
use App\Support\ClientDetailDocumentsTab;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class VisaSignedCompanionArchivesParentTest extends TestCase
{
    #[Test]
    public function archives_visa_parent_when_active_signed_companion_exists(): void
    {
        $this->createDocumentsSchema();

        $parentId = DB::table('documents')->insertGetId([
            'client_id' => 50,
            'user_id' => 1,
            'client_matter_id' => 9,
            'doc_type' => 'visa',
            'type' => 'client',
            'folder_name' => 'Forms',
            'file_name' => '956.pdf',
            'checklist' => '956 Form_ Agent',
            'status' => 'signed',
            'not_used_doc' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('documents')->insert([
            'client_id' => 50,
            'user_id' => 1,
            'client_matter_id' => 9,
            'doc_type' => 'visa',
            'type' => 'client',
            'folder_name' => 'Forms',
            'file_name' => '956_signed.pdf',
            'checklist' => '956 Form_ Agent_signed',
            'status' => 'signed',
            'not_used_doc' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $parent = Document::query()->findOrFail($parentId);
        Assert::assertTrue(ClientDetailDocumentsTab::archiveUnsignedParentAfterSignedCompanion($parent));
        Assert::assertTrue(ClientDetailDocumentsTab::isUnused($parent->fresh()));

        $byFolder = ClientDetailDocumentsTab::visaDocumentsByFolder(50);
        $forms = $byFolder->get('Forms', collect());
        Assert::assertCount(1, $forms);
        Assert::assertSame('956 Form_ Agent_signed', $forms->first()->checklist);
    }

    #[Test]
    public function does_not_archive_when_companion_missing(): void
    {
        $this->createDocumentsSchema();

        $parentId = DB::table('documents')->insertGetId([
            'client_id' => 51,
            'user_id' => 1,
            'client_matter_id' => 9,
            'doc_type' => 'visa',
            'type' => 'client',
            'folder_name' => 'Forms',
            'file_name' => '956.pdf',
            'checklist' => '956 Form_ Solo',
            'status' => 'signed',
            'not_used_doc' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $parent = Document::query()->findOrFail($parentId);
        Assert::assertFalse(ClientDetailDocumentsTab::archiveUnsignedParentAfterSignedCompanion($parent));
        Assert::assertFalse(ClientDetailDocumentsTab::isUnused($parent->fresh()));
    }

    #[Test]
    public function does_not_archive_agreement_or_personal_docs(): void
    {
        $this->createDocumentsSchema();

        $agreementId = DB::table('documents')->insertGetId([
            'client_id' => 52,
            'user_id' => 1,
            'client_matter_id' => 9,
            'doc_type' => 'agreement',
            'type' => 'client',
            'folder_name' => 'Agreements',
            'file_name' => 'ca.pdf',
            'checklist' => 'Cost Agreement',
            'status' => 'signed',
            'not_used_doc' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('documents')->insert([
            'client_id' => 52,
            'user_id' => 1,
            'client_matter_id' => 9,
            'doc_type' => 'agreement',
            'type' => 'client',
            'folder_name' => 'Agreements',
            'file_name' => 'ca_signed.pdf',
            'checklist' => 'Cost Agreement_signed',
            'status' => 'signed',
            'not_used_doc' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $agreement = Document::query()->findOrFail($agreementId);
        Assert::assertFalse(ClientDetailDocumentsTab::archiveUnsignedParentAfterSignedCompanion($agreement));
        Assert::assertFalse(ClientDetailDocumentsTab::isUnused($agreement->fresh()));
    }

    #[Test]
    public function does_not_archive_signed_companion_row_itself(): void
    {
        $this->createDocumentsSchema();

        $signedId = DB::table('documents')->insertGetId([
            'client_id' => 53,
            'user_id' => 1,
            'client_matter_id' => 9,
            'doc_type' => 'visa',
            'type' => 'client',
            'folder_name' => 'Forms',
            'file_name' => '956_signed.pdf',
            'checklist' => '956 Form_ Agent_signed',
            'status' => 'signed',
            'not_used_doc' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $signed = Document::query()->findOrFail($signedId);
        Assert::assertFalse(ClientDetailDocumentsTab::archiveUnsignedParentAfterSignedCompanion($signed));
        Assert::assertFalse(ClientDetailDocumentsTab::isUnused($signed->fresh()));
    }

    private function createDocumentsSchema(): void
    {
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
                $table->string('status')->nullable();
                $table->unsignedTinyInteger('not_used_doc')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('staff')) {
            Schema::create('staff', function (Blueprint $table) {
                $table->id();
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('email')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('signers')) {
            Schema::create('signers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('document_id')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('signature_activities')) {
            Schema::create('signature_activities', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('document_id')->nullable();
                $table->timestamps();
            });
        }
    }
}
