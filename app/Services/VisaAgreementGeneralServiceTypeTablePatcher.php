<?php

namespace App\Services;

/**
 * Runtime DOCX XML patch for Service_Agreement_general.docx service type row.
 *
 * Replaces the single-line Category + Subclass list paragraph with a two-column
 * borderless table so long subclass text wraps inside the right column only.
 */
class VisaAgreementGeneralServiceTypeTablePatcher
{
    private const TEMPLATE = 'Service_Agreement_general.docx';

    /**
     * @return array{xml: string, patched: bool}
     */
    public function patchDocumentXml(string $xml): array
    {
        if ($this->documentHasServiceTypeTable($xml)) {
            return ['xml' => $xml, 'patched' => false];
        }

        preg_match_all('/<w:p\b.*?<\/w:p>/s', $xml, $paragraphMatches);
        foreach ($paragraphMatches[0] as $paragraph) {
            if (! $this->isCategorySubclassParagraph($paragraph)) {
                continue;
            }

            $table = $this->buildServiceTypeTable($paragraph);
            if ($table === null) {
                continue;
            }

            $xml = str_replace($paragraph, $table, $xml);

            return ['xml' => $xml, 'patched' => true];
        }

        return ['xml' => $xml, 'patched' => false];
    }

    public static function supportsTemplate(?string $templateFileName): bool
    {
        if ($templateFileName === null || $templateFileName === '') {
            return false;
        }

        return $templateFileName === config('visa_agreement_templates.default', self::TEMPLATE);
    }

    private function documentHasServiceTypeTable(string $xml): bool
    {
        preg_match_all('/<w:tbl\b.*?<\/w:tbl>/s', $xml, $tableMatches);
        foreach ($tableMatches[0] as $table) {
            $text = $this->extractPlainText($table);
            if (
                str_contains($text, 'Category:')
                && str_contains($text, 'Subclass:')
                && preg_match('/\$\{(?:visa_apply|Visa_apply)\}/', $text) === 1
            ) {
                return true;
            }
        }

        return false;
    }

    private function isCategorySubclassParagraph(string $paragraph): bool
    {
        $text = $this->extractPlainText($paragraph);

        return str_contains($text, 'Category:')
            && str_contains($text, 'Subclass:')
            && preg_match('/\$\{(?:visa_apply|Visa_apply)\}/', $text) === 1;
    }

    private function buildServiceTypeTable(string $paragraph): ?string
    {
        $plain = $this->extractPlainText($paragraph);
        if (! preg_match('/Category:\s*(.*?)\s*Subclass:\s*(\$\{(?:visa_apply|Visa_apply)\})/si', $plain, $matches)) {
            return null;
        }

        $categoryValue = trim($matches[1]);

        if (! preg_match('/^(<w:p\b[^>]*>)(<w:pPr>.*?<\/w:pPr>)?/s', $paragraph, $openMatch)) {
            return null;
        }

        $paragraphOpen = $openMatch[1];
        $paragraphProperties = $openMatch[2] ?? '';

        preg_match_all('/<w:r\b[^>]*>.*?<\/w:r>/s', $paragraph, $runMatches);
        $runs = $runMatches[0];

        $categoryLabelRun = null;
        $categoryValueRunProperties = null;
        $subclassRunIndex = $this->findSubclassRunIndex($runs);

        foreach ($runs as $index => $run) {
            if ($subclassRunIndex !== null && $index >= $subclassRunIndex) {
                break;
            }

            $runText = $this->extractPlainText($run);

            if ($categoryLabelRun === null && str_contains($runText, 'Category:')) {
                $categoryLabelRun = $run;

                continue;
            }

            if ($categoryLabelRun !== null && trim($runText) !== '') {
                $categoryValueRunProperties = $this->extractRunProperties($run);
            }
        }

        if ($categoryLabelRun === null || $subclassRunIndex === null) {
            return null;
        }

        $subclassRuns = array_slice($runs, $subclassRunIndex);
        if ($subclassRuns === []) {
            return null;
        }

        $valueRunProperties = $categoryValueRunProperties ?? $this->extractRunProperties($categoryLabelRun);

        $categoryCellParagraph = $paragraphOpen
            .$paragraphProperties
            .$categoryLabelRun
            .$this->buildTextRun($valueRunProperties, $categoryValue)
            .'</w:p>';

        $subclassCellParagraph = $paragraphOpen
            .$this->buildSubclassCellParagraphProperties($paragraphProperties)
            .implode('', $subclassRuns)
            .'</w:p>';

        return '<w:tbl>'
            .'<w:tblPr>'
            .'<w:tblW w:w="5000" w:type="pct"/>'
            .'<w:tblLayout w:type="fixed"/>'
            .'<w:tblBorders>'
            .'<w:top w:val="none" w:sz="0" w:space="0" w:color="auto"/>'
            .'<w:left w:val="none" w:sz="0" w:space="0" w:color="auto"/>'
            .'<w:bottom w:val="none" w:sz="0" w:space="0" w:color="auto"/>'
            .'<w:right w:val="none" w:sz="0" w:space="0" w:color="auto"/>'
            .'<w:insideH w:val="none" w:sz="0" w:space="0" w:color="auto"/>'
            .'<w:insideV w:val="none" w:sz="0" w:space="0" w:color="auto"/>'
            .'</w:tblBorders>'
            .'<w:tblLook w:val="04A0" w:firstRow="1" w:lastRow="0" w:firstColumn="1" w:lastColumn="0" w:noHBand="1" w:noVBand="1"/>'
            .'</w:tblPr>'
            .'<w:tblGrid><w:gridCol w:w="4536"/><w:gridCol w:w="4536"/></w:tblGrid>'
            .'<w:tr>'
            .'<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr>'
            .$categoryCellParagraph
            .'</w:tc>'
            .'<w:tc><w:tcPr><w:tcW w:w="2500" w:type="pct"/></w:tcPr>'
            .$subclassCellParagraph
            .'</w:tc>'
            .'</w:tr>'
            .'</w:tbl>';
    }

    /**
     * @param  list<string>  $runs
     */
    private function findSubclassRunIndex(array $runs): ?int
    {
        foreach ($runs as $index => $run) {
            $text = $this->extractPlainText($run);
            if ($text === 'S' && isset($runs[$index + 1]) && str_contains($this->extractPlainText($runs[$index + 1]), 'ubclass')) {
                return $index;
            }

            if (str_contains($text, 'Subclass')) {
                return $index;
            }
        }

        return null;
    }

    private function buildSubclassCellParagraphProperties(string $paragraphProperties): string
    {
        if ($paragraphProperties === '') {
            return '<w:pPr/>';
        }

        $properties = preg_replace('/<w:numPr>.*?<\/w:numPr>/s', '', $paragraphProperties);
        if (! is_string($properties) || $properties === '') {
            return '<w:pPr/>';
        }

        if (! str_contains($properties, '<w:pPr')) {
            return '<w:pPr/>';
        }

        return $properties;
    }

    private function buildTextRun(string $runProperties, string $text): string
    {
        $escaped = htmlspecialchars($text, ENT_XML1 | ENT_QUOTES, 'UTF-8');

        return '<w:r>'.$runProperties.'<w:t xml:space="preserve">'.$escaped.'</w:t></w:r>';
    }

    private function extractRunProperties(string $run): string
    {
        if (preg_match('/<w:rPr\b.*?<\/w:rPr>/s', $run, $match)) {
            return $match[0];
        }

        return '';
    }

    private function extractPlainText(string $xmlFragment): string
    {
        $text = trim(preg_replace('/\s+/', ' ', strip_tags(preg_replace('/<w:t[^>]*>/', '', $xmlFragment))));

        return html_entity_decode($text, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }
}
