<?php

namespace Xentral\Modules\Hubspot;

use Xentral\Modules\Hubspot\Exception\MetaException;

class HubspotMetaService
{

    private $name;
    private $content;
    private $extension;
    private $tmpDir;

    /**
     * @param string $tmpDir
     * @param string $ext
     */
    public function __construct(string $tmpDir, $ext = 'json')
    {
        $this->extension = $ext;
        $this->tmpDir = $tmpDir;
    }

    /**
     * @param string $name
     *
     * @throws MetaException
     * @return HubspotMetaService
     */
    public function setName(string $name): HubspotMetaService
    {
        if (empty($name)) {
            throw new MetaException('Name cannot be empty');
        }
        $this->name = preg_replace('/[^a-zA-Z]+/', '', $name);

        return $this;
    }

    /**
     * @throws MetaException
     *
     * @return string
     */
    private function getFullFileName(): string
    {
        $metaTmpDir = $this->tmpDir . 'meta';
        if (!is_dir($metaTmpDir) && !mkdir($metaTmpDir, 0777, true) && !is_dir($metaTmpDir)) {
            throw new MetaException(sprintf('Directory "%s" was not created', $metaTmpDir));
        }

        $metaTmpDir .= DIRECTORY_SEPARATOR . $this->name;
        if (!empty($this->extension)) {
            $metaTmpDir .= '.' . $this->extension;
        }

        return $metaTmpDir;
    }

    /**
     * @throws MetaException
     *
     * @return array
     */
    public function get(): array
    {
        if (!empty($this->content)) {
            return $this->content;
        }
        $fullFileName = $this->getFullFileName();
        $metaContent = @file_get_contents($fullFileName);

        $meta = json_decode($metaContent, true);

        if ($meta === null || (json_last_error() !== JSON_ERROR_NONE)
        ) {
            return [];
        }

        return $meta;
    }

    /**
     * @param array $data
     *
     * @throws MetaException
     *
     * @return bool
     */
    public function update($data = []): bool
    {
        if (!$this->save($data)) {
            return false;
        }
        $this->content = $data;
        return true;
    }

    /**
     * @param array $data
     *
     * @throws MetaException
     *
     * @return false|int
     */
    public function save($data = [])
    {
        return file_put_contents($this->getFullFileName(), json_encode($data));
    }

    /**
     * @throws MetaException
     * @return bool
     */
    public function exists(): bool
    {
        if (empty($this->name)) {
            throw new MetaException('Meta file name is not set');
        }

        return is_file($this->getFullFileName());
    }

    /**
     * @param $key
     *
     * @return bool
     */
    public function keyExists($key): bool
    {
        $meta = null;
        try {
            if ($this->exists()) {
                $meta = $this->get();
            }
        } catch (MetaException $exception) {
            return false;
        }

        return null !== $meta && array_key_exists($key, $meta);
    }

    /**
     * @throws MetaException
     *
     * @return bool
     */
    public function delete(): bool
    {
        if (!$this->exists()) {
            return false;
        }

        return @unlink($this->getFullFileName());
    }
}
