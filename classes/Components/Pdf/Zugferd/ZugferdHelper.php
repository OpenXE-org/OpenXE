<?php

namespace Xentral\Components\Pdf\Zugferd;

use SuperFPDF;

/**
 * Helper class for generating and managing ZUGFeRD / Factur-X / XRechnung hybrid PDF invoices.
 *
 * Implements 100% native pure-PHP embedding of electronic invoices into PDF/A-3 containers
 * without external tools (no Python, no Docker, no external CLI commands required).
 */
class ZugferdHelper
{
    public const TYPE_STANDARD_PDF = 0;
    public const TYPE_XML_ONLY = 1;
    public const TYPE_ZUGFERD_HYBRID = 2;

    public const FILENAME_XRECHNUNG = 'xrechnung.xml';
    public const FILENAME_FACTURX = 'factur-x.xml';

    /**
     * Check if the given invoice xmlrechnung value represents a ZUGFeRD hybrid invoice
     *
     * @param int|string $xmlrechnung
     * @return bool
     */
    public static function isZugferd($xmlrechnung): bool
    {
        return (int)$xmlrechnung === self::TYPE_ZUGFERD_HYBRID;
    }

    /**
     * Check if the given invoice xmlrechnung value represents a pure XML invoice (legacy behavior)
     *
     * @param int|string $xmlrechnung
     * @return bool
     */
    public static function isXmlOnly($xmlrechnung): bool
    {
        return (int)$xmlrechnung === self::TYPE_XML_ONLY;
    }

    /**
     * Detect the appropriate XML filename to embed inside the PDF.
     *
     * By default, XRechnung UBL uses 'xrechnung.xml', while CII (Factur-X / ZUGFeRD 2) uses 'factur-x.xml'.
     *
     * @param string $templateName Name of the Smarty template
     * @param string $xmlContent Optional XML content to inspect root tag
     * @return string
     */
    public static function detectXmlFilename(string $templateName = '', string $xmlContent = ''): string
    {
        if (!empty($templateName) && (stripos($templateName, 'factur') !== false || stripos($templateName, 'zugferd') !== false)) {
            return self::FILENAME_FACTURX;
        }

        if (!empty($xmlContent) && stripos($xmlContent, 'CrossIndustryInvoice') !== false) {
            return self::FILENAME_FACTURX;
        }

        return self::FILENAME_XRECHNUNG;
    }

    /**
     * Embed an XML invoice into an existing PDF file and save as hybrid PDF/A-3.
     *
     * @param string $pdfFilePath Path to existing PDF file
     * @param string $xmlContent Raw XML invoice string
     * @param string $targetPdfPath Target path for hybrid PDF (if null, overwrites source)
     * @param string $xmlFilename Filename inside the PDF container (xrechnung.xml or factur-x.xml)
     * @return string Path to the written hybrid PDF file
     */
    public static function embedXmlInPdfFile(string $pdfFilePath, string $xmlContent, ?string $targetPdfPath = null, string $xmlFilename = self::FILENAME_XRECHNUNG): string
    {
        if (!file_exists($pdfFilePath)) {
            throw new \InvalidArgumentException("Source PDF file does not exist: $pdfFilePath");
        }

        if (empty($targetPdfPath)) {
            $targetPdfPath = $pdfFilePath;
        }

        $pdfContent = file_get_contents($pdfFilePath);
        $hybridPdf = self::embedXmlInPdfContent($pdfContent, $xmlContent, $xmlFilename);

        file_put_contents($targetPdfPath, $hybridPdf);
        return $targetPdfPath;
    }

    /**
     * Embed an XML invoice into raw PDF bytes and return the hybrid PDF/A-3 bytes.
     *
     * @param string $pdfContent Raw PDF content
     * @param string $xmlContent Raw XML invoice string
     * @param string $xmlFilename Filename inside the PDF container (xrechnung.xml or factur-x.xml)
     * @return string Raw hybrid PDF bytes
     */
    public static function embedXmlInPdfContent(string $pdfContent, string $xmlContent, string $xmlFilename = self::FILENAME_XRECHNUNG): string
    {
        $pdf = new SuperFPDF('P', 'mm', 'A4');
        $pdf->AddPDF($pdfContent);
        $pdf->AttachFile($xmlContent, $xmlFilename, 'ZUGFeRD / XRechnung E-Rechnung', 'Alternative');
        return $pdf->displayAnhaenge('S');
    }
}
