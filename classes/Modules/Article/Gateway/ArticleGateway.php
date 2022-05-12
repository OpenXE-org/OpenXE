<?php

namespace Xentral\Modules\Article\Gateway;

use Xentral\Components\Database\Database;
use Xentral\Modules\Article\Exception\InvalidArgumentException;
use Xentral\Modules\Article\Exception\SellingPriceNotFoundException;

final class ArticleGateway
{
    /** @var Database $db */
    private $db;

    /**
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->db = $database;
    }

    /**
     * @param int $articleId
     *
     * @return bool
     */
    public function isPartsListArticle($articleId)
    {
        $this->ensureArticleId($articleId);

        $isPartsList = $this->db->fetchValue(
            'SELECT a.stueckliste FROM artikel AS a WHERE a.id = :article_id',
            ['article_id' => (int)$articleId]
        );

        return (int)$isPartsList === 1;
    }

    /**
     * @param $sellingPriceId
     *
     * @throws SellingPriceNotFoundException
     * @return array
     */
    public function getSellingPriceById($sellingPriceId)
    {
        $this->ensurePriceId($sellingPriceId);
        $sellingPrice = $this->db->fetchRow(
            'SELECT sp.`id`, sp.`preis` AS `price`, sp.`waehrung` AS `currency`, sp.`ab_menge` AS `quantity_from`, 
                sp.gueltig_bis AS `valid_to`, sp.gruppe AS `price_group_id`, sp.`art` AS `price_type`, 
                sp.adresse AS `address_id` 
            FROM verkaufspreise  AS sp
            WHERE sp.id = :id',
            ['id' => $sellingPriceId]
        );
        if (empty($sellingPrice)) {
            throw new SellingPriceNotFoundException('Sellingprice not found: ID' . $sellingPriceId);
        }

        return $sellingPrice;
    }

    /**
     * @param int   $articleId
     * @param float $quantityFrom
     *
     * @return array
     */
    public function findLastStandardSellingPrice($articleId, $quantityFrom = 0.0)
    {
        $this->ensureArticleId($articleId);

        return $this->db->fetchRow(
            "SELECT sp.`id`, sp.`preis` AS `price`, sp.`waehrung` AS `currency`, sp.`ab_menge` AS `quantity_from`
            FROM `verkaufspreise` AS sp
            WHERE sp.`artikel` = :article_id 
            AND (
                sp.`gueltig_bis` >= CURDATE() 
                OR IFNULL(sp.`gueltig_bis`, '0000-00-00') = '0000-00-00'
            )
            AND sp.`adresse` = 0 AND sp.`art` <> 'preisgruppe' 
            AND sp.`ab_menge` = :quantity_from
            ORDER BY sp.`id` DESC
            LIMIT 1",
            [
                'article_id'    => (int)$articleId,
                'quantity_from' => (float)$quantityFrom,
            ]
        );
    }

    /**
     * @param int   $aricleId
     * @param int   $currencyCode
     * @param float $quantityFrom
     *
     * @return array
     */
    public function findLastStandardSellingPriceByCurrency($aricleId, $currencyCode, $quantityFrom = 0.0)
    {
        return [];
    }

    /**
     * @param int   $aricleId
     * @param int   $currencyCode
     * @param float $quantityFrom
     *
     * @return array
     */
    public function findMinimumStandardSellingPriceByCurrency($aricleId, $currencyCode, $quantityFrom = 0.0)
    {
        return [];
    }

    /**
     * @param int   $articleId
     * @param float $quantityFrom
     *
     * @return array
     */
    public function findStandardSellingPrices($articleId, $quantityFrom = 0.0)
    {
        $this->ensureArticleId($articleId);

        return $this->db->fetchAll(
            "SELECT sp.`id`, sp.`preis` AS `price`, sp.`waehrung` AS `currency`, sp.`ab_menge` AS `quantity_from` 
            FROM `verkaufspreise` AS sp 
            WHERE sp.`artikel` = :article_id 
                AND (
                    sp.`gueltig_bis` >= CURDATE() 
                    OR IFNULL(sp.`gueltig_bis`, '0000-00-00') = '0000-00-00'
                )
                AND sp.`adresse` = 0 AND sp.`art` <> 'preisgruppe' 
                AND sp.`ab_menge` = :quantity_from
            ORDER BY sp.`ab_menge`, sp.`preis`",
            [
                'article_id'    => (int)$articleId,
                'quantity_from' => (float)$quantityFrom,
            ]
        );
    }

    /**
     * @param int   $articleId
     * @param int   $addressId
     * @param float $quantityFrom
     *
     * @return array
     */
    public function findLastCustomerSellingPrice($articleId, $addressId, $quantityFrom = 0.0)
    {
        $this->ensureArticleId($articleId);

        return $this->db->fetchRow(
            "SELECT sp.`id`, sp.`preis` AS `price`, sp.`waehrung` AS `currency`, sp.`ab_menge` AS `quantity_from`
            FROM `verkaufspreise` AS sp
            WHERE sp.`artikel` = :article_id 
                AND 
                (
                    sp.`gueltig_bis` >= CURDATE() 
                    OR IFNULL(sp.`gueltig_bis`, '0000-00-00') = '0000-00-00'
                )
                AND sp.`adresse` = :address_id AND sp.`art` <> 'preisgruppe' 
                AND ab_menge = :quantity_from
            ORDER BY sp.`id` DESC
            LIMIT 1",
            [
                'address_id'    => (int)$addressId,
                'article_id'    => (int)$articleId,
                'quantity_from' => (float)$quantityFrom,
            ]
        );
    }

    /**
     * @param int   $articleId
     * @param int   $addressId
     * @param float $quantityFrom
     *
     * @return array
     */
    public function findCustomerSellingPrices($articleId, $addressId, $quantityFrom = 0.0)
    {
        $this->ensureArticleId($articleId);

        return $this->db->fetchAll(
            "SELECT sp.`id`, sp.`preis` AS `price`, sp.`waehrung` AS `currency`, sp.`ab_menge` AS `quantity_from`
            FROM `verkaufspreise` AS sp
            WHERE sp.`artikel` = :article_id 
                AND 
                (
                    sp.`gueltig_bis` >= CURDATE() 
                    OR IFNULL(sp.`gueltig_bis`, '0000-00-00') = '0000-00-00'
                )
                AND sp.`adresse` = :address_id AND sp.`art` <> 'preisgruppe'
                AND sp.`ab_menge` >= :quantity_from
            ORDER BY sp.`ab_menge`, sp.`preis`",
            [
                'address_id'    => (int)$addressId,
                'article_id'    => (int)$articleId,
                'quantity_from' => (float)$quantityFrom,
            ]
        );
    }

    /**
     * @param int   $articleId
     * @param int   $groupId
     * @param float $quantityFrom
     *
     * @return array
     */
    public function findGroupSellingPrices($articleId, $groupId, $quantityFrom = 0.0)
    {
        $this->ensureArticleId($articleId);

        return $this->db->fetchAll(
            "SELECT sp.`id`, sp.`preis` AS `price`, sp.`waehrung` AS `currency`, sp.`ab_menge` AS `quantity_from`
            FROM `verkaufspreise` AS sp
            WHERE sp.`artikel` = :article_id 
                AND 
                (
                    sp.`gueltig_bis` >= CURDATE() 
                    OR  IFNULL(sp.`gueltig_bis`, '0000-00-00') = '0000-00-00'
                )  
                AND sp.gruppe = :group_id AND sp.`gruppe` = 0 AND sp.`art` = 'preisgruppe'
                AND sp.`ab_menge` >= :quantity_from
            ORDER BY sp.`ab_menge`, sp.`preis`",
            [
                'group_id'      => (int)$groupId,
                'article_id'    => (int)$articleId,
                'quantity_from' => (float)$quantityFrom,
            ]
        );
    }

    public function findMinimumGroupSellingPriceByCurrency(
        $aricleId,
        $sellingGroupId,
        $currencyCode,
        $quantityFrom = 0.0
    ) {
    }

    public function findStandardSellingPricesCollectionByArticleIds($articleIds, $quantityFrom = 0.0)
    {
    }

    /**
     * Finds an article-ID by searching in numbers, eans, manufacturing numbers and foreign numbers.
     * Especially for scannable articles
     *
     * @param string $number
     *
     * @return array
     */
    public function findScannableArticle($number)
    {
        $sql = 'SELECT a.id, a.name_de 
        FROM `artikel` AS `a`
        LEFT JOIN `artikelnummer_fremdnummern` AS `af` ON a.id = af.artikel AND af.aktiv = 1 AND af.scannable = 1
        WHERE a.nummer=:number 
        OR a.ean=:number 
        OR a.herstellernummer=:number
        OR af.nummer = :number LIMIT 1';

        return $this->db->fetchRow($sql, ['number' => $number]);
    }

    /**
     * Finds an uniqie Article-Id by serarching Serial numbers in Storages
     *
     * @param string $serialnumber
     *
     * @return array
     */
    public function findUniqueArticleBySerial($serialnumber)
    {
        $sql = "SELECT DISTINCT art.id, art.name_de
            FROM `lager_seriennummern` AS `ls`
            INNER JOIN `artikel` AS `art` 
                ON ls.artikel = art.id AND art.geloescht <> 1 
                       AND art.seriennummern <> '' AND art.seriennummern <> 'keine' 
            WHERE ls.seriennummer <> '' AND ls.seriennummer = :number";

        $result = $this->db->fetchAll($sql, ['number' => $serialnumber]);
        if(count($result) !== 1) {
            return [];
        }

        return array_pop($result);
    }

    /**
     * Finds an article-ID by searching in numbers, eans, manufacturing numbers and foreign numbers.
     * It must have a purchase price.
     * Especially for scannable articles
     *
     * @param string $number
     *
     * @return array
     */
    public function findScannableOrderPurchaseArticle($number, $purchaseOrderId)
    {
        $sql = 'SELECT a.id, a.name_de 
        FROM `artikel` AS `a`
        LEFT JOIN `artikelnummer_fremdnummern` AS `af` ON a.id = af.artikel AND af.aktiv = 1 AND af.scannable = 1
        INNER JOIN `einkaufspreise` ek ON ek.artikel = a.id
        INNER JOIN `bestellung` b ON b.adresse = ek.adresse 
        WHERE (
            a.nummer=:number 
            OR a.ean=:number 
            OR a.herstellernummer=:number
            OR af.nummer = :number
        )
        AND b.id=:purchaseOrderId LIMIT 1';

        return $this->db->fetchRow($sql, ['number' => $number, 'purchaseOrderId' => $purchaseOrderId]);
    }

    /**
     * @param int $articleId
     *
     * @throws InvalidArgumentException
     */
    private function ensureArticleId($articleId)
    {
        if (empty($articleId) || (int)$articleId < 0) {
            throw new InvalidArgumentException(
                'Required argument "articleId" is empty or invalid.'
            );
        }
    }

    /**
     * @param int $sellingPriceId
     *
     * @throws InvalidArgumentException
     */
    private function ensurePriceId($sellingPriceId)
    {
        if (empty($sellingPriceId) || (int)$sellingPriceId < 0) {
            throw new InvalidArgumentException(
                'Required argument "SellingId" is empty or invalid.'
            );
        }
    }
}
