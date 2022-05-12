<?php

namespace Xentral\Components\Http;

use DateTimeInterface;
use Xentral\Components\Http\Exception\FileNotFoundException;
use Xentral\Components\Http\Exception\InvalidArgumentException;
use Xentral\Components\Http\File\FileInfo;

class FileResponse extends Response
{
    /** @var FileInfo $file */
    protected $file;

    /** @var bool $deleteFileAfterDownload */
    protected $deleteFileAfterDownload = false;

    /**
     * Creates a Http response to send a file to the client.
     *
     * @param string $filePath       server-file to send
     * @param string $clientFileName filename for the download dialog
     * @param string $contentType    determined by mimetype of the file if not set
     * @param bool   $deleteAfterDownload true=remove the file when response was sent
     *
     * @return FileResponse
     */
    public static function createFromFile($filePath, $clientFileName, $contentType = null, $deleteAfterDownload = false)
    {
        $fileResponse = new self();
        $fileResponse->setContentFile($filePath, $deleteAfterDownload);
        if ($contentType === null) {
            $contentType = $fileResponse->file->getMimeType();
        }
        $fileResponse->setContentType($contentType);
        $fileResponse->setContentDisposition(self::DISPOSITION_ATTACHMENT, $clientFileName);

        return $fileResponse;
    }

    /**
     * Creates a file response with forced download header
     *
     * Useful for images and pdf.
     * Avoids that browsers open pdfs in viewer but download them directly.
     *
     * @param string $filePath
     * @param string $clientFileName
     * @param bool   $deleteAfterDownload
     *
     * @return FileResponse
     * @internal Content-Description -> optionaler MIME header https://tools.ietf.org/html/rfc1521#section-1
     *
     */
    public static function createForcedDownload($filePath, $clientFileName, $deleteAfterDownload = false)
    {
        $fileResponse = new self();
        $fileResponse->setContentFile($filePath, $deleteAfterDownload);
        $fileResponse->setContentDisposition(self::DISPOSITION_ATTACHMENT, $clientFileName);
        $fileResponse->setContentType('application/force-download');
        $fileResponse->addHeader('Content-Description', 'File Transfer');

        return $fileResponse;
    }

    /**
     * Sets the file as response body.
     *
     * @param string $contentFile file to send
     * @param bool   $deleteFile  delete file after response was sent
     */
    public function setContentFile($contentFile, $deleteFile = false)
    {
        $fileInfo = new FileInfo($contentFile, true);
        $this->file = $fileInfo;
        $this->deleteFileAfterDownload = $deleteFile;
        $this->setHeader('Content-Length', (string)filesize($fileInfo->getRealPath()));
        $this->setContentDisposition(self::DISPOSITION_ATTACHMENT, $fileInfo->getFilename());
    }

    /**
     * Send the FileResponse to the client.
     *
     * @param DateTimeInterface|null $sendTime  leave empty
     * @param int                    $chunkSize output content will be chunked
     */
    public function send(DateTimeInterface $sendTime = null, $chunkSize = 65536)
    {
        if ($this->file === null) {
            throw new FileNotFoundException('No content File Available');
        }
        if ($chunkSize < 1) {
            throw new InvalidArgumentException(sprintf('Invalid chunk size %s', $chunkSize));
        }
        parent::send($sendTime);
        $this->sendStreamedContent($chunkSize);

        if ($this->deleteFileAfterDownload) {
            unlink($this->file->getRealPath());
        }
    }

    /**
     * Returns the content File
     *
     * @return FileInfo|null
     */
    public function getContentFile()
    {
        return $this->file;
    }

    /**
     * Not available in FileResponse. Use setContentFile instead.
     *
     * @param string|null $content
     */
    public function setContent($content)
    {
    }

    /**
     * obsolete in FileResponse. Use getContentFile instead.
     *
     * @return FileInfo|null
     */
    public function getContent()
    {
        return $this->getContentFile();
    }

    /**
     * Send file content in portions to safe RAM.
     *
     * @param int $chunkSize
     */
    protected function sendStreamedContent($chunkSize = 65536)
    {
        $inStream = @fopen($this->file->getRealPath(), 'rb');
        if ($inStream === false) {
            throw new FileNotFoundException('Error reading file.');
        }

        while (!feof($inStream)) {
            $dataChunk = @fread($inStream, $chunkSize);
            if ($dataChunk === false) {
                throw new FileNotFoundException('Error reading file.');
            }
            echo $dataChunk;
        }
        fclose($inStream);
    }
}
