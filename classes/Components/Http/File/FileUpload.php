<?php

namespace Xentral\Components\Http\File;

use Xentral\Components\Http\Exception\FileExistsException;
use Xentral\Components\Http\Exception\InvalidArgumentException;
use Xentral\Components\Http\Exception\NoUploadErrorException;
use Xentral\Components\Util\StringUtil;

class FileUpload extends FileInfo
{
    /** @var string $clientFileName Original file name on client side (without path) */
    protected $clientFileName;

    /** @var string $clientMimeType Mime type on client side */
    protected $clientMimeType;

    /** @var int|null $clientSize */
    protected $clientSize;

    /** @var int $errorCode Error code */
    protected $errorCode;

    /**
     * @param string      $filePath
     * @param string      $clientName
     * @param string|null $mimeType
     * @param int|null    $fileSize
     * @param int|null    $errorCode
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        $filePath,
        $clientName,
        $mimeType = null,
        $fileSize = null,
        $errorCode = null
    ) {
        if (empty($filePath)) {
            throw new InvalidArgumentException('File upload information is invalid. File path is missing.');
        }
        if (empty($clientName)) {
            throw new InvalidArgumentException('File upload information is invalid. Original file name is missing.');
        }

        parent::__construct((string)$filePath);

        $this->clientFileName = (string)$clientName;
        $this->clientMimeType = $mimeType !== null ? $mimeType : 'application/octet-stream';
        $this->clientSize = $fileSize;
        $this->errorCode = $errorCode !== null ? (int)$errorCode : UPLOAD_ERR_OK;
    }

    /**
     * @param array $file
     *
     * @return FileUpload
     */
    public static function fromFilesArray(array $file)
    {
        foreach (['tmp_name', 'name', 'type', 'size', 'error'] as $key) {
            if (!array_key_exists($key, $file)) {
                $file[$key] = null;
            }
        }

        return new self($file['tmp_name'], $file['name'], $file['type'], $file['size'], $file['error']);
    }

    /**
     * Client file size may not be set. Use self::getSize()
     *
     * @return int|null File size
     */
    public function getClientSize()
    {
        return $this->clientSize;
    }

    /**
     * Client mime type may not correct. Use self::getMimeType()
     *
     * @return string 'application/octet-stream' if not set
     */
    public function getClientMimeType()
    {
        return $this->clientMimeType;
    }

    /**
     * Returns true if an error other than 0 exists.
     *
     * @return bool
     */
    public function hasError()
    {
        return $this->errorCode !== UPLOAD_ERR_OK;
    }

    /**
     * Returns error message.
     *
     * @throws NoUploadErrorException
     *
     * @return string error message
     */
    public function getErrorMessage()
    {
        if (!$this->hasError()) {
            throw new NoUploadErrorException('There is no error message. Please call first hasError()');
        }

        switch ($this->getErrorCode()) {
            case UPLOAD_ERR_INI_SIZE:
                $message = sprintf(
                    'Die Datei "%s" überschreitet die \'upload_max_filesize\' Einstellung (%s) der in php.ini.',
                    $this->getClientFileName(),
                    ini_get('upload_max_filesize')// @todo format using Stringutils
                );
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $message = sprintf(
                    'Der Datei "%s" überschreitet die MAX_FILE_SIZE Einstellung des HTML-Formulars.',
                    $this->getClientFileName()
                );
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = sprintf(
                    'Die Datei "%s" wurde nicht vollständig übertragen.',
                    $this->getClientFileName()
                );
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = 'Es wurde keine Datei ausgewählt.';
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = 'Temporärer Ordner fehlt.';
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = sprintf(
                    'Die Datei "%s" konnte nicht abgespeichert werden.',
                    $this->getClientFileName()
                );
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = 'Der Upload wurde durch eine PHP-Erweiterung gestoppt.';
                break;
            default:
                $message = 'Unbekannter Upload-Fehler.';
                break;
        }

        return $message;
    }

    /**
     * Returns file upload error code.
     *
     * @see http://php.net/manual/de/features.file-upload.errors.php
     *
     * @return int File upload error code
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * Returns the client's file name
     *
     * @return string file name on client side
     */
    public function getClientFileName()
    {
        return $this->clientFileName;
    }

    /**
     * Returns the content of the file.
     *
     * @return string file contents
     */
    public function getContent()
    {
        return file_get_contents($this->getRealPath());
    }

    /**
     * Opens a readonly stream to the file.
     *
     * @return resource file contents as stream
     */
    public function createContentStream()
    {
        return fopen($this->getRealPath(), 'rb');
    }

    /**
     * Returns true if the file is an image.
     *
     * @return bool
     */
    public function isImage()
    {
        return in_array(
            $this->getMimeType(),
            ['image/jpg', 'image/jpeg', 'image/png', 'image/gif', 'image/tiff', 'image/tif'],
            true
        );
    }

    /**
     * Returns true if the file is a Pdf file.
     *
     * @return bool
     */
    public function isPdf()
    {
        return $this->getMimeType() === 'application/pdf';
    }

    /**
     * Return true if file is valid
     *
     * @return bool
     */
    public function isValid()
    {
        return is_uploaded_file($this->getRealPath());
    }

    /**
     * Moves file to specific location.
     *
     * @param string      $targetDir
     * @param string|null $targetName
     *
     * @return FileInfo file at new location
     */
    public function move($targetDir, $targetName = null)
    {
        if (!is_dir($targetDir)) {
            throw new InvalidArgumentException(
                sprintf('The target directory "%s" does not exist or is no directory.', $targetDir)
            );
        }
        if ($targetName === '') {
            throw new InvalidArgumentException('The target file name can not be empty.');
        }
        if ($targetName === null) {
            $targetName = StringUtil::toFilename( $this->getClientFileName());
        }

        $targetFilePath = $targetDir . '/' . $targetName;

        if (file_exists($targetFilePath)) {
            throw new FileExistsException(
                sprintf('Cannot move file. Target file "%s" already exists', $targetFilePath)
            );
        }

        move_uploaded_file($this->getRealPath(), $targetFilePath);

        return new FileInfo($targetFilePath);
    }
}
