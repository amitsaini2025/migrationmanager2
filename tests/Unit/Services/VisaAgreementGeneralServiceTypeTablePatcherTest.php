<?php

namespace Tests\Unit\Services;

use App\Services\VisaAgreementGeneralServiceTypeTablePatcher;
use App\Services\VisaAgreementServiceTypeRowPatcher;
use Tests\TestCase;
use ZipArchive;

class VisaAgreementGeneralServiceTypeTablePatcherTest extends TestCase
{
    private VisaAgreementGeneralServiceTypeTablePatcher $patcher;

    protected function setUp(): void
    {
        parent::setUp();
        $this->patcher = new VisaAgreementGeneralServiceTypeTablePatcher;
    }

    public function test_patches_general_template_with_two_column_table(): void
    {
        $path = storage_path('app/templates/Service_Agreement_general.docx');
        if (! is_file($path)) {
            $this->markTestSkipped('Service_Agreement_general.docx not present in storage/app/templates');
        }

        $xml = $this->readDocumentXml($path);
        $before = $this->categorySubclassParagraph($xml);
        if ($before === null) {
            $this->markTestSkipped('Service_Agreement_general.docx has no Category/Subclass service type row');
        }

        $result = $this->patcher->patchDocumentXml($xml);
        $this->assertTrue($result['patched']);

        $this->assertNull($this->categorySubclassParagraph($result['xml']));
        $table = $this->serviceTypeTable($result['xml']);
        $this->assertNotNull($table);
        $this->assertStringContainsString('Category:', $table['text']);
        $this->assertStringContainsString('Subclass:', $table['text']);
        $this->assertStringContainsString('${visa_apply}', $table['text']);
        $this->assertSame(2, $table['column_count']);
        $this->assertSame($before['category_value'], $table['category_value']);
        $this->assertSame($before['placeholder'], $table['placeholder']);
    }

    public function test_patch_is_idempotent_for_general_template(): void
    {
        $path = storage_path('app/templates/Service_Agreement_general.docx');
        if (! is_file($path)) {
            $this->markTestSkipped('Service_Agreement_general.docx not present in storage/app/templates');
        }

        $xml = $this->readDocumentXml($path);
        $first = $this->patcher->patchDocumentXml($xml);
        $second = $this->patcher->patchDocumentXml($first['xml']);

        $this->assertTrue($first['patched']);
        $this->assertFalse($second['patched']);
        $this->assertSame($first['xml'], $second['xml']);
    }

    public function test_supports_only_general_template(): void
    {
        $this->assertTrue(VisaAgreementGeneralServiceTypeTablePatcher::supportsTemplate('Service_Agreement_general.docx'));
        $this->assertFalse(VisaAgreementGeneralServiceTypeTablePatcher::supportsTemplate('Service_Agreement_parents.docx'));
        $this->assertFalse(VisaAgreementServiceTypeRowPatcher::supportsTemplate('Service_Agreement_general.docx'));
        $this->assertTrue(VisaAgreementServiceTypeRowPatcher::supportsTemplate('Service_Agreement_parents.docx'));
    }

    public function test_does_not_change_unrelated_paragraphs(): void
    {
        $xml = '<w:document><w:body><w:p><w:r><w:t>Other content</w:t></w:r></w:p></w:body></w:document>';

        $result = $this->patcher->patchDocumentXml($xml);

        $this->assertFalse($result['patched']);
        $this->assertSame($xml, $result['xml']);
    }

    private function readDocumentXml(string $docxPath): string
    {
        $zip = new ZipArchive;
        $this->assertTrue($zip->open($docxPath));
        $xml = $zip->getFromName('word/document.xml');
        $zip->close();
        $this->assertNotFalse($xml);

        return $xml;
    }

    /**
     * @return array{text: string, category_value: string, placeholder: string}|null
     */
    private function categorySubclassParagraph(string $xml): ?array
    {
        preg_match_all('/<w:p\b.*?<\/w:p>/s', $xml, $paragraphMatches);
        foreach ($paragraphMatches[0] as $paragraph) {
            $text = trim(preg_replace('/\s+/', ' ', strip_tags(preg_replace('/<w:t[^>]*>/', '', $paragraph))));
            if (! str_contains($text, 'Category:') || ! str_contains($text, 'Subclass:')) {
                continue;
            }

            if (! preg_match('/Category:\s*(.*?)\s*Subclass:\s*(\$\{(?:visa_apply|Visa_apply)\})/si', $text, $matches)) {
                continue;
            }

            return [
                'text' => $text,
                'category_value' => trim($matches[1]),
                'placeholder' => $matches[2],
            ];
        }

        return null;
    }

    /**
     * @return array{text: string, category_value: string, placeholder: string, column_count: int}|null
     */
    private function serviceTypeTable(string $xml): ?array
    {
        preg_match_all('/<w:tbl\b.*?<\/w:tbl>/s', $xml, $tableMatches);
        foreach ($tableMatches[0] as $table) {
            $text = trim(preg_replace('/\s+/', ' ', strip_tags(preg_replace('/<w:t[^>]*>/', '', $table))));
            if (! str_contains($text, 'Category:') || ! str_contains($text, 'Subclass:')) {
                continue;
            }

            if (! preg_match('/Category:\s*(.*?)\s*Subclass:\s*(\$\{(?:visa_apply|Visa_apply)\})/si', $text, $matches)) {
                continue;
            }

            preg_match_all('/<w:tc\b/', $table, $cells);

            return [
                'text' => $text,
                'category_value' => trim($matches[1]),
                'placeholder' => $matches[2],
                'column_count' => count($cells[0]),
            ];
        }

        return null;
    }
}
