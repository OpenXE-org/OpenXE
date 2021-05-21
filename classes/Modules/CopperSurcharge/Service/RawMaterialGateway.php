<?php

declare(strict_types=1);

namespace Xentral\Modules\CopperSurcharge\Service;

use Xentral\Components\Database\Database;
use Xentral\Modules\CopperSurcharge\Exception\EmptyResultException;

final class RawMaterialGateway
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
     * @param int $articleId
     * @param int $copperArticleId
     *
     * @throws EmptyResultException
     *
     * @return float
     */
    public function getRawMaterialAmount(int $articleId, int $copperArticleId): float
    {
        $sql =
            "SELECT r.menge
            FROM `rohstoffe` AS `r`
            WHERE r.rohstoffvonartikel = :article_id
            AND r.artikel = :copper_article_id
            LIMIT 1";

        $result = $this->db->fetchValue($sql, ['article_id' => $articleId, 'copper_article_id' => $copperArticleId]);

        if (empty($result)) {
            throw new EmptyResultException('No raw material amount found for articleId: ' . $articleId);
        }

        return (float)$result;
    }

    /**
     * @param int $copperArticleId
     * @param int $copperSurchargeArticleId
     *
     * @return array
     */
    public function findPossibleCopperArticle(int $copperArticleId, int $copperSurchargeArticleId): array
    {
        $sql =
            "SELECT art.id AS `article_id`, r.menge AS `amount`
            FROM `artikel` AS `art`
            INNER JOIN `rohstoffe` AS `r` ON r.rohstoffvonartikel = art.id
            WHERE r.rohstoffvonartikel = :copper_article_id
            AND r.artikel = :copper_surcharge_article_id
            AND art.rohstoffe = 1";
        $result = $this->db->fetchAll(
            $sql,
            [
                'copper_article_id'           => $copperArticleId,
                'copper_surcharge_article_id' => $copperSurchargeArticleId,
            ]
        );
        if (!empty($result)) {
            return [
                'article_id' => $result[0]['article_id'],
                'amount'     => (float)$result[0]['amount'],
            ];
        }

        return [];
    }

    /**
     * @param string $docType
     * @param int    $docTypeId
     * @param int    $copperSurchargeArticleId
     *
     * @return array
     */
    public function findPositions(
        string $docType,
        int $docTypeId,
        int $copperSurchargeArticleId
    ): array {
        $explodedColumnName = 'explodiert_parent';
        if ($docType === 'rechnung') {
            $explodedColumnName = 'explodiert_parent_artikel';
        }

        $sql = "SELECT DISTINCT
          beleg_pos.id AS `pos_id`,
          a.id AS `article_id`,
          beleg_pos.waehrung AS `currency`
          FROM `{$docType}_position` AS `beleg_pos`
          INNER JOIN `{$docType}` AS `beleg` ON beleg.id = beleg_pos.{$docType}
          INNER JOIN `artikel` AS `a` ON a.id = beleg_pos.artikel
          LEFT JOIN `rohstoffe` AS `r` 
            ON r.rohstoffvonartikel = beleg_pos.artikel 
            AND r.artikel = :copper_surcharge_article_id
          WHERE beleg_pos.{$docType} = :doc_type_id
          AND a.rohstoffe = 1
          AND beleg.schreibschutz = 0
          AND beleg_pos.{$explodedColumnName} = 0
          AND r.id IS NOT NULL
          ORDER BY beleg_pos.sort";

        $result = $this->db->fetchAll(
            $sql,
            [
                'copper_surcharge_article_id' => $copperSurchargeArticleId,
                'doc_type_id'                 => $docTypeId,
            ]
        );
        if (!empty($result)) {
            return $result;
        }

        return [];
    }

    /**
     * @param string $docType
     * @param int    $docId
     * @param int    $copperSurchargeArticleId
     *
     * @return bool
     */
    public function hasCopperArticles(
        string $docType,
        int $docId,
        int $copperSurchargeArticleId
    ): bool {
        $sql = "SELECT pos.id
        FROM `" . $docType . "_position`  AS `pos`
        INNER JOIN `rohstoffe` AS `raw` 
            ON raw.rohstoffvonartikel = pos.artikel 
            AND raw.artikel = :copper_surcharge_article_id
        WHERE pos." . $docType . " = :doc_id";

        return !empty(
        $this->db->fetchAll(
            $sql,
            [
                'doc_id'                      => $docId,
                'copper_surcharge_article_id' => $copperSurchargeArticleId,
            ]
        )
        );
    }

    /**
     * @param string $docType
     * @param int    $docId
     * @param int    $copperSurchargeArticleId
     *
     * @return array
     */
    public function findAllPositionsForGrouped(
        string $docType,
        int $docId,
        int $copperSurchargeArticleId
    ): array {
        $sql =
            "SELECT * FROM(
                SELECT 
                pos.id AS `pos_id`,
                pos.artikel AS `article_id`,
                pos.waehrung AS `currency`,
                pos.sort AS `sort`,
                IF(raw.id IS NULL,0,1) AS `is_copper`,
                1 AS `pos_type`,
                '' AS `between_type`,
                0 AS `between_id`       
                FROM `" . $docType . "_position`  AS `pos`
                LEFT JOIN `rohstoffe` AS `raw` 
                    ON raw.rohstoffvonartikel = pos.artikel 
                    AND raw.artikel = :copper_surcharge_article_id
                WHERE " . $docType . " = :doc_id
                UNION
                SELECT 
                0 AS `pos_id`,
                0 AS `article_id`,
                '' AS `currency`,
                z.pos AS `sort`,
                0 AS `is_copper`,
                2 AS `pos_type`,
                z.postype AS `between_type`,
                z.id AS `between_id`
                FROM `beleg_zwischenpositionen` AS `z`
                WHERE z.doctype = :doc_type
                AND z.doctypeid = :doc_id
            ) AS `data`
            ORDER BY data.sort, data.pos_type";

        return $this->db->fetchAll(
            $sql,
            [
                'doc_id'                      => $docId,
                'doc_type'                    => $docType,
                'copper_surcharge_article_id' => $copperSurchargeArticleId,
            ]
        );
    }
}
