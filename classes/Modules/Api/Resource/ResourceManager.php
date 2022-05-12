<?php

namespace Xentral\Modules\Api\Resource;

use Xentral\Components\Database\Database;
use Xentral\Modules\Api\Validator\Validator;

class ResourceManager
{
    /** @var Database $db */
    protected $db;

    /** @var Validator $validator */
    protected $validator;

    /** @var \Api $legacyApi */
    protected $legacyApi;

    /** @var array $resources Beinhaltet erzeugte Instanzen */
    protected $resources = [];

    /**
     * @param Database  $database
     * @param Validator $validator
     * @param \Api      $api
     */
    public function __construct($database, $validator, $api)
    {
        $this->db = $database;
        $this->validator = $validator;
        $this->legacyApi = $api;
    }

    /**
     * Resource erzeugen
     *
     * @param string $className
     *
     * @return AbstractResource
     */
    public function get($className)
    {
        $cleanName = $this->convertClassName($className);

        // Resource erzeugen falls noch nicht vorhanden
        if (!isset($this->resources[$cleanName])) {
            $this->resources[$cleanName] = new $className(
                $this->db,
                $this->validator
            );

            if ($className === ArticleResource::class) {
                $this->resources[$cleanName]->setLegacyApi($this->legacyApi);
            }
        }

        return $this->resources[$cleanName];
    }

    /**
     * @param string $className
     *
     * @return string
     */
    protected function convertClassName($className)
    {
        return str_replace('\\', '_', strtolower($className));
    }
}
