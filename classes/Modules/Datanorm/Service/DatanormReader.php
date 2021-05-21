<?php

declare(strict_types=1);

namespace Xentral\Modules\Datanorm\Service;

use Generator;
use Xentral\Components\Filesystem\Exception\FileNotFoundException;
use Xentral\Components\Filesystem\FilesystemInterface;
use Xentral\Components\Filesystem\PathInfo;
use Xentral\Modules\Datanorm\Data\DatanormTypeDataInterface;
use Xentral\Modules\Datanorm\Exception\FileSystemException;
use Xentral\Modules\Datanorm\Exception\InvalidLineException;
use Xentral\Modules\Datanorm\Exception\NoAddressIdFoundException;
use Xentral\Modules\Datanorm\Exception\WrongDiscountFormatException;
use Xentral\Modules\Datanorm\Exception\WrongPriceFormatException;
use Xentral\Modules\Datanorm\Exception\WrongVersionException;
use Xentral\Modules\Datanorm\Handler\DatanormReaderHandlerInterface;


final class DatanormReader
{
    /** @var FilesystemInterface $filesystem */
    private $filesystem;

    /** @var string $uploadDir */
    private $uploadDir;

    /** @var DatanormReaderHandlerInterface[] $readerHandlers */
    private $readerHandlers;

    /** @var int[] $readerVersions */
    private $readerVersions;

    /** @var DatanormIntermediateService $intermediateService */
    private $intermediateService;

    /**
     * @param FilesystemInterface              $filesystem
     * @param DatanormIntermediateService      $intermediateService
     * @param DatanormReaderHandlerInterface[] $readerHandlers
     * @param string                           $uploadDir Relative dir to filesystem-class root
     */
    public function __construct(
        FilesystemInterface $filesystem,
        DatanormIntermediateService $intermediateService,
        array $readerHandlers,
        $uploadDir
    ) {
        $this->filesystem = $filesystem;
        $this->uploadDir = $uploadDir;
        $this->readerHandlers = [];
        $this->intermediateService = $intermediateService;

        foreach ($readerHandlers as $r) {
            $this->readerVersions[] = $r->getVersion();
            $this->readerHandlers[] = $r;
        }

        if (!$filesystem->has($this->uploadDir)) {
            $filesystem->createDir($this->uploadDir);
        }
    }

    /**
     * @param PathInfo $file
     * @param int      $limit
     * @param int      $lastLineNumber
     *
     * @throws FileNotFoundException
     * @throws WrongVersionException
     * @throws InvalidLineException
     * @throws FileSystemException
     * @throws WrongPriceFormatException
     * @throws WrongDiscountFormatException
     * @throws NoAddressIdFoundException
     *
     * @return int
     */
    public function read(PathInfo $file, int $limit, int $lastLineNumber): int
    {
        $iterator = $this->getFileIterator($file->getPath());
        $counter = 0;
        $nextLastLineNumber = $lastLineNumber + $limit;
        $intermediateEntries = [];
        $version = 0;

        foreach ($iterator as $line) {
            if ($counter === 0) {
                $version = $this->getVersionByTypV($line);
            }

            if ($counter >= $lastLineNumber && $counter < $nextLastLineNumber) {
                $type = $this->getLineType($line);
                $obj = $this->parseLine($line, $version);
                if (!empty($obj)) {
                    $isEnrich = false;
                    if ($type === 'A' || $type === 'P') {
                        $isEnrich = $this->needsEnrichement($type, $obj, $version);
                    } elseif ($type === 'E' && $version === 4) {
                        $type = 'T';
                    }

                    $articleNumber = '';
                    if (method_exists($obj, 'getArticleNumber')) {
                        $articleNumber = $obj->getArticleNumber();
                    } elseif (method_exists($obj, 'getTextnumber')) {
                        $articleNumber = $obj->getTextnumber();
                    }

                    $intermediateEntries[] = [
                        'fileName'        => $file->getFilename(),
                        'type'            => $type,
                        'obj'             => $obj,
                        'nummer'          => $articleNumber,
                        'enrich'          => $isEnrich,
                        'directory'       => $file->getDir(),
                        'user_address_id' => $this->getUserIdFromPath($file->getFilename()),
                    ];
                }
            }
            $counter++;
        }

        if (count($intermediateEntries) > 0) {
            $this->intermediateService->writeMultiple($intermediateEntries);
        }

        if ($nextLastLineNumber - $counter >= 0) {
            $isDeleted = $this->deleteFile($file->getPath());

            if (!$isDeleted) {
                $scriptOwner = @posix_getpwuid(@fileowner(__FILE__));
                $scriptGroup = @posix_getgrgid(@filegroup(__FILE__));
                $phpUsername = get_current_user();

                $msg =
                    'Could not delete file: ' . $file->getPath() .
                    ', scriptowner: ' . $scriptOwner[0] .
                    ', scriptGroup: ' . $scriptGroup[0] .
                    ', phpUsername: ' . $phpUsername;

                throw new FileSystemException($msg);
            }
            $nextLastLineNumber = -1;
        }

        return $nextLastLineNumber;
    }


    /**
     * @param string $fileName
     *
     * @return int
     */
    private function getUserIdFromPath(string $fileName): int
    {
        $pos = strstr($fileName, '_', true);
        if (strstr($fileName, '_') === false) {
            throw new NoAddressIdFoundException('No user-id found in filename: ' . $fileName);
        } else {
            return (int)$pos;
        }
    }

    /**
     * @param string $line
     * @param int    $version
     *
     * @throws WrongVersionException
     * @throws WrongPriceFormatException
     * @throws WrongDiscountFormatException
     *
     * @return null|DatanormTypeDataInterface
     */
    private function parseLine(string $line, int $version): ?DatanormTypeDataInterface
    {
        $readerHandler = $this->getReaderHandler($version);

        $line = iconv('CP850', 'UTF-8', $line);

        $obj = null;
        $type = $this->getLineType($line);
        switch ($type) {
            case 'A':
                $obj = $readerHandler->transformToTypeA($line);
                break;
            case'P':
                $obj = $readerHandler->transformToTypeP($line);
                break;
            case'V':
                $obj = $readerHandler->transformToTypeV($line);
                break;
            case'B':
                $obj = $readerHandler->transformToTypeB($line);
                break;
            case'E':
                if ($version === 4) {
                    $obj = $readerHandler->transformToTypeT($line);
                }
                break;
            case'T':
                $obj = $readerHandler->transformToTypeT($line);
                break;
            case'D':
                $obj = $readerHandler->transformToTypeD($line);
                break;
        }

        return $obj;
    }

    /**
     * @param int $version
     *
     * @throws WrongVersionException
     *
     * @return DatanormReaderHandlerInterface
     */
    private function getReaderHandler(int $version): DatanormReaderHandlerInterface
    {
        $readerHandler = null;
        foreach ($this->readerHandlers as $r) {
            if ($r->getVersion() === $version) {
                $readerHandler = $r;
                break;
            }
        }

        if (empty($readerHandler)) {
            throw new WrongVersionException(
                'The DATANORM-Version is not supported. Only ' .
                implode(', ', $this->readerVersions) . ' are allowed. Requested version was: ' . $version
            );
        }

        return $readerHandler;
    }

    /**
     * @param string $filePath Relative path to filesystem-class root
     *
     * @throws FileNotFoundException
     *
     * @return Generator
     */
    private function getFileIterator(string $filePath): Generator
    {
        $stream = $this->filesystem->readStream($filePath);

        while ($line = fgets($stream)) {
            yield $line;
        }

        if (is_resource($stream)) {
            fclose($stream);
        }
    }

    /**
     * @param string $line
     *
     * @throws InvalidLineException
     *
     * @return string
     */
    private function getLineType(string $line): string
    {
        $lineType = substr(trim($line), 0, 1);

        if ($lineType === false || empty($lineType)) {
            throw new InvalidLineException('Unknown linetype in this line: ' . $line);
        }

        return $lineType;
    }

    /**
     * @param string $line
     *
     * @throws WrongVersionException
     *
     * @return int
     */
    private function getVersionByTypV(string $line): int
    {
        $v5indicator = false;
        $split = explode(';', $line);
        if (isset($split[1])) {
            $v5indicator = $split[1] === '050';
        }

        $v4indicator = false;
        if (strlen($line) > 123) {
            $v4indicator = trim(substr($line, 123, 2)) === '04';
        }


        if ($v5indicator) {
            return 5;
        } elseif ($v4indicator) {
            return 4;
        }

        throw new WrongVersionException('DATANORM-Version not found.');
    }

    /**
     * @param string $path
     *
     * @throws FileNotFoundException
     *
     * @return bool
     */
    public function deleteFile(string $path): bool
    {
        return $this->filesystem->delete($path);
    }

    /**
     * @return array|PathInfo[]
     */
    public function listUploadedDatanormFiles(): array
    {
        return $this->filesystem->listFiles($this->uploadDir);
    }

    /**
     * @param string                    $type
     * @param DatanormTypeDataInterface $object
     * @param int                       $version
     *
     * @return bool
     */
    private function needsEnrichement(string $type, DatanormTypeDataInterface $object, int $version): bool
    {
        if ($version === 4 && $type === 'P') {
            return true;
        }

        if ($type === 'A' && method_exists($object, 'getTextkey')) {
            $textFlag = '0';

            if (!empty($object->getTextkey())) {
                $textFlag = substr($object->getTextkey(), 0, 1);
            }

            if ($textFlag != '0') {
                return true;
            }
        }

        return false;
    }


}
