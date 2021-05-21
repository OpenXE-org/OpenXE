<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\CashPointClosing;

class CashPointClosingTransactionLineReference
{
    /** @var string $type */
    protected $type;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    /**
     * @param $apiResult
     *
     * @return static
     */
    public static function fromApiResult(object $apiResult)
    {
        return new self($apiResult->type);
    }

    /**
     * @param array $dbState
     *
     * @return CashPointClosingTransactionLineReference
     */
    public static function fromDbState(array $dbState)
    {
        return new self($dbState['type']);
    }

    public function toArray(): array
    {
        return ['type' => $this->type];
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
}
