<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data;

use stdClass;
use Xentral\Modules\FiskalyApi\Exception\InvalidArgumentException;

class MetaData
{
    /** @var array $metaData */
    private $metaData  = [];

    /**
     * MetaData constructor.
     *
     * @param array $metaData
     */
    public function __construct(array $metaData = [])
    {
        foreach($metaData as $key => $value) {
            $this->addMetaElement($key, $value);
        }
    }

    /**
     * @param array $dbState
     *
     * @return static
     */
    public static function fromDbState(array $dbState): self
    {
        return new self($dbState);
    }

    /**
     * @param $apiResult
     *
     * @return static
     */
    public static function fromApiResult(object $apiResult): self
    {
        $instance = new self();
        if(empty($apiResult)) {
            return $instance;
        }
        foreach($apiResult as $key => $value) {
            $instance->addMetaElement($key, $value);
        }

        return $instance;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->metaData;
    }

    /**
     * @return mixed|stdClass
     */
    public function toApiResult()
    {
        if(empty($this->metaData)) {
            return new stdClass();
        }

        return json_decode(json_encode($this->metaData));
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function addMetaElement(string $key, string $value): void
    {
        if(strlen($key) > 40) {
            throw new InvalidArgumentException('Meta Key must be less or equal 40 chracters');
        }
        if(strlen($value) > 500) {
            throw new InvalidArgumentException('Meta value must be less or equal 500 chracters');
        }
        if(count($this->metaData) >= 20 && array_key_exists($key, $this->metaData)) {
            throw new InvalidArgumentException('Maximum of 20 Meta-entries are allowed');
        }
        $this->metaData[$key] = $value;
    }

    /**
     * @param string $key
     *
     * @return $this
     */
    public function removeMetaElementByKey(string $key): self
    {
        if(array_key_exists($key, $this->metaData)) {
            unset($this->metaData[$key]);
        }

        return $this;
    }
}
