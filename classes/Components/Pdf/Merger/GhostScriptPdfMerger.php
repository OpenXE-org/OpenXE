<?php

namespace Xentral\Components\Pdf\Merger;

use Xentral\Components\Pdf\Exception\PdfComponentExceptionInterface;
use Xentral\Components\Pdf\Exception\ShellCommandFailedException;

final class GhostScriptPdfMerger extends AbstractPdfMerger
{
    /**
     * @param array  $sourceFiles
     * @param string $targetFile
     *
     * @throws PdfComponentExceptionInterface
     *
     * @return void
     */
    public function mergeFiles(array $sourceFiles, $targetFile)
    {
        $this->preCheckMergeFiles($sourceFiles, $targetFile);
        if (!$this->isGhostscriptAvailable()) {
            throw new ShellCommandFailedException('Ghostscript command "gs" is missing or not callable.');
        }

        $sourceFilesParam = '';
        foreach ($sourceFiles as $sourceFile) {
            $sourceFilesParam .= escapeshellarg($sourceFile) . ' ';
        }

        $cmdTemplate = 'gs -dQUIET -dNOPAUSE -dBATCH ';
        $cmdTemplate .= '-dPDFSETTINGS=/prepress '; // High quality, color preserving, 300 dpi images
        $cmdTemplate .= '-dAutoRotatePages=/None -sDEVICE=pdfwrite -sOUTPUTFILE=%s %s';
        $cmd = sprintf($cmdTemplate, escapeshellarg($targetFile), $sourceFilesParam);

        $this->executeShellCommand($cmd);
    }

    /**
     * @param string $command
     *
     * @throws ShellCommandFailedException
     *
     * @return array|string[] Zeilenweise Ausgabe des Commands
     */
    private function executeShellCommand($command)
    {
        if ($this->isFunctionDisabled('exec')) {
            throw new ShellCommandFailedException('PHP function "exec" is disabled by php.ini settings.');
        }

        $output = [];
        @exec($command, $output, $returnVar);

        switch ($returnVar) {
            case 0:
                // Kein Fehler
                break;
            case 1:
                throw new ShellCommandFailedException('General error: ' . implode(' ', $output));
                break;
            case 126:
                throw new ShellCommandFailedException('Can not execute "gs" command.');
                break;
            case 127:
                throw new ShellCommandFailedException('Command "gs" not found.');
                break;
        }

        return $output;
    }

    /**
     * @return bool
     */
    private function isGhostscriptAvailable()
    {
        if ($this->isFunctionDisabled('shell_exec')) {
            throw new ShellCommandFailedException('PHP function "shell_exec" is disabled by php.ini settings.');
        }

        $result = @shell_exec('gs');

        return !empty($result);
    }

    /**
     * @param string $functionName PHP function name
     *
     * @return bool
     */
    public function isFunctionDisabled($functionName)
    {
        $functionName = trim($functionName);
        $disabledFunctions = explode(',', ini_get('disable_functions'));
        foreach ($disabledFunctions as $disabledFunction) {
            if (trim($disabledFunction) === $functionName) {
                return true;
            }
        }

        return false;
    }
}
