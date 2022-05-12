<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data;

class User
{
    /** @var string */
    private $uuId;

    /** @var string */
    private $type;

    /** @var string */
    private $email;

    /** @var array */
    private $envs;

    /** @var string|null */
    private $firstName;

    /** @var string|null */
    private $lastName;

    /**
     * User constructor.
     *
     * @param string      $uuId
     * @param string      $type
     * @param string      $email
     * @param array       $envs
     * @param string|null $firstName
     * @param string|null $lastName
     */
    public function __construct(
        string $uuId,
        string $type,
        string $email,
        array $envs,
        ?string $firstName = null,
        ?string $lastName = null
    ) {
        $this->uuId = $uuId;
        $this->type = $type;
        $this->email = $email;
        $this->envs = $envs;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
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
            $apiResult->_type,
            $apiResult->email,
            $apiResult->_envs,
            $apiResult->first_name ?? null,
            $apiResult->last_name ?? null
        );
    }

    /**
     * @return string
     */
    public function getUuId(): string
    {
        return $this->uuId;
    }

    /**
     * @param string $uuId
     */
    public function setUuId(string $uuId): void
    {
        $this->uuId = $uuId;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return array
     */
    public function getEnvs(): array
    {
        return $this->envs;
    }

    /**
     * @param array $envs
     */
    public function setEnvs(array $envs): void
    {
        $this->envs = $envs;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string|null $firstName
     */
    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string|null $lastName
     */
    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }
}
