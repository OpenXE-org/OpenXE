<?php


namespace Xentral\Modules\Shopware6\Data;


class PriceData
{
    /** @var int */
    protected $startingQuantity;
    /** @var float */
    protected $net;
    /** @var float */
    protected $gross;
    /** @var string */
    protected $currency;
    /** @var string */
    protected $groupName;

    /**
     * PriceData constructor.
     *
     * @param $startingQuantity
     * @param $net
     * @param $gross
     * @param $currency
     * @param $groupName
     */
    public function __construct(int $startingQuantity, float $net, float $gross, string $currency, string $groupName)
    {
        $this->startingQuantity = $startingQuantity;
        $this->net = $net;
        $this->gross = $gross;
        $this->currency = $currency;
        $this->groupName = $groupName;
    }

    /**
     * @return string
     */
    public function getGroupName(): string
    {
        return $this->groupName;
    }

    /**
     * @param string $groupName
     *
     * @return PriceData
     */
    public function setGroupName(string $groupName): PriceData
    {
        $this->groupName = $groupName;

        return $this;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     *
     * @return PriceData
     */
    public function setCurrency(string $currency): PriceData
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @return int
     */
    public function getStartingQuantity(): int
    {
        return $this->startingQuantity;
    }

    /**
     * @param int $startingQuantity
     *
     * @return PriceData
     */
    public function setStartingQuantity(int $startingQuantity): PriceData
    {
        $this->startingQuantity = $startingQuantity;

        return $this;
    }

    /**
     * @return float
     */
    public function getNet(): float
    {
        return $this->net;
    }

    /**
     * @param float $net
     *
     * @return PriceData
     */
    public function setNet(float $net): PriceData
    {
        $this->net = $net;

        return $this;
    }

    /**
     * @return float
     */
    public function getGross(): float
    {
        return $this->gross;
    }

    /**
     * @param float $gross
     *
     * @return PriceData
     */
    public function setGross(float $gross): PriceData
    {
        $this->gross = $gross;

        return $this;
    }


}
