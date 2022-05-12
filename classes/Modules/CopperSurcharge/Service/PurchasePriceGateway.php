<?php

declare(strict_types=1);

namespace Xentral\Modules\CopperSurcharge\Service;

use DateTimeInterface;
use Xentral\Components\Database\Database;
use Xentral\Modules\CopperSurcharge\Exception\EmptyResultException;

final class PurchasePriceGateway
{

    /** @var Database $db */
    private $db;

    /**
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * @param DateTimeInterface $calcDate
     * @param int               $copperArticleId
     *
     * @throws EmptyResultException
     *
     * @return float
     */
    public function getDelCopperPriceByDate(DateTimeInterface $calcDate, int $copperArticleId): float
    {
        $sql =
            "SELECT data.price, data.valid_to FROM(
                SELECT 
                e.preis AS `price`, 
                IF(
                    e.gueltig_bis = '0000-00-00',
                    CURDATE(),
                    e.gueltig_bis
                ) AS `valid_to` 
                FROM `einkaufspreise` AS `e` 
                WHERE e.artikel = :copper_article_id
            ) AS `data`
            WHERE data.valid_to <= :calc_date 
            ORDER BY data.valid_to DESC
            LIMIT 1";

        $result = $this->db->fetchAll(
            $sql,
            [
                'copper_article_id' => $copperArticleId,
                'calc_date'         => $calcDate->format('Y-m-d'),
            ]
        );

        if (empty($result)) {
            $sql =
                "SELECT 
                e.preis AS `price`
                FROM `einkaufspreise` AS `e`  
                WHERE id = (
                    SELECT 
                    MIN(e2.id)
                    FROM `einkaufspreise` AS `e2` 
                    WHERE e2.artikel = :copper_article_id
                )";

            $result = $this->db->fetchAll(
                $sql,
                [
                    'copper_article_id' => $copperArticleId,
                ]
            );

            if (empty($result)) {
                throw new EmptyResultException('No prices found for article: ' . $copperArticleId);
            }
        }

        return (float)$result[0]['price'];
    }
}
