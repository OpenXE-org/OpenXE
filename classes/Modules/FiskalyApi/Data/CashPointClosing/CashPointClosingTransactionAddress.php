<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\CashPointClosing;

class CashPointClosingTransactionAddress
{
    /** @var string|null $street */
    private $street;

    /** @var string|null $postalCode */
    private $postalCode;

    /** @var string|null $city */
    private $city;

    /** @var string|null $countryCode */
    private $countryCode;

    /**
     * CashPointClosingTransactionAddress constructor.
     *
     * @param string|null $street
     * @param string|null $postalCode
     * @param string|null $city
     * @param string|null $countryCode
     */
    public function __construct(
        ?string $street = null,
        ?string $postalCode = null,
        ?string $city = null,
        ?string $countryCode = null
    ) {
        $this->street = $street;
        $this->postalCode = $postalCode;
        $this->city = $city;
        $this->countryCode = $countryCode;
    }

    /**
     * @param $apiResult
     *
     * @return static
     */
    public static function fromApiResult(object $apiResult): self
    {
        return new self(
            $apiResult->street ?? null,
            $apiResult->postal_code ?? null,
            $apiResult->city ?? null,
            $apiResult->country_code ?? null
        );
    }

    /**
     * @param array $dbState
     *
     * @return static
     */
    public static function fromDbState(array $dbState): self
    {
        return new self(
            $dbState['street'] ?? null,
            $dbState['postal_code'] ?? null,
            $dbState['city'] ?? null,
            $dbState['country_code'] ?? null
        );
    }

    /**
     * @return null[]|string[]
     */
    public function toArray(): array
    {
        $dbState = [];
        if ($this->street !== null) {
            $dbState['street'] = $this->getStreet();
        }
        if ($this->postalCode !== null) {
            $dbState['postal_code'] = $this->getPostalCode();
        }
        if ($this->city !== null) {
            $dbState['city'] = $this->getCity();
        }
        if ($this->countryCode !== null) {
            $dbState['country_code'] = $this->getCountryCode();
        }

        return $dbState;
    }

    /**
     * @return string|null
     */
    public function getStreet(): ?string
    {
        return $this->street;
    }

    /**
     * @param string|null $street
     */
    public function setStreet(?string $street): void
    {
        $this->street = $street;
    }

    /**
     * @return string|null
     */
    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    /**
     * @param string|null $postalCode
     */
    public function setPostalCode(?string $postalCode): void
    {
        $this->postalCode = $postalCode;
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param string|null $city
     */
    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    /**
     * @return string|null
     */
    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    /**
     * @param string|null $countryCode
     */
    public function setCountryCode(?string $countryCode): void
    {
        $this->countryCode = $countryCode;
    }


}
