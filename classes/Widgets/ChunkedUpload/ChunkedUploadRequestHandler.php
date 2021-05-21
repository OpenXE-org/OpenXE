<?php

namespace Xentral\Widgets\ChunkedUpload;

use Xentral\Components\Http\JsonResponse;
use Xentral\Components\Http\Request;
use Xentral\Components\Util\StringUtil;
use Xentral\Widgets\ChunkedUpload\Exception\ChunkedUploadExceptionInterface;
use Xentral\Widgets\ChunkedUpload\Exception\DecodingFailedException;
use Xentral\Widgets\ChunkedUpload\Exception\FilesystemErrorException;

final class ChunkedUploadRequestHandler
{
    /**
     * @param Request $request
     *
     * @return bool
     */
    public function canHandleRequest(Request $request)
    {
        if ($request->isCli() || !$request->isAjax()) {
            return false;
        }
        if (!$request->post->has('file_id') ||
            !$request->post->has('file_name') ||
            !$request->post->has('file_data') ||
            !$request->post->has('file_size')) {
            return false;
        }

        return strtolower($request->getHeader('Content-Type')) === 'application/x-www-form-urlencoded; charset=utf-8';
    }

    /**
     * @param Request $request
     * @param string  $tempDir Absolute path to directory, where the unfinished file will be stored
     * @param string  $saveDir Absolute path to directory, where the final upload will be stored
     *
     * @return JsonResponse
     */
    public function handleRequest(Request $request, $tempDir, $saveDir, $newFilename = null)
    {
        try {
            $bytesWritten = $this->handleUpload($request, $tempDir, $saveDir, $newFilename);
        } catch (ChunkedUploadExceptionInterface $exception) {
            return new JsonResponse([
                'success' => false,
                'error'   => $exception->getMessage(),
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        // Antwort zusammenbauen
        $responseData = [
            'success' => true,
            'file'    => [
                'id'    => $request->getPost('file_id'),
                'bytes' => $bytesWritten,
            ],
        ];

        // Beim ersten Request das Upload-Limit von PHP in der Antwort mitschicken
        // Erklärung:
        // Der erste Chunk ist bewusst klein gewählt (100KB), damit auf keinen Fall das Upload-Limit von PHP greift.
        // Die Antwort nach dem Upload des ersten Chunks enthält das Upload-Limit von PHP.
        // Der Uploader passt die Chunk-Size an, falls diese über dem Upload-Limit von PHP liegt.
        $fileOffset = $request->getPost('file_offset', null);
        if ($fileOffset !== null && (int)$fileOffset === 0) {
            $responseData['uploadLimit'] = $this->determineMaxUploadSize();
        }

        return new JsonResponse($responseData);
    }

    /**
     * @param Request     $request
     * @param string      $tempDir Absolute path to directory, where the unfinished file will be stored
     * @param string      $saveDir Absolute path to directory, where the final upload will be stored
     * @param string|null $newFileName
     *
     * @FilesystemErrorException
     *
     * @return int Bytes written
     */
    private function handleUpload(Request $request, $tempDir, $saveDir, $newFileName = null)
    {
        if (!is_dir($tempDir)) {
            throw new FilesystemErrorException(sprintf('Temporary upload directory does not exist: %s', $tempDir));
        }
        if (!is_dir($saveDir)) {
            throw new FilesystemErrorException(sprintf('Final storage directory does not exist: %s', $saveDir));
        }

        $fileId = $request->getPost('file_id');
        $fileName = $request->getPost('file_name');
        $fileData = $request->getPost('file_data');
        $fileSize = (int)$request->getPost('file_size');
        $fileHash = sha1(json_encode(['id' => $fileId, 'name' => $newFileName ?? $fileName, 'size' => $fileSize]));

        $tempPath = realpath($tempDir) . '/' . $fileHash;
        $savePath = realpath($saveDir) . '/' . ($newFileName ?? $fileName);
        $bytesWritten = $this->writeChunkData($tempPath, $fileData, $fileName);
        $tempSize = (int)@filesize($tempPath);

        if (file_exists($savePath)) {
            @unlink($tempPath);
            throw new FilesystemErrorException(sprintf(
                'Pre check failed. Final file already exists: %s', $savePath
            ));
        }

        if ($tempSize > $fileSize) {
            @unlink($tempPath);
            throw new FilesystemErrorException(sprintf(
                'Unknown Error: Temporary file is larger than uploaded file: %s', $tempPath
            ));
        }

        if ($tempSize === $fileSize) {
            $this->moveFinishedFile($tempPath, $savePath);
        }

        return $bytesWritten;
    }

    /**
     * @param string  $tempPath Absolute path to temp file
     * @param string  $savePath Absolute path to final file
     *
     * @throws FilesystemErrorException
     *
     * @return void
     */
    private function moveFinishedFile($tempPath, $savePath)
    {
        if (!file_exists($tempPath)) {
            throw new FilesystemErrorException(sprintf(
                'Failed to move temporary file to final location. Temp file is missing: %s', $tempPath
            ));
        }

        if (!@rename($tempPath, $savePath)) {
            @unlink($tempPath);
            throw new FilesystemErrorException(sprintf(
                'Failed to move temporary file to final location: %s', $savePath
            ));
        }

        @unlink($tempPath);
    }

    /**
     * @param string $tempPath Absolute path to temp file; chunk data will be appended
     * @param string $fileData Base64 encoded chunk data
     * @param string $fileName File name; without directory; Only needed for debugging
     *
     * @throws FilesystemErrorException
     *
     * @return int Bytes written
     */
    private function writeChunkData($tempPath, $fileData, $fileName)
    {
        $resource = @fopen($tempPath, 'a+b');
        if ($resource === false) {
            throw new FilesystemErrorException(sprintf('Can not open file for writing: %s', $tempPath));
        }

        $binaryData = $this->decodeChunkData($fileData, $fileName);
        $bytesWritten = @fwrite($resource, $binaryData);
        if ($bytesWritten === false) {
            @unlink($tempPath);
            throw new FilesystemErrorException(sprintf('Can not write to file: %s', $tempPath));
        }
        if (@fclose($resource) === false) {
            @unlink($tempPath);
            throw new FilesystemErrorException(sprintf('Could not close file pointer: %s', $tempPath));
        }

        return (int)$bytesWritten;
    }

    /**
     * @param string $data     Example 'data:application/octet-stream;base64,S0cJXKqx01pYOVeXbdtv...'
     * @param string $fileName File name; without directory; Only needed for debugging
     *
     * @throws DecodingFailedException
     *
     * @return string Decoded binary data chunk
     */
    private function decodeChunkData($data, $fileName)
    {
        $parts = explode(';base64,', $data);
        if (!is_array($parts) || !isset($parts[1])) {
            throw new DecodingFailedException(sprintf('Could not decode file upload #1. File name: %s', $fileName));
        }

        $binaryData = base64_decode($parts[1]);
        if ($binaryData === false) {
            throw new DecodingFailedException(sprintf('Could not decode file upload #2. File name: %s', $fileName));
        }

        return $binaryData;
    }

    /**
     * @return int Max upload size per file in bytes
     */
    private function determineMaxUploadSize()
    {
        $fileLimit = StringUtil::parsePhpByteSize(ini_get('upload_max_filesize'));
        $postLimit = StringUtil::parsePhpByteSize(ini_get('post_max_size'));
        $memLimit = StringUtil::parsePhpByteSize(ini_get('memory_limit'));

        return min($fileLimit, $postLimit, $memLimit);
    }
}
