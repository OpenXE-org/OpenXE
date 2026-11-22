<?php

define('USEFPDF3', true);
require_once dirname(__DIR__, 2) . '/www/lib/pdf/fpdf_3.php';
require_once dirname(__DIR__, 2) . '/www/lib/pdf/fpdf_final.php';
require_once dirname(__DIR__, 2) . '/www/lib/dokumente/class.superfpdf.php';
require_once dirname(__DIR__, 2) . '/classes/Components/Pdf/Zugferd/ZugferdHelper.php';

use Xentral\Components\Pdf\Zugferd\ZugferdHelper;

/**
 * Unit Tests for Pure-PHP ZUGFeRD / Factur-X / PDF/A-3 Hybrid Generation.
 */
class ZugferdTest
{
    private $passed = 0;
    private $failed = 0;

    public function run()
    {
        echo "====================================================\n";
        echo "Running ZUGFeRD / Factur-X PDF/A-3 Hybrid Tests\n";
        echo "====================================================\n";

        $this->testTypeClassification();
        $this->testDetectXmlFilename();
        $this->testPdfA3HeaderAndCatalogStructure();
        $this->testPdfStringEscapingInAttachFile();
        $this->testXmpMetadataAndExtensionSchema();
        $this->testEmbeddedFileStreamIntegrity();
        $this->testEmbedXmlInPdfContent();
        $this->testEmbedXmlInPdfFile();

        echo "----------------------------------------------------\n";
        echo "Tests completed: {$this->passed} passed, {$this->failed} failed.\n";
        echo "====================================================\n";

        if ($this->failed > 0) {
            exit(1);
        }
    }

    private function assert($condition, $message)
    {
        if ($condition) {
            $this->passed++;
            echo "  [PASS] {$message}\n";
        } else {
            $this->failed++;
            echo "  [FAIL] {$message}\n";
        }
    }

    private function testTypeClassification()
    {
        echo "\nTest 1: Invoice Type Classification (xmlrechnung semantics)\n";

        $this->assert(ZugferdHelper::isZugferd(2) === true, 'xmlrechnung = 2 is recognized as ZUGFeRD hybrid');
        $this->assert(ZugferdHelper::isZugferd('2') === true, "xmlrechnung = '2' is recognized as ZUGFeRD hybrid");
        $this->assert(ZugferdHelper::isZugferd(1) === false, 'xmlrechnung = 1 is not ZUGFeRD hybrid');
        $this->assert(ZugferdHelper::isZugferd(0) === false, 'xmlrechnung = 0 is not ZUGFeRD hybrid');

        $this->assert(ZugferdHelper::isXmlOnly(1) === true, 'xmlrechnung = 1 is recognized as XML-only');
        $this->assert(ZugferdHelper::isXmlOnly('1') === true, "xmlrechnung = '1' is recognized as XML-only");
        $this->assert(ZugferdHelper::isXmlOnly(2) === false, 'xmlrechnung = 2 is not XML-only');
        $this->assert(ZugferdHelper::isXmlOnly(0) === false, 'xmlrechnung = 0 is not XML-only');
    }

    private function testDetectXmlFilename()
    {
        echo "\nTest 2: XML Filename Detection\n";

        // Default UBL / XRechnung
        $this->assert(ZugferdHelper::detectXmlFilename() === 'xrechnung.xml', 'Default filename is xrechnung.xml');
        $this->assert(ZugferdHelper::detectXmlFilename('standard_ubl.xml') === 'xrechnung.xml', 'UBL template defaults to xrechnung.xml');

        // Factur-X / ZUGFeRD by template name
        $this->assert(ZugferdHelper::detectXmlFilename('factur-x_template.xml') === 'factur-x.xml', 'Template with factur-x uses factur-x.xml');
        $this->assert(ZugferdHelper::detectXmlFilename('zugferd_en16931.xml') === 'factur-x.xml', 'Template with zugferd uses factur-x.xml');

        // Factur-X / CII by XML content root
        $ciiXml = '<rsm:CrossIndustryInvoice xmlns:rsm="urn:un:unece:uncefact:data:standard:CrossIndustryInvoice:100"></rsm:CrossIndustryInvoice>';
        $this->assert(ZugferdHelper::detectXmlFilename('', $ciiXml) === 'factur-x.xml', 'CrossIndustryInvoice content triggers factur-x.xml');
    }

    private function testPdfA3HeaderAndCatalogStructure()
    {
        echo "\nTest 3: PDF/A-3 Header & Catalog Structure\n";

        $pdf = new SuperFPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(40, 10, 'Rechnung RE-2026-001');
        $pdf->AttachFile('<test>data</test>', 'xrechnung.xml', 'E-Rechnung', 'Alternative');
        $output = $pdf->Output('', 'S');

        // PDF 1.7 Header with high-order binary bytes
        $this->assert(strpos($output, '%PDF-1.7') === 0, 'PDF starts with %PDF-1.7 header');
        $this->assert(strpos($output, "%\xE2\xE3\xCF\xD3") !== false, 'Header contains 4 binary characters > 127');

        // Catalog entries
        $this->assert(strpos($output, '/AF [') !== false, 'Catalog contains /AF array');
        $this->assert(strpos($output, '/Names << /EmbeddedFiles << /Names [') !== false, 'Catalog contains /Names /EmbeddedFiles dictionary');
        $this->assert(strpos($output, '/Metadata') !== false, 'Catalog references /Metadata object');
        $this->assert(strpos($output, '/OutputIntents [') !== false, 'Catalog references /OutputIntents array');

        // OutputCondition
        $this->assert(strpos($output, '/OutputCondition (sRGB IEC61966-2.1)') !== false, 'OutputIntent contains sRGB condition');
    }

    private function testPdfStringEscapingInAttachFile()
    {
        echo "\nTest 4: PDF String Escaping in Attachments\n";

        $pdf = new SuperFPDF();
        $pdf->AddPage();
        $pdf->AttachFile('<test/>', 'xrechnung (draft).xml', 'Description with (parentheses) & \\backslashes\\');
        $output = $pdf->Output('', 'S');

        $this->assert(strpos($output, '/F (xrechnung \\(draft\\).xml)') !== false, 'Filename parentheses are escaped');
        $this->assert(strpos($output, '/Desc (Description with \\(parentheses\\) & \\\\backslashes\\\\)') !== false, 'Description parentheses and backslashes are escaped');
    }

    private function testXmpMetadataAndExtensionSchema()
    {
        echo "\nTest 5: XMP Metadata & Factur-X Extension Schema\n";

        $pdf = new SuperFPDF();
        $pdf->AddPage();
        $pdf->AttachFile('<Invoice>test</Invoice>', 'factur-x.xml', 'Factur-X Invoice', 'Alternative');
        $output = $pdf->Output('', 'S');

        // PDF/A-3 identification
        $this->assert(strpos($output, '<pdfaid:part>3</pdfaid:part>') !== false, 'XMP identifies PDF/A part 3');
        $this->assert(strpos($output, '<pdfaid:conformance>B</pdfaid:conformance>') !== false, 'XMP identifies Conformance Level B');

        // Factur-X Extension Schema
        $this->assert(strpos($output, 'urn:factur-x:pdfa:CrossIndustryDocument:invoice:1p0#') !== false, 'XMP declares Factur-X namespace URI');
        $this->assert(strpos($output, '<fx:DocumentType>INVOICE</fx:DocumentType>') !== false, 'XMP declares DocumentType INVOICE');
        $this->assert(strpos($output, '<fx:DocumentFileName>factur-x.xml</fx:DocumentFileName>') !== false, 'XMP declares DocumentFileName factur-x.xml');
        $this->assert(strpos($output, '<fx:ConformanceLevel>EN 16931</fx:ConformanceLevel>') !== false, 'XMP declares ConformanceLevel EN 16931');
    }

    private function testEmbeddedFileStreamIntegrity()
    {
        echo "\nTest 6: Embedded File Stream Integrity (Round-trip Compression)\n";

        $originalXml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" .
            '<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2">' . "\n" .
            '  <cbc:ID>RE-2026-999</cbc:ID>' . "\n" .
            '  <cbc:PayableAmount currencyID="EUR">142.80</cbc:PayableAmount>' . "\n" .
            '</Invoice>';

        $pdf = new SuperFPDF();
        $pdf->AddPage();
        $pdf->AttachFile($originalXml, 'xrechnung.xml', 'XRechnung UBL', 'Alternative');
        $output = $pdf->Output('', 'S');

        // Verify Filespec entry
        $this->assert(strpos($output, '/Type /Filespec /F (xrechnung.xml)') !== false, 'Filespec dictionary contains correct filename');
        $this->assert(strpos($output, '/AFRelationship /Alternative') !== false, 'Filespec has AFRelationship /Alternative');

        // Extract FlateDecode compressed stream and verify round-trip
        $matched = preg_match(
            '/<< \/Type \/EmbeddedFile \/Subtype \/text#2Fxml \/Filter \/FlateDecode \/Length (\d+) \/Params << \/Size (\d+) >> >>\s*stream\r?\n(.*?)\r?\nendstream/s',
            $output,
            $matches
        );

        $this->assert($matched === 1, 'EmbeddedFile stream found with correct dictionary headers');

        if ($matched === 1) {
            $decompressed = gzuncompress($matches[3]);
            $this->assert($decompressed === $originalXml, 'Decompressed embedded XML matches original content bit-for-bit');
            $this->assert((int)$matches[2] === strlen($originalXml), 'Params Size matches raw XML byte length');
        }
    }

    private function testEmbedXmlInPdfContent()
    {
        echo "\nTest 7: ZugferdHelper::embedXmlInPdfContent\n";

        // Generate base PDF
        $basePdf = new SuperFPDF();
        $basePdf->AddPage();
        $basePdf->SetFont('Arial', '', 12);
        $basePdf->Cell(40, 10, 'Existing Document');
        $pdfBytes = $basePdf->Output('', 'S');

        $xml = '<ubl:Invoice>Content</ubl:Invoice>';
        $hybridBytes = ZugferdHelper::embedXmlInPdfContent($pdfBytes, $xml, 'xrechnung.xml');

        $this->assert(strlen($hybridBytes) > strlen($pdfBytes), 'Hybrid PDF contains additional embedded payload');
        $this->assert(strpos($hybridBytes, '%PDF-1.7') === 0, 'Hybrid PDF has PDF 1.7 header');
        $this->assert(strpos($hybridBytes, '/Type /Filespec /F (xrechnung.xml)') !== false, 'Hybrid PDF contains embedded xrechnung.xml filespec');
        $this->assert(strpos($hybridBytes, '<pdfaid:part>3</pdfaid:part>') !== false, 'Hybrid PDF has PDF/A-3 XMP metadata');
    }

    private function testEmbedXmlInPdfFile()
    {
        echo "\nTest 8: ZugferdHelper::embedXmlInPdfFile\n";

        $tmpDir = sys_get_temp_dir();
        $srcPath = $tmpDir . '/test_zugferd_src_' . uniqid() . '.pdf';
        $targetPath = $tmpDir . '/test_zugferd_target_' . uniqid() . '.pdf';

        // Write a simple PDF to disk
        $basePdf = new SuperFPDF();
        $basePdf->AddPage();
        $basePdf->SetFont('Arial', '', 12);
        $basePdf->Cell(40, 10, 'File Test');
        file_put_contents($srcPath, $basePdf->Output('', 'S'));

        $xml = '<rsm:CrossIndustryInvoice>Factur-X Payload</rsm:CrossIndustryInvoice>';
        ZugferdHelper::embedXmlInPdfFile($srcPath, $xml, $targetPath, 'factur-x.xml');

        $this->assert(file_exists($targetPath), 'Target hybrid PDF file was created on disk');
        $targetContent = file_get_contents($targetPath);
        $this->assert(strpos($targetContent, '/Type /Filespec /F (factur-x.xml)') !== false, 'Target PDF file contains factur-x.xml filespec');
        $this->assert(strpos($targetContent, '<fx:DocumentFileName>factur-x.xml</fx:DocumentFileName>') !== false, 'Target PDF file contains XMP Factur-X filename');

        // Clean up
        @unlink($srcPath);
        @unlink($targetPath);
    }
}

$test = new ZugferdTest();
$test->run();
