<?php

namespace Xentral\Modules\Ebay\Data;

class StockLoggingData
{

    /** @var string */
    protected $itemId;
    /** @var string */
    protected $sku;
    /** @var int */
    protected $quantity;
    /** @var string */
    protected $status;
    /** @var StockLogingVariationData[] */
    protected $variations = [];
    /** @var string[] */
    protected $errorMessages = [];

    public function __construct(string $itemId)
    {
        $this->itemId = $itemId;
    }

    public function getItemId(): string
    {
        return $this->itemId;
    }

    public function setItemId(string $itemId): StockLoggingData
    {
        $this->itemId = $itemId;

        return $this;
    }

    public function getSku(): string
    {
        return $this->sku;
    }

    public function setSku(string $sku): StockLoggingData
    {
        $this->sku = $sku;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): StockLoggingData
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): StockLoggingData
    {
        $this->status = $status;

        return $this;
    }

    public function getVariations(): array
    {
        return $this->variations;
    }

    public function setVariations(array $variations): StockLoggingData
    {
        $this->variations = $variations;

        return $this;
    }

    public function addVariation(StockLogingVariationData $variation): StockLoggingData
    {
        $this->variations[$variation->getSku()] = $variation;

        return $this;
    }

    public function hasVariations(): bool
    {
        return !empty($this->variations);
    }

    public function getVariation(string $sku): StockLoggingVariationData
    {
        return $this->variations[$sku];
    }

    public function getErrorMessages(): array
    {
        return $this->errorMessages;
    }

    public function setErrorMessages(array $errorMessages): StockLoggingData
    {
        $this->errorMessages = $errorMessages;

        return $this;
    }

    public function hasErrorMessages(): bool
    {
        return !empty($this->errorMessages);
    }

    public function addErrorMessage(string $type, string $errorMessage): StockLoggingData
    {
        $this->errorMessages[$errorMessage] = $type;

        return $this;
    }
}
