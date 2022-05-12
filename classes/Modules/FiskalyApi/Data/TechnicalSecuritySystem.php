<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data;

class TechnicalSecuritySystem
{
    /** @var string */
    private $uuid;

    /** @var string */
    private $description;

    /** @var string */
    private $state;

    /** @var string|null $env */
    private $env;

    /** @var string|null $organizationId */
    private $organizationId;

    /**
     * @param $apiResult
     *
     * @return static
     */
    public static function fromApiResult(object $apiResult): self
    {
        return new TechnicalSecuritySystem(
            $apiResult->_id,
            $apiResult->description,
            $apiResult->state,
            $apiResult->_env ?? null
        );
    }

    /**
     * @param array $dbState
     *
     * @return static
     */
    public static function fromDbState(array $dbState): self
    {
        return new TechnicalSecuritySystem(
            $dbState['_id'],
            $dbState['description'],
            $dbState['state'],
            $dbState['_env'] ?? null
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $dbState = [
            '_id' => $this->getUuid(),
            'description' => $this->getDescription(),
            'state' => $this->getState(),
        ];
        if($this->env !== null) {
            $dbState['_env'] = $this->getEnv();
        }

        return $dbState;
    }

    /**
     * TechnicalSecuritySystem constructor.
     *
     * @param string      $uuid
     * @param string      $description
     * @param string      $state
     * @param string|null $env
     */
    public function __construct(string $uuid, string $description = '', $state = 'ACTIVE', ?string $env = null)
    {
        $this->uuid = $uuid;
        $this->description = $description;
        $this->state = $state;
        $this->env = $env;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @return mixed
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @param mixed $uuid
     */
    public function setUuid($uuid): void
    {
        $this->uuid = $uuid;
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

    /**
     * @return string|null
     */
    public function getOrganizationId(): ?string
    {
        return $this->organizationId;
    }

    /**
     * @param string|null $organizationId
     */
    public function setOrganizationId(?string $organizationId): self
    {
        $this->organizationId = $organizationId;

        return $this;
    }
}
