<?php

namespace Tests\Feature;

use App\Http\Middleware\TrackStaffCrmActivity;
use App\Models\Document;
use App\Models\Signer;
use App\Models\Staff;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ShowSignFormTest extends TestCase
{
    protected Staff $staff;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([TrackStaffCrmActivity::class]);

        $this->createSchema();

        $this->staff = Staff::create([
            'first_name' => 'Sign',
            'last_name' => 'Tester',
            'email' => 'sign-form@test.com',
            'password' => Hash::make('password'),
            'role' => 1,
            'status' => 1,
        ]);
    }

    #[Test]
    public function show_sign_form_route_is_registered(): void
    {
        $this->assertTrue(Route::has('documents.showSignForm'));
    }

    #[Test]
    public function matching_signer_email_does_not_query_missing_user_id_column(): void
    {
        $document = $this->makeDocument();
        $signer = $this->makeSigner($document, $this->staff->email, 'pending');

        $response = $this->actingAs($this->staff, 'admin')
            ->get(route('documents.showSignForm', $document->id));

        // Email lookup must succeed (opened_at is set before PDF loading). Missing PDF/media in this
        // fixture may redirect with a file error afterward — that is fine and proves no user_id SQL error.
        $this->assertNotNull($signer->fresh()->opened_at);
        $response->assertRedirect();
        $error = session('error');
        $this->assertIsString($error);
        $this->assertStringNotContainsString('user_id', $error);
        $this->assertStringNotContainsString('Undefined column', $error);
    }

    #[Test]
    public function non_matching_email_gets_invalid_signing_link(): void
    {
        $document = $this->makeDocument();
        $this->makeSigner($document, 'someone-else@example.com', 'pending');

        $response = $this->actingAs($this->staff, 'admin')
            ->get(route('documents.showSignForm', $document->id));

        $response->assertRedirect('/');
        $response->assertSessionHas('error', 'Invalid or expired signing link.');
    }

    #[Test]
    public function already_signed_signer_gets_invalid_signing_link(): void
    {
        $document = $this->makeDocument();
        $this->makeSigner($document, $this->staff->email, 'signed');

        $response = $this->actingAs($this->staff, 'admin')
            ->get(route('documents.showSignForm', $document->id));

        $response->assertRedirect('/');
        $response->assertSessionHas('error', 'Invalid or expired signing link.');
    }

    private function makeDocument(): Document
    {
        $document = new Document;
        $document->file_name = 'sign-form.pdf';
        $document->filetype = 'pdf';
        $document->myfile = 'documents/missing-sign-form.pdf';
        $document->status = 'sent';
        $document->created_by = $this->staff->id;
        $document->save();

        return $document;
    }

    private function makeSigner(Document $document, string $email, string $status): Signer
    {
        $signer = new Signer;
        $signer->document_id = $document->id;
        $signer->name = 'Signer';
        $signer->email = $email;
        $signer->token = Str::random(64);
        $signer->status = $status;
        $signer->signed_at = $status === 'signed' ? now() : null;
        $signer->save();

        return $signer;
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

        if (! Schema::hasTable('signers')) {
            Schema::create('signers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('document_id');
                $table->string('name')->nullable();
                $table->string('email')->nullable();
                $table->string('token')->nullable();
                $table->string('status', 20)->nullable();
                $table->timestamp('signed_at')->nullable();
                $table->timestamp('opened_at')->nullable();
                $table->timestamp('cancelled_at')->nullable();
                $table->timestamp('sent_at')->nullable();
                $table->timestamp('last_reminder_sent_at')->nullable();
                $table->unsignedInteger('reminder_count')->nullable();
                $table->string('email_template')->nullable();
                $table->string('email_subject')->nullable();
                $table->text('email_message')->nullable();
                $table->string('from_email')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('signature_fields')) {
            Schema::create('signature_fields', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('document_id')->nullable();
                $table->unsignedBigInteger('signer_id')->nullable();
                $table->string('type')->nullable();
                $table->integer('page')->nullable();
                $table->float('x')->nullable();
                $table->float('y')->nullable();
                $table->float('width')->nullable();
                $table->float('height')->nullable();
                $table->timestamps();
            });
        }
    }
}
