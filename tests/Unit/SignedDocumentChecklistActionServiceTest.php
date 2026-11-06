<?php

namespace Tests\Unit;

use App\Models\Admin;
use App\Models\ClientMatter;
use App\Models\Document;
use App\Models\Note;
use App\Models\Staff;
use App\Services\SignedDocumentChecklistActionService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SignedDocumentChecklistActionServiceTest extends TestCase
{
    private SignedDocumentChecklistActionService $actions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ensureSchema();
        $this->actions = new SignedDocumentChecklistActionService;
    }

    #[Test]
    public function a_fully_signed_service_agreement_is_listed_once_for_both_assignees(): void
    {
        $client = $this->client('AVNE2503771');
        $responsible = $this->staff('Aman', 'Kaur');
        $assisting = $this->staff('Rupa', 'Kumar');
        $matter = $this->matter($client, 'JRP_1', $responsible->id, $assisting->id);
        $document = $this->document($client, $matter, [
            'doc_type' => 'agreement',
            'status' => 'signed',
            'checklist' => 'Cost Agreement',
            'file_name' => 'Service_Agreement.pdf',
        ]);

        $this->actions->record($document);
        $this->actions->record($document->fresh());

        $notes = Note::query()->orderBy('assigned_to')->get();

        $this->assertCount(2, $notes);
        $this->assertSame(
            ['AVNE2503771-JRP_1 signed the service agreement', 'AVNE2503771-JRP_1 signed the service agreement'],
            $notes->pluck('description')->all()
        );
        $this->assertSame([$responsible->id, $assisting->id], $notes->pluck('assigned_to')->map(fn ($id) => (int) $id)->all());
        $this->assertSame(['Checklist', 'Checklist'], $notes->pluck('task_group')->all());
        $this->assertSame(['signed-doc-'.$document->id, 'signed-doc-'.$document->id], $notes->pluck('unique_group_id')->all());
        $this->assertTrue($notes->every(fn (Note $note): bool => (int) $note->is_action === 1
            && (string) $note->status === '0'
            && (string) $note->type === 'client'
            && (int) $note->client_id === $client->id
            && (int) $note->matter_id === $matter->id
            && (int) $note->user_id === $assisting->id));
    }

    #[Test]
    public function any_other_signed_document_uses_its_own_name(): void
    {
        $client = $this->client('AVNE2503771');
        $responsible = $this->staff('Aman', 'Kaur');
        $matter = $this->matter($client, 'JRP_1', $responsible->id, null);
        $document = $this->document($client, $matter, [
            'doc_type' => 'visa',
            'status' => 'signed',
            'checklist' => 'Form 80',
        ]);

        $this->actions->record($document);

        $note = Note::query()->first();

        $this->assertNotNull($note);
        $this->assertSame('AVNE2503771-JRP_1 signed Form 80', $note->description);
        $this->assertSame('Checklist', $note->task_group);
        $this->assertSame($responsible->id, (int) $note->assigned_to);
        $this->assertSame($responsible->id, (int) $note->user_id);
    }

    #[Test]
    public function a_document_that_is_not_fully_signed_does_not_add_a_row(): void
    {
        $client = $this->client('AVNE2503771');
        $responsible = $this->staff('Aman', 'Kaur');
        $matter = $this->matter($client, 'JRP_1', $responsible->id, null);
        $document = $this->document($client, $matter, [
            'doc_type' => 'agreement',
            'status' => 'sent',
        ]);

        $this->actions->record($document);

        $this->assertSame(0, Note::query()->count());
    }

    #[Test]
    public function a_signed_document_without_a_client_does_not_add_a_row(): void
    {
        $responsible = $this->staff('Aman', 'Kaur');
        $document = new Document;
        $document->status = 'signed';
        $document->doc_type = 'agreement';
        $document->user_id = $responsible->id;
        $document->save();

        $this->actions->record($document->fresh());

        $this->assertSame(0, Note::query()->count());
    }

    private function client(string $reference): Admin
    {
        return Admin::query()->create([
            'first_name' => 'John',
            'last_name' => 'Client',
            'email' => $reference.'@example.com',
            'client_id' => $reference,
        ]);
    }

    private function staff(string $firstName, string $lastName): Staff
    {
        return Staff::query()->create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => strtolower($firstName).'-'.uniqid().'@example.com',
            'password' => 'secret',
        ]);
    }

    private function matter(Admin $client, string $matterNo, int $responsibleId, ?int $assistingId): ClientMatter
    {
        return ClientMatter::query()->create([
            'client_id' => $client->id,
            'client_unique_matter_no' => $matterNo,
            'sel_person_responsible' => $responsibleId,
            'sel_person_assisting' => $assistingId,
        ]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function document(Admin $client, ClientMatter $matter, array $overrides): Document
    {
        return Document::query()->create(array_merge([
            'client_id' => $client->id,
            'client_matter_id' => $matter->id,
            'user_id' => $matter->sel_person_responsible,
            'file_name' => 'document.pdf',
            'doc_type' => 'visa',
            'status' => 'signed',
        ], $overrides));
    }

    private function ensureSchema(): void
    {
        if (! Schema::hasTable('admins')) {
            Schema::create('admins', function (Blueprint $table) {
                $table->id();
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('email')->nullable();
                $table->string('client_id')->nullable();
                $table->string('password')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('staff')) {
            Schema::create('staff', function (Blueprint $table) {
                $table->id();
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('email')->nullable();
                $table->string('password')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('client_matters')) {
            Schema::create('client_matters', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id')->nullable();
                $table->string('client_unique_matter_no')->nullable();
                $table->unsignedBigInteger('sel_person_responsible')->nullable();
                $table->unsignedBigInteger('sel_person_assisting')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('documents')) {
            Schema::create('documents', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id')->nullable();
                $table->unsignedBigInteger('client_matter_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->string('file_name')->nullable();
                $table->string('doc_type')->nullable();
                $table->string('checklist')->nullable();
                $table->string('status')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('notes')) {
            Schema::create('notes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('client_id')->nullable();
                $table->unsignedBigInteger('matter_id')->nullable();
                $table->unsignedBigInteger('assigned_to')->nullable();
                $table->text('description')->nullable();
                $table->date('action_date')->nullable();
                $table->string('task_group')->nullable();
                $table->string('type')->nullable();
                $table->unsignedTinyInteger('is_action')->default(0);
                $table->string('status')->nullable();
                $table->unsignedTinyInteger('pin')->default(0);
                $table->string('unique_group_id')->nullable();
                $table->timestamps();
            });
        }
    }
}
