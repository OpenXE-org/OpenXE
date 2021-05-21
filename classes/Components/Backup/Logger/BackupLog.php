<?php


namespace Xentral\Components\Backup\Logger;


use Xentral\Components\Backup\Exception\LogException;

final class BackupLog
{
    /** @var string|null $path */
    private $fullPath;

    /** @var string|null $storagePath */
    private $storagePath;

    /** @var string|null $fileName */
    private $fileName;

    /** @var string $persistentFile */
    private static $persistentFile = 'backup_logger.txt';

    public function __construct($path = null, $fileName = null)
    {
        if (null !== $path && null !== $fileName) {
            $this->fullPath = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $fileName;
        }
        $this->storagePath = $path;
        $this->fileName = $fileName;

    }

    /**
     * @param string      $message
     *
     * @param string|null $path
     * @param string|null $fileName
     *
     * @param bool        $withDate
     *
     * @param bool        $append
     *
     * @return false|int
     */
    public function write($message, $path = null, $fileName = null, $withDate = true, $append = true)
    {
        $flag = FILE_APPEND | LOCK_EX;

        $path = $this->getFullPath($path, $fileName);

        if (null === $path || (!file_exists($path) && !@touch($path))) {
            throw new LogException(sprintf('cannot access or create file %s', $path));
        }

        $message = $withDate === true ? time() . ': ' . $message : $message;
        if ($append === false) {
            $flag &= ~FILE_APPEND;
        }

        return file_put_contents($path, $message . "\n", $flag);
    }


    /**
     * @param int         $linePosition
     *
     * @param string|null $path
     *
     * @param string|null $fileName
     *
     * @return mixed|string
     */
    public function tail($linePosition = 0, $path = null, $fileName = null)
    {
        $path = $this->getFullPath($path, $fileName);

        if (null === $path || !file_exists($path)) {
            throw new LogException(sprintf('File %s cannot be found!', $path));
        }
        $output = '';
        if (($xData = file($path, FILE_SKIP_EMPTY_LINES)) && count($xData) > 0) {
            $key = (int)$linePosition === 0 ? count($xData) - 1 : $linePosition;
            if (!array_key_exists($key, $xData)) {
                throw new LogException(sprintf('Offset %d is missing', (int)$linePosition));
            }
            $output = $xData[$key];
        }

        return $output;
    }

    /**
     * @param string|null $path
     *
     * @param string|null $fileName
     *
     * @return bool|false|string
     */
    public function getContent($path = null, $fileName = null)
    {
        $path = $this->getFullPath($path, $fileName);

        if (null === $path || !file_exists($path)) {
            throw new LogException(sprintf('File %s cannot be found!', $path));
        }

        return file_get_contents($path);
    }

    /**
     * @param null        $path
     *
     * @param string|null $fileName
     *
     * @throws LogException
     * @return bool
     */
    public function delete($path = null, $fileName = null)
    {
        $path = $this->getFullPath($path, $fileName);
        if (null !== $path && !file_exists($path)) {
            return false;
            //throw new LogException(sprintf('File %s cannot be deleted!', $path));
        }

        return unlink($path);
    }

    /**
     * @param null $path
     * @param null $fileName
     *
     * @return string|null
     */
    private function getFullPath($path = null, $fileName = null)
    {
        if ($path === null && $fileName === null) {
            return $this->fullPath;
        }
        if ($path !== null && $fileName !== null) {
            return rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $fileName;
        }

        if ($fileName !== null && $path === null && $this->storagePath !== null) {
            return rtrim($this->storagePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $fileName;
        }

        if ($path !== null && $fileName === null && $this->fileName !== null) {
            return rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $this->fileName;
        }

        return null;
    }

    /**
     * @param string $message
     *
     * @return void
     */
    public function writePersistent($message)
    {
        $this->write($message, null, self::$persistentFile, true, false);
    }

    /**
     * @return string
     */
    public function getPersistentFileName()
    {
        return self::$persistentFile;
    }
}