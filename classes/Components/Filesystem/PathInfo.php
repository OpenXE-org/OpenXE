<?php

namespace Xentral\Components\Filesystem;

use League\Flysystem\Util;
use Xentral\Components\Filesystem\Exception\InvalidArgumentException;

final class PathInfo
{
    const TYPE_FILE = 'file';
    const TYPE_DIR = 'dir';

    /** @var array $readonlyProperties */
    private static $readonlyProperties = [
        'type',
        'path',
        'dirname',
        'filename',
        'basename',
        'extension',
        'timestamp',
        'size',
    ];

    /** @var array $data */
    private $data;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        // required
        $data['type'] = (string)$data['type'];
        $data['path'] = (string)$data['path'];
        $data['dirname'] = (string)$data['dirname'];
        $data['filename'] = (string)$data['filename'];
        $data['basename'] = (string)$data['basename'];

        // optional
        $data['extension'] = !empty($data['extension']) ? (string)$data['extension'] : null;
        $data['timestamp'] = is_numeric($data['timestamp']) ? (int)$data['timestamp'] : null;
        $data['size'] = is_numeric($data['size']) ? (int)$data['size'] : null;

        $this->data = $data;
    }

    /**
     * @param array $metainfo
     *
     * @return PathInfo
     */
    public static function fromMeta(array $metainfo = [])
    {
        $pathinfo = Util::pathinfo($metainfo['path']);

        return new PathInfo(array_merge($pathinfo, $metainfo));
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * @return bool
     */
    public function isFile()
    {
        return $this->getType() === self::TYPE_FILE;
    }

    /**
     * @return bool
     */
    public function isDir()
    {
        return $this->getType() === self::TYPE_DIR;
    }

    /**
     * @return string [dir|file]
     */
    public function getType()
    {
        return $this->data['type'];
    }

    /**
     * @return string Filename with extension and without path
     */
    public function getBasename()
    {
        return $this->data['basename'];
    }

    /**
     * @return string Filename without extension and without path
     */
    public function getFilename()
    {
        return $this->data['filename'];
    }

    /**
     * @return string|null File extension or null if directory
     */
    public function getExtension()
    {
        return $this->data['extension'];
    }

    /**
     * @return string Path without filename
     */
    public function getDir()
    {
        return $this->data['dirname'];
    }

    /**
     * Relativer Pfad zum Mountpoint
     *
     * @return string Path with filename
     */
    public function getPath()
    {
        return $this->data['path'];
    }

    /**
     * @return int|null File size or null if not available
     */
    public function getSize()
    {
        return $this->data['size'];
    }

    /**
     * @return int|null Last updated timestamp or null if not available
     */
    public function getTimestamp()
    {
        return $this->data['timestamp'];
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function has($name)
    {
        return isset($this->data[(string)$name]);
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function get($name)
    {
        if ($this->has($name)) {
            return $this->data[(string)$name];
        }

        return null;
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public function set($name, $value)
    {
        if (in_array((string)$name, self::$readonlyProperties, true)) {
            throw new InvalidArgumentException(sprintf('Property "%s" is readonly.', $name));
        }

        $this->data[(string)$name] = $value;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return $this->has($name);
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return void
     */
    public function __set($name, $value)
    {
        $this->set($name, $value);
    }
}
