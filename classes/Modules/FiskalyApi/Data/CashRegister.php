<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data;

use Xentral\Modules\FiskalyApi\Exception\InvalidArgumentException;
use Xentral\Modules\FiskalyApi\Exception\InvalidCredentialsException;

class CashRegister
{
    /** @var string $clientId */
    private $clientId;

    /** @var string $type */
    private $type;

    /** @var string|null $tssId */
    private $tssId;

    /** @var string|null $masterClientId */
    private $masterClientId;

    /** @var string $brand */
    private $brand;

    /** @var string $model */
    private $model;

    /** @var string $baseCurrencyCode */
    private $baseCurrencyCode;

    /** @var string|null $softwareBrand */
    private $softwareBrand;

    /** @var string|null $softwareVersion */
    private $softwareVersion;

    /** @var bool|null $vatIdAvailable */
    private $vatIdAvailable;

    /** @var string|null $env */
    private $env;

    /**
     * CashRegister constructor.
     *
     * @param string      $type
     * @param string      $clientId
     * @param string|null $tssId
     * @param string|null $masterClientId
     * @param string      $brand
     * @param string      $model
     * @param string      $baseCurrencyCode
     * @param string|null $softwareBrand
     * @param string|null $softwareVersion
     * @param bool|null   $vatIdAvailable
     * @param string|null $env
     */
    public function __construct(
        string $type,
        string $clientId,
        ?string $tssId,
        ?string $masterClientId,
        string $brand,
        string $model,
        string $baseCurrencyCode = 'EUR',
        ?string $softwareBrand = null,
        string $softwareVersion = null,
        ?bool $vatIdAvailable = null,
        ?string $env = null
    ) {
        $this->clientId = $clientId;
        $this->type = $type;
        $this->tssId = $tssId;
        $this->masterClientId = $masterClientId;
        $this->brand = $brand;
        $this->model = $model;
        $this->softwareBrand = $softwareBrand;
        $this->baseCurrencyCode = $baseCurrencyCode;
        $this->softwareVersion = $softwareVersion;
        $this->vatIdAvailable = $vatIdAvailable;
        $this->env = $env;
        $this->ensureType($type, $tssId, $masterClientId);
    }

    /**
     * @param $apiResult
     *
     * @return static
     */
    public static function fromApiResult(object $apiResult): self
    {
        return new self(
            $apiResult->cash_register_type,
            $apiResult->client_id,
            $apiResult->tss_id ?? null,
            $apiResult->master_client_id ?? null,
            $apiResult->brand,
            $apiResult->model,
            $apiResult->base_currency_code ?? 'EUR',
            $apiResult->software->brand ?? null,
            $apiResult->software->version ?? null,
            isset($apiResult->processing_flags->UmsatzsteuerNichtErmittelbar)
                ? (bool)$apiResult->processing_flags->UmsatzsteuerNichtErmittelbar : null,
            $apiResult->_env
        );
    }

    /**
     * @return string[]
     */
    public function toArray(): array
    {
        $dbState = [
            'client_id'          => $this->getClientId(),
            'cash_register_type' => [
                'type' => $this->getType(),
            ],
        ];
        if ($this->getMasterClientId() !== null) {
            $dbState['cash_register_type']['master_client_id'] = $this->getMasterClientId();
        }
        if ($this->getTssId() !== null) {
            $dbState['cash_register_type']['tss_id'] = $this->getTssId();
        }
        $dbState['brand'] = $this->getBrand();
        $dbState['model'] = $this->getModel();
        if ($this->softwareBrand !== null) {
            $dbState['software']['brand'] = $this->getSoftwareBrand();
        }
        if ($this->softwareVersion !== null) {
            $dbState['software']['version'] = $this->getSoftwareVersion();
        }
        $dbState['base_currency_code'] = $this->getBaseCurrencyCode();

        return $dbState;
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
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * @param string $clientId
     */
    public function setClientId(string $clientId): void
    {
        $this->clientId = $clientId;
    }

    /**
     * @return string|null
     */
    public function getTssId(): ?string
    {
        return $this->tssId;
    }

    /**
     * @param string|null $tssId
     */
    public function setTssId(?string $tssId): void
    {
        $this->tssId = $tssId;
    }

    /**
     * @return string|null
     */
    public function getMasterClientId(): ?string
    {
        return $this->masterClientId;
    }

    /**
     * @param string|null $masterClientId
     */
    public function setMasterClientId(?string $masterClientId): void
    {
        $this->masterClientId = $masterClientId;
    }

    /**
     * @return string
     */
    public function getBrand(): string
    {
        return $this->brand;
    }

    /**
     * @param string $brand
     */
    public function setBrand(string $brand): void
    {
        $this->brand = $brand;
    }

    /**
     * @return string
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * @param string $model
     */
    public function setModel(string $model): void
    {
        $this->model = $model;
    }

    /**
     * @return string
     */
    public function getBaseCurrencyCode(): string
    {
        return $this->baseCurrencyCode;
    }

    /**
     * @param string $baseCurrencyCode
     */
    public function setBaseCurrencyCode(string $baseCurrencyCode): void
    {
        $this->baseCurrencyCode = $baseCurrencyCode;
    }

    /**
     * @return string|null
     */
    public function getSoftwareBrand(): ?string
    {
        return $this->softwareBrand;
    }

    /**
     * @param string|null $softwareBrand
     */
    public function setSoftwareBrand(?string $softwareBrand): void
    {
        $this->softwareBrand = $softwareBrand;
    }

    /**
     * @return string|null
     */
    public function getSoftwareVersion(): ?string
    {
        return $this->softwareVersion;
    }

    /**
     * @param string|null $softwareVersion
     */
    public function setSoftwareVersion(?string $softwareVersion): void
    {
        $this->softwareVersion = $softwareVersion;
    }

    /**
     * @return bool|null
     */
    public function getVatIdAvailable(): ?bool
    {
        return $this->vatIdAvailable;
    }

    /**
     * @param bool|null $vatIdAvailable
     */
    public function setVatIdAvailable(?bool $vatIdAvailable): void
    {
        $this->vatIdAvailable = $vatIdAvailable;
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
     * @param string      $type
     * @param string|null $tssId
     * @param string|null $masterClientId
     */
    private function ensureType(
        string $type,
        ?string $tssId,
        ?string $masterClientId
    ): void {
        switch ($type) {
            case 'MASTER':
                if ($tssId === null) {
                    throw new InvalidCredentialsException('ss_id must be not null');
                }

                return;
            case 'SLAVE_WITHOUT_TSS':
                if ($masterClientId === null) {
                    throw new InvalidCredentialsException('masterClientId must be not null');
                }

                return;
            case 'SLAVE_WITH_TSS':
                if ($tssId === null) {
                    throw new InvalidCredentialsException('ss_id must be not null');
                }
                if ($masterClientId === null) {
                    throw new InvalidCredentialsException('masterClientId must be not null');
                }

                return;
        }
        throw new InvalidArgumentException(
            "type {$type} is not valid. Allowed are 'MASTER', 'SLAVE_WITHOUT_TSS', 'SLAVE_WITH_TSS'"
        );
    }
}
