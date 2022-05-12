<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\Transaction;

use stdClass;

class TransactionSchema
{
    /** @var SchemaStandardV1|null $standardV1 */
    private $standardV1;

    /** @var SchemaRaw|null $raw */
    private $raw;

    public function __construct(?SchemaStandardV1 $standardV1, ?SchemaRaw $raw = null)
    {
        $this->setStandardV1($standardV1);
        $this->setRaw($raw);
    }

    /**
     * @param $apiResult
     *
     * @return static
     */
    public static function fromApiResult(object $apiResult): self
    {
        return new self(
            empty($apiResult->standard_v1) ? null : SchemaStandardV1::fromApiResult($apiResult->standard_v1),
            empty($apiResult->raw) ? null : SchemaRaw::fromApiResult($apiResult->raw)
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
            empty($dbState['standard_v1']) ? null : SchemaStandardV1::fromDbState($dbState['standard_v1']),
            empty($dbState['raw']) ? null : SchemaRaw::fromDbState($dbState['raw'])
        );
    }

    /**
     * @return string[]
     */
    public function toArray(): array
    {
        $dbState = [];
        if ($this->standardV1 !== null) {
            $dbState['standard_v1'] = $this->standardV1->toArray();
        }
        if ($this->raw !== null) {
            $dbState['raw'] = $this->raw->toArray();
        }

        return $dbState;
    }

    /**
     * @return stdClass
     */
    public function toApiResult()
    {
        $apiResult = new stdClass();
        if ($this->standardV1 !== null) {
            $apiResult->standard_v1 = json_decode(json_encode($this->standardV1->toArray()));
        }
        if ($this->raw !== null) {
            $apiResult->raw = json_decode(json_encode($this->raw->toArray()));
        }

        return $apiResult;
    }

    /**
     * @return SchemaStandardV1|null
     */
    public function getStandardV1(): ?SchemaStandardV1
    {
        return $this->standardV1 === null ? null : SchemaStandardV1::fromDbState($this->standardV1->toArray());
    }

    /**
     * @param SchemaStandardV1|null $standardV1
     */
    public function setStandardV1(?SchemaStandardV1 $standardV1): void
    {
        $this->standardV1 = $standardV1 === null ? null : SchemaStandardV1::fromDbState($standardV1->toArray());
    }

    /**
     * @return SchemaRaw|null
     */
    public function getRaw(): ?SchemaRaw
    {
        return $this->raw === null ? null : SchemaRaw::fromDbState($this->raw->toArray());
    }

    /**
     * @param SchemaRaw|null $raw
     */
    public function setRaw(?SchemaRaw $raw): void
    {
        $this->raw = $raw === null ? null : SchemaRaw::fromDbState($raw->toArray());
    }
}
