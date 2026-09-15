<?php

namespace Tests\Unit;

use App\Http\Requests\StoreForm956Request;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CompanyNominationForm956Test extends TestCase
{
    #[Test]
    public function company_file_documents_tab_has_create_form_956_button_for_nomination(): void
    {
        $nomination = file_get_contents($this->projectPath('resources/views/crm/companies/tabs/nomination_documents.blade.php'));
        Assert::assertNotFalse($nomination);

        Assert::assertStringContainsString('form956CreateForm', $nomination);
        Assert::assertStringContainsString('Create Form 956', $nomination);
        Assert::assertStringContainsString('data-form956-doctype="nomination"', $nomination);
        Assert::assertStringContainsString('data-form956-folder=', $nomination);
        Assert::assertStringContainsString('window.initNominationDocDragDrop = initNominationDocDragDrop', $nomination);

        // Keep existing checklist / bulk actions intact next to the new button.
        Assert::assertStringContainsString('add_nomination_doc', $nomination);
        Assert::assertStringContainsString('bulk-upload-toggle-btn-nomination', $nomination);
    }

    #[Test]
    public function person_visa_documents_still_create_form_956_as_visa_doc_type(): void
    {
        $visa = file_get_contents($this->projectPath('resources/views/crm/clients/tabs/visa_documents.blade.php'));
        Assert::assertNotFalse($visa);

        Assert::assertStringContainsString('form956CreateForm', $visa);
        Assert::assertStringContainsString('data-form956-doctype="visa"', $visa);
    }

    #[Test]
    public function form_956_modal_carries_doc_type_hidden_field_defaulting_to_visa(): void
    {
        $modal = file_get_contents($this->projectPath('resources/views/crm/clients/modals/forms.blade.php'));
        Assert::assertNotFalse($modal);

        Assert::assertStringContainsString('id="form956_doc_type"', $modal);
        Assert::assertStringContainsString('name="form956_doc_type"', $modal);
        Assert::assertStringContainsString('value="visa"', $modal);
    }

    #[Test]
    public function store_form_956_request_accepts_visa_or_nomination_doc_type(): void
    {
        $rules = (new StoreForm956Request)->rules();

        Assert::assertArrayHasKey('form956_doc_type', $rules);
        Assert::assertSame('nullable|in:visa,nomination', $rules['form956_doc_type']);
        Assert::assertArrayHasKey('form956_folder_name', $rules);
    }

    #[Test]
    public function form_956_store_and_soft_insert_support_nomination_doc_type(): void
    {
        $controller = file_get_contents($this->projectPath('app/Http/Controllers/CRM/Form956Controller.php'));
        Assert::assertNotFalse($controller);
        Assert::assertStringContainsString("\$validated['form956_doc_type']", $controller);
        Assert::assertStringContainsString('$document->doc_type = $docType;', $controller);
        Assert::assertStringContainsString("'doc_type' => \$docType,", $controller);

        $detailMain = file_get_contents($this->projectPath('public/js/crm/clients/detail-main.js'));
        Assert::assertNotFalse($detailMain);
        Assert::assertStringContainsString("$('#form956_doc_type').val(", $detailMain);
        Assert::assertStringContainsString("$('#nominationdocuments-tab')", $detailMain);
        Assert::assertStringContainsString("doc.doc_type === 'nomination'", $detailMain);
        Assert::assertStringContainsString('window.initNominationDocDragDrop', $detailMain);
        Assert::assertStringContainsString('nomination-doc-drag-zone', $detailMain);
    }

    private function projectPath(string $relative): string
    {
        return dirname(__DIR__, 2).DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relative);
    }
}
