<?php

declare(strict_types=1);

namespace Xentral\Modules\CopperSurcharge\Service;

use DateTimeImmutable;
use Exception;
use Xentral\Components\Database\Database;
use Xentral\Modules\CopperSurcharge\Exception\EmptyResultException;
use Xentral\Modules\CopperSurcharge\Exception\InvalidDateFormatException;

final class DocumentGateway
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
     * @param int $orderId
     *
     * @return int
     */
    public function findOrderOfferId(int $orderId): int
    {
        $sql =
            "SELECT a.angebotid
            FROM `auftrag` AS `a`
            WHERE id = :order_id";

        return (int)$this->db->fetchValue($sql, ['order_id' => $orderId]);
    }

    /**
     * @param string $doctype
     * @param int    $documentId
     *
     * @throws InvalidDateFormatException
     * @return DateTimeImmutable
     */
    public function getCalcDate(string $doctype, int $documentId): DateTimeImmutable
    {
        $sql =
            "SELECT b.datum AS `date`
            FROM `" . $doctype . "` AS `b`
            WHERE b.id = :document_id";

        $result = $this->db->fetchValue($sql, ['document_id' => $documentId]);

        try {
            return new DateTimeImmutable($result);
        } catch (Exception $e) {
            throw new InvalidDateFormatException('Could not convert date: ' . $result['date']);
        }
    }

    /**
     * @param int $documentId
     *
     * @throws InvalidDateFormatException
     *
     * @return DateTimeImmutable|null
     */
    public function findDeliveryDate(int $documentId): ?DateTimeImmutable
    {
        $sql =
            "SELECT b.lieferdatum AS `delivery_date`
            FROM `rechnung` AS `b`
            WHERE b.id = :document_id";

        $result = $this->db->fetchValue($sql, ['document_id' => $documentId]);

        if ($result === '0000-00-00') {
            return null;
        }

        try {
            return new DateTimeImmutable($result);
        } catch (Exception $e) {
            throw new InvalidDateFormatException('Could not convert date: ' . $result['date']);
        }
    }

    /**
     * @param int    $articleId
     * @param string $articleCopperBaseField
     *
     * @return float
     */
    public function getArticleCopperBase(int $articleId, string $articleCopperBaseField): float
    {
        $copperBase = 0.0;
        $sql =
            "SELECT a.{$articleCopperBaseField} AS `copper_base` 
            FROM `artikel` AS `a`
            WHERE a.id = :article_id";

        $result = $this->db->fetchValue($sql, ['article_id' => $articleId]);

        if (!empty($result)) {
            $copperBase = $this->formatToFloat($result);
        }

        return $copperBase;
    }

    /**
     * @param string $string
     *
     * @return float
     */
    private function formatToFloat(string $string): float
    {
        $string = str_replace(',', '.', $string);

        return (float)$string;
    }

    /**
     * @param string $docType
     * @param int    $docId
     * @param string $freeField
     *
     * @return array
     */
    public function findAllPositionsForGrouped(
        string $docType,
        int $docId,
        string $freeField
    ): array {
        $sql =
            "SELECT * FROM(
                SELECT
                pos.id AS `pos_id`,
                pos.artikel AS `article_id`,
                pos.waehrung AS `currency`,
                pos.sort AS `sort`,
                IF(a.{$freeField} = '', 0, 1) AS `is_copper`,
                1 AS `pos_type`,
                '' AS `between_type`,
                0 AS `between_id`
                FROM `" . $docType . "_position`  AS `pos`
                INNER JOIN `artikel` AS `a` ON a.id = pos.artikel
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
                'doc_id'   => $docId,
                'doc_type' => $docType,
            ]
        );
    }

    /**
     * @param string $docType
     * @param int    $positionArticleId
     *
     * @return int
     */
    public function getPositionAmount(string $docType, int $positionArticleId): int
    {
        $sql =
            "SELECT pos.menge
            FROM `" . $docType . "_position` AS `pos`
            WHERE pos.id = :pos_id";

        return (int)$this->db->fetchValue($sql, ['pos_id' => $positionArticleId]);
    }

    /**
     * @param int $docId
     *
     * @return int
     */
    public function findInvoiceOrderId(int $docId): int
    {
        $sql =
            "SELECT r.auftragid
            FROM `rechnung` AS `r`
            WHERE id = :doc_id";

        return (int)$this->db->fetchValue($sql, ['doc_id' => $docId]);
    }

    /**
     * @param string $docType
     * @param int    $docId
     * @param string $copperNumberOption
     *
     * @return bool
     */
    public function hasCopperArticles(
        string $docType,
        int $docId,
        string $copperNumberOption
    ): bool {
        $sql = "SELECT pos.id
        FROM `" . $docType . "_position`  AS `pos`
        INNER JOIN `artikel` AS `a` ON a.id = pos.artikel
        WHERE pos." . $docType . " = :doc_id
        AND a.{$copperNumberOption} != ''";

        return !empty($this->db->fetchAll($sql, ['doc_id' => $docId]));
    }

    /**
     * @param $articleId
     *
     * @throws EmptyResultException
     *
     * @return array
     */
    public function getArticleData($articleId): array
    {
        $sql =
            "SELECT 
            a.name_de,
            a.anabregs_text AS `description`,
            a.umsatzsteuer AS `vat`, 
            a.rabatt AS `discount`, 
            a.projekt AS `project`, 
            a.nummer AS `number`
            FROM `artikel` AS `a`
            WHERE a.id = :article_id
            ";
        $articleData = $this->db->fetchRow($sql, ['article_id' => $articleId]);
        if (empty($articleData)) {
            throw new EmptyResultException('No article found for id: ' . $articleId);
        }

        return $articleData;
    }

    /**
     * @param string $docType
     * @param int    $positionId
     *
     * @return int
     */
    public function getArticleIdByPositionId(string $docType, int $positionId): int
    {
        $sql =
            "SELECT pos.artikel
            FROM `{$docType}_position` AS `pos`
            WHERE pos.id = :position_id";

        return $this->db->fetchValue($sql, ['position_id' => $positionId]);
    }

    /**
     * @param int $docId
     *
     * @return int
     */
    public function findInvoiceOfferId(int $docId): int
    {
        $sql =
            "SELECT a.angebotid 
            FROM `rechnung` AS `r`
            INNER JOIN `auftrag` AS `a` ON a.id = r.auftragid
            WHERE r.id = :doc_id";

        return (int)$this->db->fetchValue($sql, ['doc_id' => $docId]);
    }

    /**
     * @param string $docType
     * @param int    $docTypeId
     * @param string $copperNumberOption
     *
     * @return array
     */
    public function findPositions(
        string $docType,
        int $docTypeId,
        string $copperNumberOption
    ): array {
        $explodedColumnName = 'explodiert_parent';
        if ($docType === 'rechnung') {
            $explodedColumnName = 'explodiert_parent_artikel';
        }

        $sql = "SELECT 
          beleg_pos.id AS `pos_id`,
          a.id AS `article_id`,
          beleg_pos.waehrung AS `currency`
          FROM `{$docType}_position` AS `beleg_pos`
          INNER JOIN `{$docType}` AS `beleg` ON beleg.id = beleg_pos.{$docType}
          INNER JOIN `artikel` AS `a` ON a.id = beleg_pos.artikel
          WHERE beleg_pos.{$docType} = :doc_type_id
          AND a.{$copperNumberOption} != ''
          AND beleg.schreibschutz = 0
          AND beleg_pos.{$explodedColumnName} = 0
          ORDER BY beleg_pos.sort";

        $result = $this->db->fetchAll($sql, ['doc_type_id' => $docTypeId]);
        if (!empty($result)) {
            return $result;
        }

        return [];
    }

    /**
     * @param string $docType
     * @param int    $doctypeId
     * @param int    $copperSurchargeArticleId
     *
     * @return array
     */
    public function findCopperSurchargeArticlePositionIds(
        string $docType,
        int $doctypeId,
        int $copperSurchargeArticleId
    ): array {
        $sql =
            "SELECT 
            beleg_pos.id AS `pos_id`
            FROM `{$docType}_position` AS `beleg_pos`
            WHERE beleg_pos.{$docType} = :doc_type_id
            AND beleg_pos.artikel = :copper_surcharge_article_id";

        return $this->db->fetchAll(
            $sql,
            ['doc_type_id' => $doctypeId, 'copper_surcharge_article_id' => $copperSurchargeArticleId]
        );
    }

    /**
     * @param int    $copperArticleId
     * @param string $copperNumberOption
     *
     * @return array
     */
    public function findPossibleCopperArticle(int $copperArticleId, string $copperNumberOption): array
    {
        $sql =
            "SELECT art.id AS `article_id`, art.{$copperNumberOption} AS `copper_number`
            FROM `artikel` AS `art`
            WHERE art.id = :copper_article_id
            AND art.{$copperNumberOption} != ''";

        $result = $this->db->fetchAll($sql, ['copper_article_id' => $copperArticleId]);
        if (!empty($result)) {
            return [
                'article_id' => $result[0]['article_id'],
                'amount'     => $this->formatToFloat($result[0]['copper_number']),
            ];
        }

        return [];
    }

    /**
     * @param string $docType
     * @param int    $docTypeId
     *
     * @return array
     */
    public function findPartListHeadArticles(string $docType, int $docTypeId): array
    {
        $sql =
            "SELECT art.id, pos.sort, pos.id AS `pos_id`, pos.waehrung AS `currency`, pos.menge AS `amount`
            FROM `{$docType}_position` AS `pos`
            INNER JOIN artikel AS `art` ON art.id = pos.artikel
            WHERE pos.{$docType} = :doc_type_id
            AND art.stueckliste = 1";

        return $this->db->fetchAll($sql, ['doc_type_id' => $docTypeId]);
    }

    /**
     * @param       $headArticleId
     * @param float $amount
     *
     * @return array
     */
    public function getAllPartListChildElements($headArticleId, float $amount = 1.0): array
    {
        $result = [];
        $sql =
            "SELECT art.id, art.stueckliste, partlist.menge AS `amount`
            FROM `artikel` AS `art`
            INNER JOIN `stueckliste` AS `partlist` ON art.id = partlist.artikel
            WHERE partlist.stuecklistevonartikel = :head_article_id";

        $datas = $this->db->fetchAll($sql, ['head_article_id' => $headArticleId]);

        foreach ($datas as $data) {
            if (!empty($data['stueckliste'])) {
                $result = array_merge(
                    $result,
                    $this->getAllPartListChildElements((int)$data['id'], (float)$data['amount'])
                );
            } else {
                $result[] = [
                    'id'     => $data['id'],
                    'amount' => $data['amount'] * $amount,
                ];
            }
        }

        return $result;
    }

    /**
     * @param int    $articleId
     * @param string $copperNumberOption
     *
     * @return float
     */
    public function getArticleCopperNumber(int $articleId, string $copperNumberOption): float
    {
        $sql =
            "SELECT a.{$copperNumberOption}
            FROM `artikel` AS `a`
            WHERE a.id = :article_id";

        return (float)str_replace(',', '.', $this->db->fetchValue($sql, ['article_id' => $articleId]));
    }

    /**
     * @param string $docType
     * @param int    $positionId
     *
     * @return int
     */
    public function evaluatePartListLastPositionId(string $docType, int $positionId): int
    {
        $explodedColumnName = 'explodiert_parent';
        if ($docType === 'rechnung') {
            $explodedColumnName = 'explodiert_parent_artikel';
        }

        $sql =
            "SELECT MAX(id) AS `pos_id`
            FROM `{$docType}_position` AS `pos`
            WHERE pos.{$explodedColumnName} = :pos_id";
        $result = $this->db->fetchValue($sql, ['pos_id' => $positionId]);
        if (!empty($result)) {
            return (int)$result;
        }

        return $positionId;
    }
}
