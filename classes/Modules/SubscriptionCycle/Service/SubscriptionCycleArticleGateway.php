<?php

declare(strict_types=1);

namespace Xentral\Modules\SubscriptionCycle\Service;

use DateTimeInterface;
use Xentral\Components\Database\Database;
use Xentral\Modules\SubscriptionCycle\Data\SubscriptionCycleCacheData;

final class SubscriptionCycleArticleGateway
{
    /** @var Database */
    private $db;

    /**
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * @param DateTimeInterface $nextFirstDay
     *
     * @return SubscriptionCycleCacheData[]
     */
    public function findMonthlySubscriptionData(DateTimeInterface $nextFirstDay): array
    {
        $sql =
            'SELECT abr.id AS `subscription_article_id`,
            CASE 
                WHEN abr.abgerechnetbis != \'0000-00-00\' AND abr.abgerechnetbis = LAST_DAY(abr.abgerechnetbis)
                    THEN DATE_ADD(abr.abgerechnetbis, INTERVAL 1 DAY)
                WHEN abr.abgerechnetbis != \'0000-00-00\' 
                    THEN abr.abgerechnetbis
                WHEN abr.startdatum = LAST_DAY(abr.startdatum)
                    THEN DATE_ADD(abr.startdatum, INTERVAL 1 DAY)
                ELSE abr.startdatum
            END AS `start_date`,
            ROUND(ROUND(
              DATEDIFF(
                CASE 
                    WHEN abr.abgerechnetbis != \'0000-00-00\' AND abr.abgerechnetbis >= :next_first_day
                        THEN DATE_ADD(LAST_DAY(abr.abgerechnetbis), INTERVAL 1 DAY)
                    WHEN abr.abgerechnetbis != \'0000-00-00\' AND abr.abgerechnetbis = LAST_DAY(abr.abgerechnetbis)
                        THEN DATE_SUB(DATE_ADD(:next_first_day, INTERVAL 1 MONTH), INTERVAL IF(abr.zahlzyklus < 1, 0, abr.zahlzyklus - 1) MONTH)
                    WHEN abr.abgerechnetbis != \'0000-00-00\' AND DAY(abr.abgerechnetbis) = 1
                        THEN DATE_SUB(:next_first_day, INTERVAL IF(abr.zahlzyklus < 1, 0, abr.zahlzyklus - 1) MONTH)
                    WHEN abr.abgerechnetbis != \'0000-00-00\'
                       THEN DATE_ADD(:next_first_day, INTERVAL IF(abr.zahlzyklus <= 1, 1, abr.zahlzyklus) MONTH)
                    WHEN abr.startdatum > :next_first_day
                        THEN DATE_ADD(LAST_DAY(abr.startdatum), INTERVAL 1 DAY)
                    WHEN abr.startdatum = :next_first_day AND DAY(abr.startdatum) = 1
                        THEN DATE_ADD(:next_first_day, INTERVAL 1 MONTH)
                    WHEN abr.startdatum < :next_first_day
                        THEN DATE_ADD(:next_first_day, INTERVAL 1 MONTH)
                    ELSE :next_first_day
                END,
                CASE 
                WHEN abr.abgerechnetbis != \'0000-00-00\' AND DAY(abr.abgerechnetbis) = 1
                  THEN abr.abgerechnetbis
                WHEN abr.abgerechnetbis != \'0000-00-00\' AND abr.abgerechnetbis = LAST_DAY(abr.abgerechnetbis)
                  THEN DATE_ADD(LAST_DAY(abr.abgerechnetbis), INTERVAL 1 DAY)
                WHEN abr.abgerechnetbis != \'0000-00-00\'
                  THEN DATE_SUB(DATE_ADD(LAST_DAY(abr.abgerechnetbis), INTERVAL 1 DAY), INTERVAL 1 MONTH)
                WHEN DAY(abr.startdatum) = 1
                  THEN abr.startdatum
                WHEN abr.startdatum = LAST_DAY(abr.startdatum)
                  THEN DATE_ADD(abr.startdatum, INTERVAL 1 DAY)
                ELSE DATE_SUB(DATE_ADD(LAST_DAY(abr.startdatum), INTERVAL 1 DAY), INTERVAL 1 MONTH)
                END
              ) / 30
            ,0) / IF(abr.zahlzyklus <= 1, 1, abr.zahlzyklus)) * IF(abr.zahlzyklus <= 1, 1, abr.zahlzyklus) AS `cycles_count`,
            CASE
                WHEN abr.abgerechnetbis != \'0000-00-00\' AND abr.abgerechnetbis = LAST_DAY(abr.abgerechnetbis)
                    THEN DATE_ADD(abr.abgerechnetbis, INTERVAL 1 DAY)
                WHEN abr.abgerechnetbis != \'0000-00-00\'
                    THEN DATE_ADD(LAST_DAY(DATE_SUB(abr.abgerechnetbis,INTERVAL 1 MONTH)), INTERVAL 1 DAY)
                WHEN DAY(abr.startdatum) = 1
                    THEN abr.startdatum
                WHEN abr.startdatum = LAST_DAY(abr.startdatum)
                    THEN DATE_ADD(abr.startdatum, INTERVAL 1 DAY)
                ELSE DATE_ADD(LAST_DAY(DATE_SUB(abr.startdatum,INTERVAL 1 MONTH)), INTERVAL 1 DAY)
            END AS `calculation_base_date`,
            CASE
                WHEN abr.abgerechnetbis = \'0000-00-00\' AND DAY(abr.startdatum) != 1 AND abr.startdatum != LAST_DAY(abr.startdatum) 
                    THEN ( IF(DAY(abr.startdatum) = 1,0,DAY(abr.startdatum)) / DAY(LAST_DAY(abr.startdatum)))
                WHEN abr.abgerechnetbis = \'0000-00-00\'
                    THEN 0
                WHEN abr.abgerechnetbis != LAST_DAY(abr.abgerechnetbis)
                    THEN (IF(DAY(abr.abgerechnetbis) = 1,0,DAY(abr.abgerechnetbis)) / DAY(LAST_DAY(abr.abgerechnetbis)))
                ELSE
                    0
            END
             AS `start_month_price_factor`
            FROM `abrechnungsartikel` AS `abr`
            WHERE (abr.preisart = \'monat\' OR abr.preisart = \'\') 
              AND (abr.startdatum IS NULL OR abr.startdatum = \'0000-00-00\'
                       OR (
                           abr.startdatum <= :next_first_day
                               OR (abr.preisart = \'monat\' OR abr.preisart = \'\'
                                   AND DATE_ADD(abr.startdatum, INTERVAL 1 DAY) < DATE_ADD(:next_first_day, INTERVAL 1 MONTH))
                          )
                  )
              AND (
                  abr.abgerechnetbis = \'0000-00-00\'
                    OR  (abr.abgerechnetbis < :next_first_day)
                       OR (abr.abgerechnetbis < DATE_SUB(DATE_ADD(:next_first_day, INTERVAL 1 MONTH), INTERVAL 1 DAY)
                               AND (abr.preisart = \'monat\' OR abr.preisart = \'\'))
                  )
              AND (abr.enddatum = \'0000-00-00\' OR abr.enddatum >= DATE_SUB(:next_first_day, INTERVAL 1 DAY))';

        return $this->convertArrayToSubscriptionCycleCacheData(
            $this->db->fetchAll($sql, ['next_first_day' => $nextFirstDay->format('Y-m-d')])
        );
    }

    /**
     * @param DateTimeInterface $nextFirstDay
     *
     * @return SubscriptionCycleCacheData[]
     */
    public function findCustomIntervalSubscriptionData(DateTimeInterface $nextFirstDay): array
    {
        $sql =
            'SELECT
            abr.id AS `subscription_article_id`, 
            IF(
              abr.abgerechnetbis != \'0000-00-00\',
              IF(
                abr.abgerechnetbis = LAST_DAY(abr.abgerechnetbis),
                DATE_ADD(abr.abgerechnetbis, INTERVAL 1 DAY),
                abr.abgerechnetbis
              ),
              IF(
                abr.startdatum = LAST_DAY(abr.startdatum),
                DATE_ADD(abr.startdatum, INTERVAL 1 DAY),
                abr.startdatum
              )
            ) AS `start_date`,
            ROUND(
              DATEDIFF(
                IF(
		            abr.startdatum > :next_first_day,
                    DATE_ADD(LAST_DAY(abr.startdatum), INTERVAL 1 DAY),
                    :next_first_day
                ),
                IF(
                  abr.abgerechnetbis != \'0000-00-00\',
                  IF(
                    abr.abgerechnetbis = LAST_DAY(abr.abgerechnetbis),
                    DATE_ADD(abr.abgerechnetbis, INTERVAL 1 DAY),
                    DATE_ADD(LAST_DAY(DATE_SUB(abr.abgerechnetbis,INTERVAL 1 MONTH)), INTERVAL 1 DAY)
                  ),
                  IF(
                    abr.startdatum = LAST_DAY(abr.startdatum),
                    DATE_ADD(abr.startdatum, INTERVAL 1 DAY),
                    DATE_ADD(LAST_DAY(DATE_SUB(abr.startdatum,INTERVAL 1 MONTH)), INTERVAL 1 DAY)
                  )
                )
              ) / 30
            ,0) AS `cycles_count`,
            IF(
              abr.abgerechnetbis != \'0000-00-00\',
              IF(
                abr.abgerechnetbis = LAST_DAY(abr.abgerechnetbis),
                DATE_ADD(abr.abgerechnetbis, INTERVAL 1 DAY),
                DATE_ADD(LAST_DAY(DATE_SUB(abr.abgerechnetbis,INTERVAL 1 MONTH)), INTERVAL 1 DAY)
              ),
              IF(
                abr.startdatum = LAST_DAY(abr.startdatum),
                DATE_ADD(abr.startdatum, INTERVAL 1 DAY),
                DATE_ADD(LAST_DAY(DATE_SUB(abr.startdatum,INTERVAL 1 MONTH)), INTERVAL 1 DAY)
              )
            ) AS `calculation_base_date`,
            0 AS `start_month_price_factor`
            FROM `abrechnungsartikel` AS `abr`
            WHERE abr.preisart = \'monatx\'';

        return $this->convertArrayToSubscriptionCycleCacheData(
            $this->db->fetchAll($sql, ['next_first_day' => $nextFirstDay->format('Y-m-d')])
        );
    }

    /**
     * @param DateTimeInterface $nextFirstDay
     *
     * @return SubscriptionCycleCacheData[]
     */
    public function findYearlySubscriptionData(DateTimeInterface $nextFirstDay): array
    {
        $sql =
            'SELECT
            abr.id AS `subscription_article_id`, 
            IF(
              abr.abgerechnetbis != \'0000-00-00\',
              DATE_ADD(abr.abgerechnetbis, INTERVAL 1 DAY),
              IF(
                abr.startdatum = LAST_DAY(abr.startdatum),
                DATE_ADD(abr.startdatum, INTERVAL 1 DAY),
                abr.startdatum
              )
            ) AS `start_date`,
            ROUND(
              DATEDIFF(
                IF(
		            abr.startdatum > :next_first_day,
                    DATE_ADD(LAST_DAY(abr.startdatum), INTERVAL 1 DAY),
                    :next_first_day
                ),
                IF(
                  abr.abgerechnetbis != \'0000-00-00\',
                  IF(
                    abr.abgerechnetbis = LAST_DAY(abr.abgerechnetbis),
                    DATE_ADD(abr.abgerechnetbis, INTERVAL 1 DAY),
                    DATE_ADD(LAST_DAY(DATE_SUB(abr.abgerechnetbis,INTERVAL 1 MONTH)), INTERVAL 1 DAY)
                  ),
                  IF(
                    abr.startdatum = LAST_DAY(abr.startdatum),
                    DATE_ADD(abr.startdatum, INTERVAL 1 DAY),
                    DATE_ADD(LAST_DAY(DATE_SUB(abr.startdatum,INTERVAL 1 MONTH)), INTERVAL 1 DAY)
                  )
                )
              ) / 365
            ,0) AS `cycles_count`,
            IF(
              abr.abgerechnetbis != \'0000-00-00\',
              IF(
                abr.abgerechnetbis = LAST_DAY(abr.abgerechnetbis),
                DATE_ADD(abr.abgerechnetbis, INTERVAL 1 DAY),
                DATE_ADD(LAST_DAY(DATE_SUB(abr.abgerechnetbis,INTERVAL 1 MONTH)), INTERVAL 1 DAY)
              ),
              IF(
                abr.startdatum = LAST_DAY(abr.startdatum),
                DATE_ADD(abr.startdatum, INTERVAL 1 DAY),
                DATE_ADD(LAST_DAY(DATE_SUB(abr.startdatum,INTERVAL 1 MONTH)), INTERVAL 1 DAY)
              )
            ) AS `calculation_base_date`,
            0 AS `start_month_price_factor`
            FROM `abrechnungsartikel` AS `abr`
            WHERE abr.preisart = \'jahr\'';

        return $this->convertArrayToSubscriptionCycleCacheData(
            $this->db->fetchAll($sql, ['next_first_day' => $nextFirstDay->format('Y-m-d')])
        );
    }

    /**
     * @param DateTimeInterface $date
     *
     * @return SubscriptionCycleCacheData[]
     */
    public function findWeeklySubscriptionData(DateTimeInterface $date): array
    {
        $sql =
            'SELECT
            abr.id AS `subscription_article_id`,
            IF(
              abr.abgerechnetbis != \'0000-00-00\',
              abr.abgerechnetbis,
              abr.startdatum
            ) AS `start_date`,
            (FLOOR(    
                DATEDIFF(
                  :date,
                  IF(
                    abr.abgerechnetbis != \'0000-00-00\',
                    IF(
                      abr.abgerechnetbis = LAST_DAY(abr.abgerechnetbis),
                      DATE_ADD(abr.abgerechnetbis, INTERVAL 1 DAY),
                      DATE_ADD(LAST_DAY(DATE_SUB(abr.abgerechnetbis,INTERVAL 1 MONTH)), INTERVAL 1 DAY)
                    ),
                    IF(
                      abr.startdatum = LAST_DAY(abr.startdatum),
                      DATE_ADD(abr.startdatum, INTERVAL 1 DAY),
                      DATE_ADD(LAST_DAY(DATE_SUB(abr.startdatum,INTERVAL 1 MONTH)), INTERVAL 1 DAY)
                    )
                  )
                ) 
                / (7 * abr.zahlzyklus)
            ) 
            * abr.zahlzyklus
            ) + abr.zahlzyklus AS `cycles_count`,
            IF(
              abr.abgerechnetbis != \'0000-00-00\',
              abr.abgerechnetbis,
              abr.startdatum
            ) AS `calculation_base_date`,
            0 AS `start_month_price_factor`
            FROM `abrechnungsartikel` AS `abr`
            WHERE abr.preisart = \'wochen\'';

        return $this->convertArrayToSubscriptionCycleCacheData(
            $this->db->fetchAll($sql, ['date' => $date->format('Y-m-d')])
        );
    }

    /**
     * @param DateTimeInterface $date
     *
     * @return SubscriptionCycleCacheData[]
     */
    public function find30DaysSubscriptionData(DateTimeInterface $date): array
    {
        $sql =
            'SELECT
            abr.id AS `subscription_article_id`, 
            IF(
              abr.abgerechnetbis != \'0000-00-00\',
              abr.abgerechnetbis,
              abr.startdatum
            ) AS `start_date`,
            (FLOOR(    
                DATEDIFF(
                  :date,
                  IF(
                    abr.abgerechnetbis != \'0000-00-00\',
                    IF(
                      abr.abgerechnetbis = LAST_DAY(abr.abgerechnetbis),
                      DATE_ADD(abr.abgerechnetbis, INTERVAL 1 DAY),
                      DATE_ADD(LAST_DAY(DATE_SUB(abr.abgerechnetbis,INTERVAL 1 MONTH)), INTERVAL 1 DAY)
                    ),
                    IF(
                      abr.startdatum = LAST_DAY(abr.startdatum),
                      DATE_ADD(abr.startdatum, INTERVAL 1 DAY),
                      DATE_ADD(LAST_DAY(DATE_SUB(abr.startdatum,INTERVAL 1 MONTH)), INTERVAL 1 DAY)
                    )
                  )
                ) 
                / (30 * abr.zahlzyklus)
            ) 
            * abr.zahlzyklus
            ) + abr.zahlzyklus AS `cycles_count`,
            IF(
              abr.abgerechnetbis != \'0000-00-00\',
              abr.abgerechnetbis,
              abr.startdatum
            ) AS `calculation_base_date`,
            0 AS `start_month_price_factor`
            FROM `abrechnungsartikel` AS `abr`
            WHERE abr.preisart = \'30tage\'';

        return $this->convertArrayToSubscriptionCycleCacheData(
            $this->db->fetchAll($sql, ['date' => $date->format('Y-m-d')])
        );
    }

    /**
     * @return SubscriptionCycleCacheData[]
     */
    public function findOneTimeSubscriptionData(): array
    {
        $sql =
            'SELECT
            abr.id AS `subscription_article_id`, 
            \'0000-00-00\' AS `start_date`,
            0 AS `cycles_count`,
            IF(
              abr.abgerechnetbis != \'0000-00-00\',
              abr.abgerechnetbis,
              abr.startdatum
            ) AS `calculation_base_date`,
            0 AS `start_month_price_factor`
            FROM `abrechnungsartikel` AS `abr`
            WHERE abr.preisart = \'einmalig\'';

        return $this->convertArrayToSubscriptionCycleCacheData($this->db->fetchAll($sql));
    }

    /**
     * @param array $result
     *
     * @return SubscriptionCycleCacheData[]
     */
    private function convertArrayToSubscriptionCycleCacheData(array $result): array
    {
        $return = [];
        if (!empty($result)) {
            foreach ($result as $row) {
                $return[] = SubscriptionCycleCacheData::fromDbState($row);
            }
        }

        return $return;
    }
}
