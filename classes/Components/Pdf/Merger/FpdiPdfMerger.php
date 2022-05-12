<?php

namespace Xentral\Components\Pdf\Merger;

use fpdi;
use Xentral\Components\Pdf\Exception\FileExistsException;
use Xentral\Components\Pdf\Exception\FileNotFoundException;
use Xentral\Components\Pdf\Exception\FileNotWritableException;
use Xentral\Components\Pdf\Exception\InvalidArgumentException;
use Xentral\Components\Pdf\Exception\PdfIncompatibleException;

/**
 * Anti-Corruption-Layer für alte FPDI-Klasse
 */
final class FpdiPdfMerger extends AbstractPdfMerger
{
    /**
     * @param string[] $sourceFiles Array with files pathes
     * @param string   $targetFile  Path to merged file
     *
     * @throws InvalidArgumentException If required parameter is invalid/empty
     * @throws FileNotWritableException If target file is not writable
     * @throws FileExistsException If target file already exists
     * @throws FileNotFoundException If one of the source files does not exist
     * @throws PdfIncompatibleException If XRef-Pointer is missing
     *
     * @return void
     */
    public function mergeFiles(array $sourceFiles, $targetFile)
    {
        $this->preCheckMergeFiles($sourceFiles, $targetFile);

        $fpdi = new fpdi('P', 'mm', 'A4');

        foreach ($sourceFiles as $file) {
            if (!$this->hasPdfXrefError($file)) {
                throw new PdfIncompatibleException(sprintf(
                    'Can not merge PDFs. Unable to find pointer to xref table in file "%s".', $file
                ));
            }
            $pageCount = $fpdi->setSourceFile($file);

            for ($pageNumber = 1; $pageNumber <= $pageCount; $pageNumber++) {
                $templateId = $fpdi->importPage($pageNumber);
                $size = $fpdi->getTemplateSize($templateId);

                // Create empty page
                if ($size['w'] > $size['h']) {
                    // Landscape orientation
                    $fpdi->addPage('L', [$size['h'], $size['w']]);
                } else {
                    // Portrait orientation
                    $fpdi->addPage('P', [$size['w'], $size['h']]);
                }

                // Import page
                $fpdi->useTemplate($templateId);
            }
        }

        $fpdi->Output($targetFile, 'F');
    }

    /**
     * Helper method to detect xref-Error
     *
     * @see \fpdi_pdf_parser::pdf_find_xref
     *
     * @param string $pdfFile Path to pdf file
     *
     * @return bool
     */
    public function hasPdfXrefError($pdfFile)
    {
        $resource = @fopen($pdfFile, 'rb');
        fseek($resource, -50, SEEK_END);
        $data = fread($resource, 50);

        $foundXrefOffset = preg_match('/startxref\s*(\d+)\s*%%EOF\s*$/', $data, $matches);
        if (!$foundXrefOffset) {
            return false;
        }

        $xrefOffset = (int)$matches[1];
        fseek($resource, $xrefOffset);
        $data = trim(fgets($resource));

        return $data === 'xref';
    }
}
