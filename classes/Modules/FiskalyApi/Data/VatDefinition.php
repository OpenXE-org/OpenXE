<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data;

class VatDefinition
{

    /** @var int */
    private $vatDefinitionExportId;

    /** @var string */
    private $type;

    /** @var string */
    private $env;

    /** @var float */
    private $percentage;

    /** @var string */
    private $description;

    /**
     * VatDefinition constructor.
     *
     * @param int    $vatDefinitionExportId
     * @param string $type
     * @param string $env
     * @param float  $percentage
     * @param string $description
     */
    public function __construct(
        int $vatDefinitionExportId,
        string $type,
        string $env,
        float $percentage,
        string $description
    ) {
        $this->vatDefinitionExportId = $vatDefinitionExportId;
        $this->type = $type;
        $this->env = $env;
        $this->percentage = $percentage;
        $this->description = $description;
    }

    /**
     * @param $apiResult
     *
     * @return static
     */
    public static function fromApiResult(object $apiResult): self
    {
        return new self(
            (int)$apiResult->vat_definition_export_id,
            $apiResult->_type,
            $apiResult->_env,
            (float)$apiResult->percentage,
            $apiResult->description
        );
    }

    /**
     * @return int
     */
    public function getVatDefinitionExportId(): int
    {
        return $this->vatDefinitionExportId;
    }

    /**
     * @param int $vatDefinitionExportId
     */
    public function setVatDefinitionExportId(int $vatDefinitionExportId): void
    {
        $this->vatDefinitionExportId = $vatDefinitionExportId;
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
    public function getEnv(): string
    {
        return $this->env;
    }

    /**
     * @param string $env
     */
    public function setEnv(string $env): void
    {
        $this->env = $env;
    }

    /**
     * @return float
     */
    public function getPercentage(): float
    {
        return $this->percentage;
    }

    /**
     * @param float $percentage
     */
    public function setPercentage(float $percentage): void
    {
        $this->percentage = $percentage;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
}
