<?php

declare(strict_types=1);

namespace Xentral\Modules\SubscriptionCycle;

use DateTimeInterface;
use Xentral\Modules\SubscriptionCycle\Service\SubscriptionCycleArticleGateway;
use Xentral\Modules\SubscriptionCycle\Service\SubscriptionCycleCacheService;

final class SubscriptionCycleCacheFiller
{

    /** @var SubscriptionCycleArticleGateway $articleGateway */
    private $articleGateway;

    /** @var SubscriptionCycleCacheService $cacheService */
    private $cacheService;

    /**
     * @param SubscriptionCycleArticleGateway $articleGateway
     * @param SubscriptionCycleCacheService   $cacheService
     */
    public function __construct(
        SubscriptionCycleArticleGateway $articleGateway,
        SubscriptionCycleCacheService $cacheService
    ) {
        $this->articleGateway = $articleGateway;
        $this->cacheService = $cacheService;
    }

    /**
     * @param DateTimeInterface $nextFirstDate
     */
    public function generateCacheByNextFirstDate(DateTimeInterface $nextFirstDate): void
    {
        $monthlyData = $this->articleGateway->findMonthlySubscriptionData($nextFirstDate);
        $customIntervalData = $this->articleGateway->findCustomIntervalSubscriptionData($nextFirstDate);
        $yearlyData = $this->articleGateway->findYearlySubscriptionData($nextFirstDate);

        $allData = array_merge($monthlyData, $customIntervalData, $yearlyData);
        if (!empty($allData)) {
            $this->cacheService->createCacheEntries($allData);
        }
    }

    /**
     * @param DateTimeInterface $currentDate
     */
    public function generateCacheByCurrentDate(DateTimeInterface $currentDate): void
    {
        $weeklyData = $this->articleGateway->findWeeklySubscriptionData($currentDate);
        $thirtyDaysData = $this->articleGateway->find30DaysSubscriptionData($currentDate);

        $allData = array_merge($weeklyData, $thirtyDaysData);
        if (!empty($allData)) {
            $this->cacheService->createCacheEntries($allData);
        }
    }

    public function generateCacheByOneTimeData(): void
    {
        $data = $this->articleGateway->findOneTimeSubscriptionData();
        if (!empty($data)) {
            $this->cacheService->createCacheEntries($data);
        }
    }

    public function emptyCache()
    {
        $this->cacheService->emptyCache();
    }
}
