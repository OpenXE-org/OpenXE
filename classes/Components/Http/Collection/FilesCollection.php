<?php

namespace Xentral\Components\Http\Collection;

use ArrayIterator;
use Iterator;
use Xentral\Components\Http\File\FileUpload;

class FilesCollection implements Iterator
{
    /** @var ArrayIterator $iterator */
    protected $iterator;

    /**
     * @param array $files
     */
    public function __construct(array $files = [])
    {
        $files = $this->loadFilesArray($files);
        $this->iterator = new ArrayIterator($files);
    }

    /**
     * Returns all file uploads.
     *
     * @return FileUpload[]
     */
    public function all()
    {
        return $this->iterator->getArrayCopy();
    }

    /**
     * Returns true if there is a file upload entry with this name.
     *
     * @param string $name
     *
     * @return bool
     */
    public function has($name)
    {
        return $this->iterator->offsetExists($name);
    }

    /**
     * Get an file upload entry.
     *
     * @param string     $name
     * @param mixed|null $default
     *
     * @return FileUpload|mixed
     */
    public function get($name, $default = null)
    {
        return $this->has($name) ? $this->iterator->offsetGet($name) : $default;
    }

    /**
     * Returns true if all upload files are valid.
     *
     * @return bool true=all upload files are valid
     */
    public function allValid()
    {
        foreach ($this->all() as $name => $upload) {
            if (!$upload->isValid()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns true if at least one file upload has an error.
     *
     * @return bool true=there is at least one error
     */
    public function hasErrors()
    {
        foreach ($this->all() as $name => $upload) {
            if ($upload->hasError()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return the current element
     *
     * @return mixed FileUpload Object, null on failure
     */
    public function current()
    {
        return $this->iterator->current();
    }

    /**
     * Move forward to next element
     *
     * @return void
     */
    public function next()
    {
        $this->iterator->next();
    }

    /**
     * Return the key of the current element
     *
     * @return mixed on success, or null on failure.
     */
    public function key()
    {
        return $this->iterator->key();
    }

    /**
     * Checks if current position is valid
     *
     * @return boolean true on success or false on failure.
     */
    public function valid()
    {
        return $this->iterator->valid();
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->iterator->rewind();
    }

    /**
     * @param $fileEntries
     *
     * @return bool true=is alternate Array
     */
    protected function isAlternateArray($fileEntries)
    {
        if (
            !isset($fileEntries[array_keys($fileEntries)[0]]['tmp_name'])
            || is_array($fileEntries[array_keys($fileEntries)[0]]['tmp_name'])
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param array $fileEntries
     *
     * @return FileUpload[] $files
     */
    protected function loadFilesArray($fileEntries)
    {
        if (empty($fileEntries)) {
            return [];
        }
        if ($this->isAlternateArray($fileEntries)) {
            return $this->convertFilesArray($fileEntries);
        }

        $files = [];
        foreach ($fileEntries as $name => $upload) {
            if (empty($upload['tmp_name'])) {
                continue;
            }
            if (!is_array($upload['tmp_name'])) {
                $files[$name] = FileUpload::fromFilesArray($upload);
                continue;
            }
        }

        return $files;
    }

    /**
     * Recursively builds the array with FileUpload instances from alternative array format.
     *
     * tested up to 3rd level nesting
     *
     * @param array $files
     *
     * @return array
     */
    protected function convertFilesArray(array $files)
    {
        $totalUploads = [];
        foreach ($files as $name => $file) {
            $keys = array_keys($file);
            if (count(array_intersect($keys, ['name', 'tmp_name'])) === 2) {
                $uploads = [];
                foreach ($file['tmp_name'] as $k => $v) {
                    $upload = [
                        'tmp_name' => array_key_exists('tmp_name', $file) ? $file['tmp_name'][$k] : null,
                        'name'     => array_key_exists('name', $file) ? $file['name'][$k] : null,
                        'type'     => array_key_exists('type', $file) ? $file['type'][$k] : null,
                        'size'     => array_key_exists('size', $file) ? $file['size'][$k] : null,
                        'error'    => array_key_exists('error', $file) ? $file['error'][$k] : null,
                    ];
                    if (!empty($upload['tmp_name'])) {
                        $uploads[$k] = FileUpload::fromFilesArray($upload);
                    }
                }
                $totalUploads[$name] = $uploads;
            } else {
                if (!(isset($file['tmp_name']) && $file['tmp_name'] === '')) {
                    $totalUploads[$name] = $this->convertFilesArray($file);
                }
            }
        }

        return $totalUploads;
    }
}
