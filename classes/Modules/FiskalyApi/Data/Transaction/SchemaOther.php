<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\Transaction;

class SchemaOther
{
    /**
     * SchemaOther constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param $apiResult
     *
     * @return static
     */
    public static function fromApiResult(object $apiResult): self
    {
        return new self();
    }

    /**
     * @param array $dbState
     *
     * @return static
     */
    public static function fromDbState(array $dbState): self
    {
        return new self();
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [];
    }
}
