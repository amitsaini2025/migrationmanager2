<?php

namespace Tests\Unit;

use App\Models\Document;
use App\Models\Signer;
use App\Services\EmailConfigService;
use App\Services\SignatureService;
use App\Services\SignedDocumentS3PathResolver;
use App\Services\SystemEmailLogService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SignatureServiceMultiSignerTest extends TestCase
{
    private SignatureService $signatures;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ensureSchema();
        Mail::fake();

        $this->signatures = new SignatureService(
            new EmailConfigService,
            app(SystemEmailLogService::class),
        );
    }

    #[Test]
    public function emailing_one_of_two_signers_leaves_the_other_unsent(): void
    {
        $document = $this->document();
        $husband = $this->signer($document, 'husband@example.com', 'Husband');
        $wife = $this->signer($document, 'wife@example.com', 'Wife');
        $this->assignBox($document, $husband);
        $this->assignBox($document, $wife);

        $result = $this->signatures->sendPendingSigningEmails($document, $husband->id);

        $this->assertSame(1, $result['sent']);
        $this->assertSame([], $result['skipped']);
        $this->assertNotNull($husband->fresh()->sent_at);
        $this->assertNull($wife->fresh()->sent_at);
        $this->assertSame('sent', $document->fresh()->status);
    }

    #[Test]
    public function send_all_emails_every_pending_signer(): void
    {
        $document = $this->document();
        $first = $this->signer($document, 'first@example.com', 'First Client');
        $second = $this->signer($document, 'second@example.com', 'Second Client');
        $this->assignBox($document, $first);
        $this->assignBox($document, $second);

        $result = $this->signatures->sendPendingSigningEmails($document);

        $this->assertSame(2, $result['sent']);
        $this->assertSame([], $result['errors']);
        $this->assertSame([], $result['skipped']);
        $this->assertNotNull($first->fresh()->sent_at);
        $this->assertNotNull($second->fresh()->sent_at);
        $this->assertSame('sent', $document->fresh()->status);
        $this->assertCount(2, $result['emailed']);
    }

    #[Test]
    public function send_all_continues_when_one_signer_still_has_a_placeholder_email(): void
    {
        $document = $this->document();
        $real = $this->signer($document, 'real@example.com', 'Real Client');
        $placeholder = $this->signer($document, 'client-123@lead.internal', 'Placeholder Client');
        $this->assignBox($document, $real);
        $this->assignBox($document, $placeholder);

        $result = $this->signatures->sendPendingSigningEmails($document);

        $this->assertSame(1, $result['sent']);
        $this->assertNotNull($real->fresh()->sent_at);
        $this->assertNull($placeholder->fresh()->sent_at);
        $this->assertNotEmpty($result['errors']);
        $this->assertStringContainsString('placeholder', strtolower($result['errors'][0]));
    }

    #[Test]
    public function first_submit_leaves_the_document_sent_and_the_second_sets_signed(): void
    {
        $document = $this->document(['status' => 'sent']);
        $husband = $this->signer($document, 'husband@example.com', 'Husband', ['sent_at' => now()]);
        $wife = $this->signer($document, 'wife@example.com', 'Wife', ['sent_at' => now()]);

        $this->assertSame('sent', $this->signatures->recordSignerSigned($husband));
        $this->assertSame('sent', $document->fresh()->status);
        $this->assertSame('pending', $wife->fresh()->status);

        $this->assertSame('signed', $this->signatures->recordSignerSigned($wife->fresh()));
        $this->assertSame('signed', $document->fresh()->status);
    }

    #[Test]
    public function a_local_signed_pdf_url_is_the_file_the_next_signer_opens(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('signed/9_signed.pdf', '%PDF-1.4 test');

        $document = new Document;
        $document->id = 9;
        $document->signed_doc_link = 'http://localhost/storage/signed/9_signed.pdf';

        $path = SignedDocumentS3PathResolver::copySignedPdfToLocalPath($document);

        $this->assertNotNull($path);
        $this->assertSame('%PDF-1.4 test', file_get_contents($path));
    }

    #[Test]
    public function checklist_reminder_sends_and_leaves_the_signing_email_available(): void
    {
        $document = $this->document([
            'doc_type' => 'agreement',
            'status' => 'sent',
            'client_id' => 10,
            'client_matter_id' => 20,
            'signature_doc_link' => json_encode([['url' => 'https://example.test/sign']]),
        ]);
        $signer = $this->signer($document, 'signer@example.com', 'Signer');
        $this->assignBox($document, $signer);

        $this->assertTrue($this->signatures->remind($signer));

        $signer->refresh();
        $this->assertSame(1, (int) $signer->reminder_count);
        $this->assertNull($signer->sent_at);

        $result = $this->signatures->sendPendingSigningEmails($document->fresh(), $signer->id);

        $this->assertSame(1, $result['sent']);
        $this->assertNotNull($signer->fresh()->sent_at);
    }

    #[Test]
    public function reminder_before_the_signing_email_stays_blocked_for_other_documents(): void
    {
        $document = $this->document([
            'doc_type' => 'visa',
            'status' => 'placed',
            'client_id' => 10,
            'client_matter_id' => 20,
            'signature_doc_link' => json_encode([['url' => 'https://example.test/sign']]),
        ]);
        $signer = $this->signer($document, 'visa@example.com', 'Visa Signer');

        $this->assertFalse($this->signatures->remind($signer));
        $this->assertSame(0, (int) $signer->fresh()->reminder_count);
        $this->assertNull($signer->fresh()->sent_at);
    }

    #[Test]
    public function reminder_still_sends_after_the_signing_email(): void
    {
        $document = $this->document([
            'doc_type' => 'visa',
            'status' => 'sent',
            'client_id' => 3,
        ]);
        $signer = $this->signer($document, 'sent@example.com', 'Sent Signer', ['sent_at' => now()]);

        $this->assertTrue($this->signatures->remind($signer));
        $this->assertSame(1, (int) $signer->fresh()->reminder_count);
        $this->assertNotNull($signer->fresh()->sent_at);
    }

    #[Test]
    public function a_single_signer_finishes_the_document_on_submit(): void
    {
        $document = $this->document(['status' => 'sent']);
        $signer = $this->signer($document, 'only@example.com', 'Only Signer', ['sent_at' => now()]);

        $this->assertSame('signed', $this->signatures->recordSignerSigned($signer));
        $this->assertSame('signed', $document->fresh()->status);
        $this->assertSame('signed', $signer->fresh()->status);
    }

    private function document(array $overrides = []): Document
    {
        return Document::query()->create(array_merge([
            'file_name' => 'agreement.pdf',
            'myfile' => 'documents/agreement.pdf',
            'status' => 'draft',
            'doc_type' => 'visa',
        ], $overrides));
    }

    private function signer(Document $document, string $email, string $name, array $overrides = []): Signer
    {
        return $document->signers()->create(array_merge([
            'email' => $email,
            'name' => $name,
            'token' => bin2hex(random_bytes(32)),
            'status' => 'pending',
            'reminder_count' => 0,
        ], $overrides));
    }

    private function assignBox(Document $document, Signer $signer): void
    {
        $document->signatureFields()->create([
            'signer_id' => $signer->id,
            'page_number' => 1,
            'x_percent' => 0.1,
            'y_percent' => 0.1,
            'width_percent' => 0.2,
            'height_percent' => 0.05,
        ]);
    }

    private function assignEveryBox(Document $document, Signer $signer): void
    {
        $this->assignBox($document, $signer);
    }

    private function ensureSchema(): void
    {
        if (! Schema::hasTable('documents')) {
            Schema::create('documents', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id')->nullable();
                $table->string('file_name')->nullable();
                $table->string('myfile')->nullable();
                $table->string('status')->nullable();
                $table->string('doc_type')->nullable();
                $table->text('signed_doc_link')->nullable();
                $table->text('signature_doc_link')->nullable();
                $table->timestamps();
            });
        }

        foreach ([
            'client_id' => fn (Blueprint $table) => $table->unsignedBigInteger('client_id')->nullable(),
            'client_matter_id' => fn (Blueprint $table) => $table->unsignedBigInteger('client_matter_id')->nullable(),
            'file_name' => fn (Blueprint $table) => $table->string('file_name')->nullable(),
            'myfile' => fn (Blueprint $table) => $table->string('myfile')->nullable(),
            'status' => fn (Blueprint $table) => $table->string('status')->nullable(),
            'doc_type' => fn (Blueprint $table) => $table->string('doc_type')->nullable(),
            'signed_doc_link' => fn (Blueprint $table) => $table->text('signed_doc_link')->nullable(),
            'signature_doc_link' => fn (Blueprint $table) => $table->text('signature_doc_link')->nullable(),
        ] as $column => $definition) {
            $this->ensureColumn('documents', $column, $definition);
        }

        if (! Schema::hasTable('signers')) {
            Schema::create('signers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('document_id')->nullable();
                $table->string('email')->nullable();
                $table->string('name')->nullable();
                $table->string('token')->nullable();
                $table->string('status')->nullable();
                $table->timestamp('sent_at')->nullable();
                $table->timestamp('signed_at')->nullable();
                $table->unsignedInteger('reminder_count')->default(0);
                $table->string('email_template')->nullable();
                $table->string('email_subject')->nullable();
                $table->text('email_message')->nullable();
                $table->string('from_email')->nullable();
                $table->timestamps();
            });
        }

        foreach ([
            'document_id' => fn (Blueprint $table) => $table->unsignedBigInteger('document_id')->nullable(),
            'email' => fn (Blueprint $table) => $table->string('email')->nullable(),
            'name' => fn (Blueprint $table) => $table->string('name')->nullable(),
            'token' => fn (Blueprint $table) => $table->string('token')->nullable(),
            'status' => fn (Blueprint $table) => $table->string('status')->nullable(),
            'sent_at' => fn (Blueprint $table) => $table->timestamp('sent_at')->nullable(),
            'signed_at' => fn (Blueprint $table) => $table->timestamp('signed_at')->nullable(),
            'last_reminder_sent_at' => fn (Blueprint $table) => $table->timestamp('last_reminder_sent_at')->nullable(),
            'reminder_count' => fn (Blueprint $table) => $table->unsignedInteger('reminder_count')->default(0),
            'email_template' => fn (Blueprint $table) => $table->string('email_template')->nullable(),
            'email_subject' => fn (Blueprint $table) => $table->string('email_subject')->nullable(),
            'email_message' => fn (Blueprint $table) => $table->text('email_message')->nullable(),
            'from_email' => fn (Blueprint $table) => $table->string('from_email')->nullable(),
        ] as $column => $definition) {
            $this->ensureColumn('signers', $column, $definition);
        }

        if (! Schema::hasTable('signature_fields')) {
            Schema::create('signature_fields', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('document_id')->nullable();
                $table->unsignedBigInteger('signer_id')->nullable();
                $table->unsignedInteger('page_number')->nullable();
                $table->float('x_percent')->nullable();
                $table->float('y_percent')->nullable();
                $table->float('width_percent')->nullable();
                $table->float('height_percent')->nullable();
                $table->timestamps();
            });
        }

        foreach ([
            'document_id' => fn (Blueprint $table) => $table->unsignedBigInteger('document_id')->nullable(),
            'signer_id' => fn (Blueprint $table) => $table->unsignedBigInteger('signer_id')->nullable(),
            'page_number' => fn (Blueprint $table) => $table->unsignedInteger('page_number')->nullable(),
            'x_percent' => fn (Blueprint $table) => $table->float('x_percent')->nullable(),
            'y_percent' => fn (Blueprint $table) => $table->float('y_percent')->nullable(),
            'width_percent' => fn (Blueprint $table) => $table->float('width_percent')->nullable(),
            'height_percent' => fn (Blueprint $table) => $table->float('height_percent')->nullable(),
        ] as $column => $definition) {
            $this->ensureColumn('signature_fields', $column, $definition);
        }

        if (! Schema::hasTable('signature_activities')) {
            Schema::create('signature_activities', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('document_id')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->string('action_type')->nullable();
                $table->text('note')->nullable();
                $table->text('metadata')->nullable();
                $table->timestamps();
            });
        }

        foreach ([
            'document_id' => fn (Blueprint $table) => $table->unsignedBigInteger('document_id')->nullable(),
            'created_by' => fn (Blueprint $table) => $table->unsignedBigInteger('created_by')->nullable(),
            'action_type' => fn (Blueprint $table) => $table->string('action_type')->nullable(),
            'note' => fn (Blueprint $table) => $table->text('note')->nullable(),
            'metadata' => fn (Blueprint $table) => $table->text('metadata')->nullable(),
        ] as $column => $definition) {
            $this->ensureColumn('signature_activities', $column, $definition);
        }

        if (! Schema::hasTable('email_logs')) {
            Schema::create('email_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('from_mail')->nullable();
                $table->string('to_mail')->nullable();
                $table->string('subject')->nullable();
                $table->text('message')->nullable();
                $table->string('type')->nullable();
                $table->integer('mail_type')->nullable();
                $table->unsignedBigInteger('client_id')->nullable();
                $table->unsignedBigInteger('client_matter_id')->nullable();
                $table->string('conversion_type')->nullable();
                $table->string('system_email_category')->nullable();
                $table->string('delivery_status')->nullable();
                $table->text('status_reason')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('emails')) {
            Schema::create('emails', function (Blueprint $table) {
                $table->id();
                $table->string('email')->nullable();
                $table->boolean('status')->default(true);
                $table->string('display_name')->nullable();
                $table->text('email_signature')->nullable();
                $table->timestamps();
            });
        }
    }

    private function ensureColumn(string $table, string $column, callable $definition): void
    {
        if (Schema::hasTable($table) && ! Schema::hasColumn($table, $column)) {
            Schema::table($table, function (Blueprint $blueprint) use ($definition) {
                $definition($blueprint);
            });
        }
    }
}
