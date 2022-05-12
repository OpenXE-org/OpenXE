<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data;

class Client
{
    /** @var string */
    private $uuid;

    /** @var string */
    private $serialNumber;

    /** @var string|null $tssId */
    private $tssId;

    /** @var string|null $env */
    private $env;

    /**
     * Client constructor.
     *
     * @param string      $uuid
     * @param string      $serialNumber
     * @param string|null $tssId
     * @param string|null $env
     */
    public function __construct(string $uuid, string $serialNumber, ?string $tssId = null, ?string $env = null)
    {
        $this->uuid = $uuid;
        $this->serialNumber = $serialNumber;
        $this->tssId = $tssId;
        $this->env = $env;
    }

    /**
     * @param $apiResult
     *
     * @return static
     */
    public static function fromApiResult(object $apiResult): self
    {
        return new self(
            $apiResult->_id,
            $apiResult->serial_number,
            $apiResult->tss_id,
            $apiResult->_env ?? null
        );
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function getSerialNumber(): string
    {
        return $this->serialNumber;
    }

    /**
     * @return string|null
     */
    public function getTssId(): ?string
    {
        return $this->tssId;
    }

    /**
     * @return string|null
     */
    public function getEnv(): ?string
    {
        return $this->env;
    }

    /**
     * @param string|null $env
     */
    public function setEnv(?string $env): void
    {
        $this->env = $env;
    }
}
