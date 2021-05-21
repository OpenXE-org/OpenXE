<?php

namespace Xentral\Modules\AmazonVendorDF\Data;

class Quantity
{
    /** @var int */
    private $amount;
    /** @var string */
    private $unitOfMeasure;
    /** @var int */
    private $unitSize;

    public function __construct(int $amount, string $unitOfMeasure = 'Each', ?int $unitSize = 1)
    {
        $this->amount = $amount;
        $this->unitOfMeasure = $unitOfMeasure;
        $this->unitSize = $unitSize;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getUnitOfMeasure(): string
    {
        return $this->unitOfMeasure;
    }

    public function getUnitSize(): int
    {
        return $this->unitSize;
    }

    public function toArray(): array
    {
        return [
            'amount'        => $this->amount,
            'unitOfMeasure' => $this->unitOfMeasure,
            'unitSize'      => $this->unitSize,
        ];
    }

    public static function fromArray(array $data)
    {
        return new static(
            $data['amount'],
            $data['unitOfMeasure'],
            isset($data['unitSize']) ? $data['unitSize'] : null
        );
    }
}
