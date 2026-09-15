<?php

namespace Tests\Unit;

use App\Http\Middleware\TrackStaffCrmActivity;
use App\Http\Middleware\VerifyCsrfToken;
use App\Http\Requests\StoreDibpReceiptUnusedRequest;
use App\Models\Document;
use App\Models\Staff;
use App\Support\ClientDetailDocumentsTab;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DibpReceiptUnusedTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            VerifyCsrfToken::class,
            TrackStaffCrmActivity::class,
        ]);

        $this->createSchema();
    }

    #[Test]
    public function unused_request_does_not_accept_visa_or_personal_doc_type(): void
    {
        $rules = (new StoreDibpReceiptUnusedRequest)->rules();

        Assert::assertArrayHasKey('clientid', $rules);
        Assert::assertArrayHasKey('fileid', $rules);
        Assert::assertArrayNotHasKey('doctype', $rules);
        Assert::assertArrayNotHasKey('doc_type', $rules);
        Assert::assertArrayNotHasKey('folder_name', $rules);
    }

    #[Test]
    public function unused_query_returns_only_dibp_receipts_and_not_used_tab_excludes_them(): void
    {
        $this->seedClient(20);

        $now = now();
        DB::table('documents')->insert([
            [
                'client_id' => 20,
                'client_matter_id' => null,
                'user_id' => null,
                'doc_type' => ClientDetailDocumentsTab::DIBP_RECEIPT_DOC_TYPE,
                'type' => 'client',
                'folder_name' => ClientDetailDocumentsTab::DIBP_RECEIPT_FOLDER_NAME,
                'file_name' => 'active.pdf',
                'checklist' => 'Active',
                'not_used_doc' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'client_id' => 20,
                'client_matter_id' => null,
                'user_id' => null,
                'doc_type' => ClientDetailDocumentsTab::DIBP_RECEIPT_DOC_TYPE,
                'type' => 'client',
                'folder_name' => ClientDetailDocumentsTab::DIBP_RECEIPT_FOLDER_NAME,
                'file_name' => 'unused-receipt.pdf',
                'checklist' => 'Unused receipt',
                'not_used_doc' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'client_id' => 20,
                'client_matter_id' => null,
                'user_id' => null,
                'doc_type' => 'visa',
                'type' => 'client',
                'folder_name' => '1',
                'file_name' => 'unused-visa.pdf',
                'checklist' => 'Unused visa',
                'not_used_doc' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'client_id' => 20,
                'client_matter_id' => null,
                'user_id' => null,
                'doc_type' => 'personal',
                'type' => 'client',
                'folder_name' => '1',
                'file_name' => 'unused-personal.pdf',
                'checklist' => 'Unused personal',
                'not_used_doc' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        $unusedReceipts = ClientDetailDocumentsTab::unusedDibpReceiptDocuments(20, null);
        Assert::assertCount(1, $unusedReceipts);
        Assert::assertSame('unused-receipt.pdf', $unusedReceipts->first()->file_name);
        Assert::assertSame(ClientDetailDocumentsTab::DIBP_RECEIPT_DOC_TYPE, $unusedReceipts->first()->doc_type);

        $active = ClientDetailDocumentsTab::dibpReceiptDocuments(20, null);
        Assert::assertCount(1, $active);
        Assert::assertSame('active.pdf', $active->first()->file_name);

        $notUsedTab = ClientDetailDocumentsTab::notUsedDocuments(20);
        Assert::assertCount(2, $notUsedTab);
        Assert::assertTrue($notUsedTab->every(
            fn (Document $document): bool => in_array($document->doc_type, ['visa', 'personal', 'nomination'], true)
        ));
        Assert::assertFalse($notUsedTab->contains(
            fn (Document $document): bool => $document->doc_type === ClientDetailDocumentsTab::DIBP_RECEIPT_DOC_TYPE
        ));
    }

    #[Test]
    public function unused_endpoints_mark_restore_and_delete_receipts_and_reject_visa_rows(): void
    {
        Storage::fake('s3');
        $this->seedClient(20);

        $staff = Staff::query()->create([
            'first_name' => 'Dibp',
            'last_name' => 'Unused',
            'email' => 'dibp-unused@test.com',
            'password' => Hash::make('password'),
            'role' => 1,
            'status' => 1,
        ]);

        $receipt = ClientDetailDocumentsTab::addChecklist(20, (int) $staff->id, 'Lodgement', null);
        Storage::disk('s3')->put('C20/dibp_receipt/Test_Lodgement.pdf', 'receipt-bytes');
        $receipt->file_name = 'Test_Lodgement';
        $receipt->filetype = 'pdf';
        $receipt->myfile_key = 'Test_Lodgement.pdf';
        $receipt->myfile = Storage::disk('s3')->url('C20/dibp_receipt/Test_Lodgement.pdf');
        $receipt->save();

        $empty = ClientDetailDocumentsTab::addChecklist(20, (int) $staff->id, 'Empty', null);

        $visa = Document::query()->create([
            'client_id' => 20,
            'user_id' => $staff->id,
            'doc_type' => 'visa',
            'type' => 'client',
            'folder_name' => '1',
            'checklist' => 'Visa form',
            'file_name' => 'Visa_form',
            'filetype' => 'pdf',
            'myfile_key' => 'Visa_form.pdf',
            'not_used_doc' => null,
        ]);

        $markedEmpty = $this->actingAs($staff, 'admin')
            ->postJson('/documents/mark-dibp-receipt-unused', [
                'clientid' => 20,
                'fileid' => $empty->id,
                'doctype' => 'visa',
            ]);
        $markedEmpty->assertStatus(422);
        $empty->refresh();
        Assert::assertNull($empty->not_used_doc);

        $markedVisa = $this->actingAs($staff, 'admin')
            ->postJson('/documents/mark-dibp-receipt-unused', [
                'clientid' => 20,
                'fileid' => $visa->id,
            ]);
        $markedVisa->assertStatus(422);
        $visa->refresh();
        Assert::assertSame('visa', $visa->doc_type);
        Assert::assertNull($visa->not_used_doc);

        $marked = $this->actingAs($staff, 'admin')
            ->postJson('/documents/mark-dibp-receipt-unused', [
                'clientid' => 20,
                'fileid' => $receipt->id,
                'doctype' => 'visa',
            ]);
        $marked->assertOk();
        Assert::assertTrue($marked->json('status'));
        Assert::assertTrue($marked->json('document.not_used'));
        $receipt->refresh();
        Assert::assertSame(1, (int) $receipt->not_used_doc);
        Assert::assertSame(ClientDetailDocumentsTab::DIBP_RECEIPT_DOC_TYPE, $receipt->doc_type);
        Assert::assertFalse(ClientDetailDocumentsTab::dibpReceiptDocuments(20, null)->contains('id', $receipt->id));
        Assert::assertTrue(ClientDetailDocumentsTab::unusedDibpReceiptDocuments(20, null)->contains('id', $receipt->id));
        Assert::assertCount(0, ClientDetailDocumentsTab::notUsedDocuments(20));

        $restoredVisa = $this->actingAs($staff, 'admin')
            ->postJson('/documents/restore-dibp-receipt', [
                'clientid' => 20,
                'fileid' => $visa->id,
            ]);
        $restoredVisa->assertStatus(422);

        $restored = $this->actingAs($staff, 'admin')
            ->postJson('/documents/restore-dibp-receipt', [
                'clientid' => 20,
                'fileid' => $receipt->id,
            ]);
        $restored->assertOk();
        Assert::assertFalse((bool) $restored->json('document.not_used'));
        $receipt->refresh();
        Assert::assertNull($receipt->not_used_doc);
        Assert::assertTrue(ClientDetailDocumentsTab::dibpReceiptDocuments(20, null)->contains('id', $receipt->id));
        Assert::assertTrue(ClientDetailDocumentsTab::unusedDibpReceiptDocuments(20, null)->isEmpty());

        $deleteActive = $this->actingAs($staff, 'admin')
            ->postJson('/documents/delete-dibp-receipt-unused', [
                'clientid' => 20,
                'fileid' => $receipt->id,
            ]);
        $deleteActive->assertStatus(422);
        Assert::assertNotNull(Document::query()->find($receipt->id));

        $this->actingAs($staff, 'admin')
            ->postJson('/documents/mark-dibp-receipt-unused', [
                'clientid' => 20,
                'fileid' => $receipt->id,
            ])
            ->assertOk();

        $deletedVisa = $this->actingAs($staff, 'admin')
            ->postJson('/documents/delete-dibp-receipt-unused', [
                'clientid' => 20,
                'fileid' => $visa->id,
            ]);
        $deletedVisa->assertStatus(422);
        Assert::assertNotNull(Document::query()->find($visa->id));

        $deleted = $this->actingAs($staff, 'admin')
            ->postJson('/documents/delete-dibp-receipt-unused', [
                'clientid' => 20,
                'fileid' => $receipt->id,
            ]);
        $deleted->assertOk();
        Assert::assertNull(Document::query()->find($receipt->id));
        Storage::disk('s3')->assertMissing('C20/dibp_receipt/Test_Lodgement.pdf');
        Assert::assertSame('visa', Document::query()->find($visa->id)?->doc_type);
    }

    #[Test]
    public function unused_routes_stay_off_visa_personal_not_used_endpoints(): void
    {
        $router = app('router');

        $mark = $router->getRoutes()->match(Request::create('/documents/mark-dibp-receipt-unused', 'POST'));
        Assert::assertSame('clients.documents.markDibpReceiptUnused', $mark->getName());
        Assert::assertSame('markDibpReceiptUnused', $mark->getActionMethod());

        $restore = $router->getRoutes()->match(Request::create('/documents/restore-dibp-receipt', 'POST'));
        Assert::assertSame('clients.documents.restoreDibpReceipt', $restore->getName());
        Assert::assertSame('restoreDibpReceipt', $restore->getActionMethod());

        $delete = $router->getRoutes()->match(Request::create('/documents/delete-dibp-receipt-unused', 'POST'));
        Assert::assertSame('clients.documents.deleteDibpReceiptUnused', $delete->getName());
        Assert::assertSame('deleteDibpReceiptUnused', $delete->getActionMethod());

        $visaUnused = $router->getRoutes()->match(Request::create('/documents/not-used', 'POST'));
        Assert::assertSame('clients.documents.notuseddoc', $visaUnused->getName());
        Assert::assertSame('notuseddoc', $visaUnused->getActionMethod());

        $back = $router->getRoutes()->match(Request::create('/documents/back-to-doc', 'POST'));
        Assert::assertSame('clients.documents.backtodoc', $back->getName());
        Assert::assertSame('backtodoc', $back->getActionMethod());

        $visaDelete = $router->getRoutes()->match(Request::create('/documents/delete', 'GET'));
        Assert::assertSame('clients.documents.deletedocs', $visaDelete->getName());
        Assert::assertSame('deletedocs', $visaDelete->getActionMethod());
    }

    #[Test]
    public function unused_ui_is_isolated_from_visa_personal_not_used(): void
    {
        $blade = file_get_contents($this->projectPath('resources/views/crm/clients/tabs/account.blade.php'));
        Assert::assertNotFalse($blade);
        Assert::assertStringNotContainsString('$dibpReceiptsShowUnusedUi', $blade);
        Assert::assertStringNotContainsString('empty($fetchedData->is_company)', $blade);
        Assert::assertStringContainsString('id="dibp-receipts-unused"', $blade);
        Assert::assertStringContainsString('id="dibp-receipts-unused-list"', $blade);
        Assert::assertStringContainsString('id="dibp-receipts-unused-toggle"', $blade);
        Assert::assertStringContainsString('id="dibp-receipts-unused-context-menu"', $blade);
        Assert::assertStringContainsString('data-action="mark-unused"', $blade);
        Assert::assertStringContainsString('data-action="restore-unused"', $blade);
        Assert::assertStringContainsString('data-action="delete-unused"', $blade);
        Assert::assertStringContainsString('Back to Receipt', $blade);
        Assert::assertStringContainsString('data-unused-url', $blade);
        Assert::assertStringContainsString('data-restore-url', $blade);
        Assert::assertStringContainsString('data-delete-url', $blade);
        Assert::assertStringNotContainsString('class="notuseddoc"', $blade);
        Assert::assertStringNotContainsString('class="backtodoc"', $blade);
        Assert::assertStringNotContainsString('class="deletenote"', $blade);
        Assert::assertStringNotContainsString('id="notuseddocuments-tab"', $blade);
        Assert::assertStringNotContainsString('notuseddocumnetlist', $blade);

        $companyDetail = file_get_contents($this->projectPath('resources/views/crm/companies/detail.blade.php'));
        Assert::assertNotFalse($companyDetail);
        Assert::assertStringContainsString('crm.clients.tabs.account', $companyDetail);
        Assert::assertStringContainsString('dibp-receipts-tab.js', $companyDetail);
        Assert::assertStringNotContainsString('account-tab.js', $companyDetail);

        $js = file_get_contents($this->projectPath('public/js/crm/clients/dibp-receipts-tab.js'));
        Assert::assertNotFalse($js);
        Assert::assertStringContainsString('dibpReceiptsMarkUnused', $js);
        Assert::assertStringContainsString('dibpReceiptsRestoreUnused', $js);
        Assert::assertStringContainsString('dibpReceiptsDeleteUnused', $js);
        Assert::assertStringContainsString('dibpReceiptsShowUnusedPane', $js);
        Assert::assertStringContainsString('data-unused-url', $js);
        Assert::assertStringContainsString('dibp-receipts-unused-file', $js);
        Assert::assertSame(8, substr_count($js, '}, true);'));
        Assert::assertStringNotContainsString('notuseddoc', $js);
        Assert::assertStringNotContainsString('backtodoc', $js);
        Assert::assertStringNotContainsString('deletedocs', $js);
        Assert::assertStringNotContainsString('deletenote', $js);
        Assert::assertStringNotContainsString('/documents/not-used', $js);
        Assert::assertStringNotContainsString('/documents/back-to-doc', $js);
        Assert::assertStringNotContainsString('notuseddocuments-tab', $js);
        Assert::assertStringNotContainsString('notuseddocumnetlist', $js);

        $notUsed = file_get_contents($this->projectPath('resources/views/crm/clients/tabs/not_used_documents.blade.php'));
        Assert::assertNotFalse($notUsed);
        Assert::assertStringContainsString('notuseddocumnetlist', $notUsed);
        Assert::assertStringContainsString('backtodoc', $notUsed);
        Assert::assertStringNotContainsString('dibp-receipts-', $notUsed);
        Assert::assertStringNotContainsString('mark-dibp-receipt-unused', $notUsed);
        Assert::assertStringNotContainsString('restore-dibp-receipt', $notUsed);

        $detailMain = file_get_contents($this->projectPath('public/js/crm/clients/detail-main.js'));
        Assert::assertNotFalse($detailMain);
        Assert::assertStringContainsString('.notuseddoc', $detailMain);
        Assert::assertStringContainsString('.backtodoc', $detailMain);
        Assert::assertStringNotContainsString('dibp-receipts-unused', $detailMain);
        Assert::assertStringNotContainsString('mark-dibp-receipt-unused', $detailMain);
    }

    private function seedClient(int $id): void
    {
        if (! Schema::hasTable('admins')) {
            Schema::create('admins', function (Blueprint $table) {
                $table->id();
                $table->string('client_id')->nullable();
                $table->string('first_name')->nullable();
                $table->unsignedTinyInteger('is_company')->nullable();
                $table->timestamps();
            });
        }

        DB::table('admins')->updateOrInsert(
            ['id' => $id],
            [
                'client_id' => 'C'.$id,
                'first_name' => 'Test',
                'is_company' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
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
                $table->unsignedBigInteger('client_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('client_matter_id')->nullable();
                $table->string('doc_type')->nullable();
                $table->string('type')->nullable();
                $table->string('folder_name')->nullable();
                $table->string('file_name')->nullable();
                $table->string('checklist')->nullable();
                $table->string('myfile')->nullable();
                $table->string('myfile_key')->nullable();
                $table->string('filetype')->nullable();
                $table->unsignedInteger('file_size')->nullable();
                $table->string('status')->nullable();
                $table->unsignedTinyInteger('not_used_doc')->nullable();
                $table->boolean('hubdoc_sent')->nullable();
                $table->timestamp('hubdoc_sent_at')->nullable();
                $table->timestamps();
            });
        }

        foreach (['myfile', 'myfile_key', 'filetype', 'folder_name', 'checklist'] as $column) {
            if (! Schema::hasColumn('documents', $column)) {
                Schema::table('documents', function (Blueprint $table) use ($column) {
                    $table->string($column)->nullable();
                });
            }
        }
        if (! Schema::hasColumn('documents', 'file_size')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->unsignedInteger('file_size')->nullable();
            });
        }
        if (! Schema::hasColumn('documents', 'not_used_doc')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->unsignedTinyInteger('not_used_doc')->nullable();
            });
        }

        foreach (['password', 'role', 'status'] as $column) {
            if (Schema::hasTable('staff') && ! Schema::hasColumn('staff', $column)) {
                Schema::table('staff', function (Blueprint $table) use ($column) {
                    if ($column === 'password') {
                        $table->string($column)->nullable();
                    } else {
                        $table->unsignedInteger($column)->nullable();
                    }
                });
            }
        }

        if (! Schema::hasTable('signers')) {
            Schema::create('signers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('document_id')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('activities_logs')) {
            Schema::create('activities_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->string('subject')->nullable();
                $table->text('description')->nullable();
                $table->string('activity_type')->nullable();
                $table->unsignedTinyInteger('task_status')->nullable();
                $table->unsignedTinyInteger('pin')->nullable();
                $table->timestamps();
            });
        }
    }

    private function projectPath(string $relative): string
    {
        return dirname(__DIR__, 2).DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relative);
    }
}
