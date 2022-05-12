<?php

namespace Xentral\Components\Pdf;

use Xentral\Components\Pdf\Exception\FileExistsException;
use Xentral\Components\Pdf\Exception\FileNotFoundException;
use Xentral\Components\Pdf\Exception\FileNotWritableException;
use Xentral\Components\Pdf\Exception\InvalidArgumentException;
use Xentral\Components\Pdf\Exception\PdfIncompatibleException;
use Xentral\Components\Pdf\Exception\ShellCommandFailedException;
use Xentral\Components\Pdf\Merger\FpdiPdfMerger;
use Xentral\Components\Pdf\Merger\GhostScriptPdfMerger;

final class PdfMerger
{
    /** @var FpdiPdfMerger $fpdi */
    private $fpdi;

    /** @var GhostScriptPdfMerger $gs */
    private $gs;

    /**
     * @param FpdiPdfMerger        $fpdi
     * @param GhostScriptPdfMerger $ghostScript
     */
    public function __construct(FpdiPdfMerger $fpdi, GhostScriptPdfMerger $ghostScript)
    {
        $this->fpdi = $fpdi;
        $this->gs = $ghostScript;
    }

    /**
     * @param array       $sourceFiles Array with file pathes
     * @param string|null $targetFile  Path to merged file; Path will be generated if null
     *
     * @throws InvalidArgumentException If required parameter is invalid/empty
     * @throws FileNotWritableException If target file is not writable
     * @throws FileExistsException If target file already exists
     * @throws FileNotFoundException If one of the source files does not exist
     * @throws PdfIncompatibleException If XRef-Pointer is missing
     * @throws ShellCommandFailedException IF GhostScript fails to execute a shell command
     *
     * @return string Path to merged file; Same as parameter $targetFile if not null
     */
    public function merge(array $sourceFiles, $targetFile = null)
    {
        if ($targetFile === null) {
            $targetFile = realpath(sys_get_temp_dir()) . '/' . uniqid('pdfmerge_', false) . '.pdf';
        }

        try {
            $this->fpdi->mergeFiles($sourceFiles, $targetFile);
        } catch (PdfIncompatibleException $exception) {
            $this->gs->mergeFiles($sourceFiles, $targetFile);
        }

        return $targetFile;
    }

    /**
     * @param string $filePath
     *
     * @return bool
     */
    public function hasPdfXrefError($filePath)
    {
        return $this->fpdi->hasPdfXrefError($filePath);
    }
}
