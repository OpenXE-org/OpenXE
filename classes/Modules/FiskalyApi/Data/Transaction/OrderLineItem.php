<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\Transaction;

use Xentral\Modules\FiskalyApi\Exception\InvalidArgumentException;

class OrderLineItem
{
    /** @var string $quantity */
    private $quantity;

    /** @var string $text */
    private $text;

    /** @var string $pricePerUnit */
    private $pricePerUnit;

    /**
     * OrderLineItem constructor.
     *
     * @param string $quantity
     * @param string $text
     * @param string $pricePerUnit
     */
    public function __construct(string $quantity, string $text, string $pricePerUnit)
    {
        $this->setQuantity($quantity);
        $this->setText($text);
        $this->setPricePerUnit($pricePerUnit);
    }

    /**
     * @param $apiResult
     *
     * @return static
     */
    public static function fromApiResult(object $apiResult): self
    {
        return new self($apiResult->quantity, $apiResult->text, $apiResult->price_per_unit);
    }

    /**
     * @param array $dbState
     *
     * @return static
     */
    public static function fromDbState(array $dbState): self
    {
        return new self($dbState['quantity'], $dbState['text'], $dbState['price_per_unit']);
    }

    /**
     * @return string[]
     */
    public function toArray(): array
    {
        return [
            'quantity'       => $this->getQuantity(),
            'text'           => $this->getText(),
            'price_per_unit' => $this->getPricePerUnit(),
        ];
    }

    /**
     * @return string
     */
    public function getQuantity(): string
    {
        return $this->quantity;
    }

    /**
     * @param string $quantity
     */
    public function setQuantity(string $quantity): void
    {
        if (!preg_match('/^-?\d+(\.\d{1,64})?$/', $quantity)) {
            throw new InvalidArgumentException("invalid quantity-format '{$quantity}'");
        }
        $this->quantity = $quantity;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = mb_substr($text, 0, 255);
    }

    /**
     * @return string
     */
    public function getPricePerUnit(): string
    {
        return $this->pricePerUnit;
    }

    /**
     * @param string $pricePerUnit
     */
    public function setPricePerUnit(string $pricePerUnit): void
    {
        if (!preg_match('/^-?\d+(\.\d{2,64})?$/', $pricePerUnit)) {
            throw new InvalidArgumentException("invalid price-format '{$pricePerUnit}'");
        }
        $this->pricePerUnit = $pricePerUnit;
    }
}
