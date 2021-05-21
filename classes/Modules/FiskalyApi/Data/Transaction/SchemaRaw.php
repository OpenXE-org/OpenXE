<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\Transaction;

class SchemaRaw
{
    /** @var string $processData */
    private $processData;

    /** @var string|null $processType */
    private $processType;

    /**
     * SchemaRaw constructor.
     *
     * @param string      $processData
     * @param string|null $processType
     */
    public function __construct(string $processData, ?string $processType = null)
    {
        $this->setProcessData($processData);
        $this->setProcessType($processType);
    }

    /**
     * @param $apiResult
     *
     * @return static
     */
    public static function fromApiResult(object $apiResult): self
    {
        return new self($apiResult->process_data, $apiResult->process_type ?? null);
    }

    /**
     * @param array $dbState
     *
     * @return static
     */
    public static function fromDbState(array $dbState): self
    {
        return new self($dbState['process_data'], $dbState['process_type'] ?? null);
    }

    /**
     * @return string[]
     */
    public function toArray(): array
    {
        $dbState = ['process_data' => $this->getProcessData()];
        if($this->processType !== null) {
            $dbState['process_type'] = $this->getProcessType();
        }

        return $dbState;
    }

    /**
     * @return string
     */
    public function getProcessData(): string
    {
        return $this->processData;
    }

    /**
     * @param string $processData
     */
    public function setProcessData(string $processData): void
    {
        $this->processData = $processData;
    }

    /**
     * @return string|null
     */
    public function getProcessType(): ?string
    {
        return $this->processType;
    }

    /**
     * @param string|null $processType
     */
    public function setProcessType(?string $processType): void
    {
        $this->processType = $processType;
    }
}
