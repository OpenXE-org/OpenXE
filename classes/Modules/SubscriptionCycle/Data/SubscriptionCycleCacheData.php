<?php

declare(strict_types=1);

namespace Xentral\Modules\SubscriptionCycle\Data;

use DateTimeImmutable;
use DateTimeInterface;

final class SubscriptionCycleCacheData
{

    /** @var int $subscriptionArticleId */
    private $subscriptionArticleId;

    /** @var DateTimeInterface $startDate */
    private $startDate;

    /** @var DateTimeInterface $calculationBaseDate */
    private $calculationBaseDate;

    /** @var float $startMonthPriceFactor */
    private $startMonthPriceFactor;

    /** @var int $cyclesCount */
    private $cyclesCount;

    private function __construct()
    {
    }

    /**
     * @param array $data
     *
     * @return SubscriptionCycleCacheData
     */
    public static function fromDbState(array $data): SubscriptionCycleCacheData
    {
        $cacheData = new SubscriptionCycleCacheData();

        $cacheData->subscriptionArticleId = (int)$data['subscription_article_id'];
        $cacheData->startDate = new DateTimeImmutable($data['start_date']);
        $cacheData->cyclesCount = (int)$data['cycles_count'];
        $cacheData->calculationBaseDate = new DateTimeImmutable($data['calculation_base_date']);
        $cacheData->startMonthPriceFactor = (float)$data['start_month_price_factor'];

        return $cacheData;
    }

    /**
     * @return int
     */
    public function getSubscriptionArticleId(): int
    {
        return $this->subscriptionArticleId;
    }

    /**
     * @return DateTimeInterface
     */
    public function getStartDate(): DateTimeInterface
    {
        return $this->startDate;
    }

    /**
     * @return DateTimeInterface
     */
    public function getCalculationBaseDate(): DateTimeInterface
    {
        return $this->calculationBaseDate;
    }

    /**
     * @return float
     */
    public function getStartMonthPriceFactor(): float
    {
        return $this->startMonthPriceFactor;
    }

    /**
     * @return int
     */
    public function getCyclesCount(): int
    {
        return $this->cyclesCount;
    }
}
