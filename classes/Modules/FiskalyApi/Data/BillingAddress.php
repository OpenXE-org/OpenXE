<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data;

class BillingAddress
{
    /** @var string $uuid */
    private $uuid;

    /** @var string $type */
    private $type;

    /** @var array $envs */
    private $envs;

    /** @var string $name */
    private $name;

    /** @var string $addressLine1 */
    private $addressLine1;

    /** @var string|null $addressLine2 */
    private $addressLine2;

    /** @var string $zip */
    private $zip;

    /** @var string $town */
    private $town;

    /** @var string $countryCode */
    private $countryCode;

    /** @var string|null $displayName */
    private $displayName;

    /** @var string|null $vatId */
    private $vatId;

    /** @var bool|null $isVatIdValid */
    private $isVatIdValid;

    /**
     * BillingAddress constructor.
     *
     * @param string      $uuId
     * @param string      $type
     * @param array       $envs
     * @param string      $recipient
     * @param string      $addressLine1
     * @param string      $zip
     * @param string      $town
     * @param string      $countryCode
     * @param string|null $addressLine2
     * @param string|null $displayName
     * @param string|null $vatId
     * @param bool|null   $isVatIdValid
     */
    public function __construct(
        string $uuId,
        string $type,
        array $envs,
        string $recipient,
        string $addressLine1,
        string $zip,
        string $town,
        string $countryCode,
        ?string $addressLine2 = null,
        ?string $displayName = null,
        ?string $vatId = null,
        ?bool $isVatIdValid = null
    ) {
        $this->uuid = $uuId;
        $this->type = $type;
        $this->envs = $envs;
        $this->name = $recipient;
        $this->addressLine1 = $addressLine1;
        $this->zip = $zip;
        $this->town = $town;
        $this->countryCode = $countryCode;
        $this->addressLine2 = $addressLine2;
        $this->displayName = $displayName;
        $this->vatId = $vatId;
        $this->isVatIdValid = $isVatIdValid;
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
            $apiResult->_envs,
            $apiResult->recipient,
            $apiResult->address_line1,
            $apiResult->zip,
            $apiResult->town,
            $apiResult->country_code,
            $apiResult->address_line2 ?? null,
            $apiResult->display_name ?? null,
            $apiResult->vat_id ?? null,
            isset($apiResult->vat_id_valid) ? (bool)$apiResult->vat_id_valid : null
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
     * @param string $uuid
     */
    public function setUuid(string $uuid): void
    {
        $this->uuid = $uuid;
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
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getAddressLine1(): string
    {
        return $this->addressLine1;
    }

    /**
     * @param string $addressLine1
     */
    public function setAddressLine1(string $addressLine1): void
    {
        $this->addressLine1 = $addressLine1;
    }

    /**
     * @return string|null
     */
    public function getAddressLine2(): ?string
    {
        return $this->addressLine2;
    }

    /**
     * @param string|null $addressLine2
     */
    public function setAddressLine2(?string $addressLine2): void
    {
        $this->addressLine2 = $addressLine2;
    }

    /**
     * @return string
     */
    public function getZip(): string
    {
        return $this->zip;
    }

    /**
     * @param string $zip
     */
    public function setZip(string $zip): void
    {
        $this->zip = $zip;
    }

    /**
     * @return string
     */
    public function getTown(): string
    {
        return $this->town;
    }

    /**
     * @param string $town
     */
    public function setTown(string $town): void
    {
        $this->town = $town;
    }

    /**
     * @return string
     */
    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    /**
     * @param string $countryCode
     */
    public function setCountryCode(string $countryCode): void
    {
        $this->countryCode = $countryCode;
    }

    /**
     * @return string|null
     */
    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    /**
     * @param string|null $displayName
     */
    public function setDisplayName(?string $displayName): void
    {
        $this->displayName = $displayName;
    }

    /**
     * @return string|null
     */
    public function getVatId(): ?string
    {
        return $this->vatId;
    }

    /**
     * @param string|null $vatId
     */
    public function setVatId(?string $vatId): void
    {
        $this->vatId = $vatId;
    }

    /**
     * @return bool|null
     */
    public function getIsVatIdValid(): ?bool
    {
        return $this->isVatIdValid;
    }

    /**
     * @param bool|null $isVatIdValid
     */
    public function setIsVatIdValid(?bool $isVatIdValid): void
    {
        $this->isVatIdValid = $isVatIdValid;
    }
}
