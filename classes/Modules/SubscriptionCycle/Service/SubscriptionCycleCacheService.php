<?php

declare(strict_types=1);

namespace Xentral\Modules\SubscriptionCycle\Service;

use Xentral\Components\Database\Database;
use Xentral\Modules\SubscriptionCycle\Data\SubscriptionCycleCacheData;

final class SubscriptionCycleCacheService
{
    /** @var Database */
    private $db;

    /** @var string TABLE_NAME */
    const TABLE_NAME = 'subscription_cycle_cache';

    /**
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * @param SubscriptionCycleCacheData[] $data
     */
    public function createCacheEntries(array $data): void
    {
        if (!empty($data)) {
            $insert = $this->db->insert()
                ->into(self::TABLE_NAME);

            foreach ($data as $entry) {
                $insert->addRow()
                    ->cols(
                        [
                            'subscription_article_id'  => $entry->getSubscriptionArticleId(),
                            'start_date'               => $entry->getStartDate()->format('Y-m-d'),
                            'cycles_count'             => $entry->getCyclesCount(),
                            'calculation_base_date'    => $entry->getCalculationBaseDate()->format('Y-m-d'),
                            'start_month_price_factor' => $entry->getStartMonthPriceFactor(),
                        ]
                    );
            }

            $insertSql = $insert->getStatement();
            $values = $insert->getBindValues();
            $this->db->perform($insertSql, $values);
        }
    }

    public function emptyCache(): void
    {
        $sql = 'TRUNCATE `subscription_cycle_cache`';
        $this->db->perform($sql);
    }
}
