<?php

namespace Xentral\Components\Pdf\Merger;

use Xentral\Components\Pdf\Exception\FileExistsException;
use Xentral\Components\Pdf\Exception\FileNotFoundException;
use Xentral\Components\Pdf\Exception\FileNotWritableException;
use Xentral\Components\Pdf\Exception\InvalidArgumentException;
use Xentral\Components\Pdf\Exception\PdfComponentExceptionInterface;

abstract class AbstractPdfMerger
{
    /**
     * @param array|string[] $sourceFiles
     * @param string         $targetFile
     *
     * @throws PdfComponentExceptionInterface
     *
     * @return void
     */
    abstract public function mergeFiles(array $sourceFiles, $targetFile);

    /**
     * @param array|string[] $sourceFiles
     * @param string         $targetFile
     *
     * @throws InvalidArgumentException
     * @throws FileExistsException
     * @throws FileNotWritableException
     * @throws FileNotFoundException
     *
     * @return void
     */
    protected function preCheckMergeFiles(array $sourceFiles, $targetFile)
    {
        if (empty($sourceFiles)) {
            throw new InvalidArgumentException('Can not merge PDFs. Required parameter "sourceFiles" is empty.');
        }
        if (empty($targetFile)) {
            throw new InvalidArgumentException('Can not merge PDFs. Required parameter "$targetFile" is empty.');
        }
        if (is_file($targetFile)) {
            throw new FileExistsException(sprintf(
                'Can not merge PDFs. Target file "%s" already exists.', $targetFile
            ));
        }
        if (!$this->isFilePathWriteable($targetFile)) {
            throw new FileNotWritableException(sprintf(
                'Can not merge PDFs. Unable to create target file "%s" .', $targetFile
            ));
        }

        foreach ($sourceFiles as $sourceFile) {
            if (!is_file($sourceFile)) {
                throw new FileNotFoundException(sprintf(
                    'Can not merge PDFs. Source file "%s" does not exist.', $sourceFile
                ));
            }
        }
    }

    /**
     * @param string $filePath
     *
     * @return bool
     */
    protected function isFilePathWriteable($filePath)
    {
        $result = @fopen($filePath, 'wb');
        @unlink($filePath);

        return $result !== false;
    }
}
