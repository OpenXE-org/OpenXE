<?php

declare(strict_types=1);

namespace Xentral\Modules\Ebay\Gateway;

use Xentral\Components\Database\Database;
use Xentral\Modules\Ebay\Data\StagingListingData;
use Xentral\Modules\Ebay\Data\StagingListingVariationData;
use Xentral\Modules\Ebay\Exception\InvalidArgumentException;
use Xentral\Modules\Ebay\Exception\ValidationFailedException;

final class EbayListingGateway
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
     * @param int $itemId
     *
     * @throws ValidationFailedException
     *
     * @return int
     */
    public function tryGetStagingListingIdByItemId($itemId): int
    {
        $sql = 'SELECT e.* 
                FROM `ebay_staging_listing` AS `e` 
                WHERE e.item_id_external = :item_id';
        $values = ['item_id' => $itemId];
        $stagingListingData = $this->db->fetchRow($sql, $values);

        if (empty($stagingListingData)) {
            return 0;
        }

        return $stagingListingData['id'];
    }

    /**
     * @param int $id
     *
     * @throws InvalidArgumentException
     * @throws ValidationFailedException
     *
     * @return StagingListingData
     */
    public function getStagingListingByDatabaseId(int $id): StagingListingData
    {
        $sql = 'SELECT e.* 
                FROM `ebay_staging_listing` AS `e` 
                WHERE e.id = :listing_id';
        $values = ['listing_id' => $id];
        $stagingListingData = $this->db->fetchRow($sql, $values);

        if (empty($stagingListingData)) {
            throw new InvalidArgumentException('Required argument "id" is empty or invalid.');
        }
        $stagingListing = StagingListingData::fromDbState($stagingListingData);
        $stagingListing->setVariations($this->getStagingListingVariations($stagingListing->getId()));

        $validationErrors = $stagingListing->validate();
        if (!empty($validationErrors)) {
            throw ValidationFailedException::fromErrors($validationErrors);
        }

        return $stagingListing;
    }

    /**
     * @param int $stagingListingId
     *
     * @throws InvalidArgumentException
     * @return array
     */
    protected function getStagingListingVariations(int $stagingListingId): array
    {
        if (empty($stagingListingId)) {
            throw new InvalidArgumentException('Required argument "stagingListingId" is empty or invalid.');
        }

        $variations = [];
        $sql = 'SELECT e.id, e.sku, e.ebay_staging_listing_id, e.article_id 
                FROM `ebay_staging_listing_variant` AS `e`
                WHERE e.ebay_staging_listing_id = :listing_id';
        $values = ['listing_id' => $stagingListingId];
        $stagingListingVariations = $this->db->fetchAll($sql, $values);

        if (empty($stagingListingVariations)) {
            return $variations;
        }

        foreach ($stagingListingVariations as $variation) {
            $sql = 'SELECT e.property, e.value
                    FROM ebay_staging_listing_variant_specific AS `e`
                    WHERE e.ebay_staging_listing_variant_id = :variation_id';
            $values = ['variation_id' => $variation['id']];
            $allSpecificsInDatabase = $this->db->fetchAll($sql, $values);

            $specifics = [];
            foreach ($allSpecificsInDatabase as $specificsInDatabase) {
                $specifics[$specificsInDatabase['property']] = $specificsInDatabase['value'];
            }
            $stagingListingVariation = StagingListingVariationData::fromArray($variation, $specifics);
            $variations[] = $stagingListingVariation;
        }

        return $variations;
    }

    /**
     * @param int $shopId
     *
     * @return array
     */
    public function getPaymentBusinessPolicies($shopId): array
    {
        return $this->findBusinessPolicies($shopId, 'PAYMENT');
    }

    /**
     * @param int    $shopId
     * @param string $type
     *
     * @throws InvalidArgumentException
     *
     * @return array
     */
    protected function findBusinessPolicies($shopId, $type): array
    {
        if (empty($shopId)) {
            throw new InvalidArgumentException('Required argument "shopId" is empty or invalid.');
        }
        if (empty($type)) {
            throw new InvalidArgumentException('Required argument "type" is empty.');
        }

        $sql = 'SELECT 
                    e.id, e.aktiv AS active, e.profilid AS profile_id_external, 
                    e.profilname AS profile_name, e.profilsummary AS profile_summary 
                FROM ebay_rahmenbedingungen AS `e` 
                WHERE e.shop=:shopid AND e.profiltype=:profiltype';
        $values = [
            'shopid'     => $shopId,
            'profiltype' => $type,
        ];

        return $this->db->fetchAll($sql, $values);
    }

    /**
     * @param int $shopId
     *
     * @return array
     */
    public function getShippingBusinessPolicies($shopId): array
    {
        return $this->findBusinessPolicies($shopId, 'SHIPPING');
    }

    /**
     * @param int $shopId
     *
     * @return array
     */
    public function getReturnBusinessPolicies($shopId): array
    {
        return $this->findBusinessPolicies($shopId, 'RETURN_POLICY');
    }

    /**
     * @return array
     */
    public function getTemplates(): array
    {
        $sql = 'SELECT e.id, e.bezeichnung AS template_name
                FROM `ebay_template` AS `e` 
                WHERE e.aktiv = 1';

        return $this->db->fetchAll($sql);
    }

    /**
     * @param int $shopId
     *
     * @throws InvalidArgumentException
     * @return array
     */
    public function getStoreCategories(int $shopId): array
    {
        if ($shopId <= 0) {
            throw new InvalidArgumentException('Required argument "shopId" is invalid.');
        }

        $sql = 'SELECT e.id, e.kategorie AS `category_id_external`, e.bezeichnung AS description
            FROM `ebay_storekategorien` AS `e` 
            WHERE shop = :shop_id';
        $values = ['shop_id' => $shopId];

        return $this->db->fetchAll($sql, $values);
    }

    /**
     * @param StagingListingData $stagingListing
     *
     * @return int
     */
    public function searchForMatchingArticleId(StagingListingData $stagingListing): int
    {
        if (!empty($stagingListing->getArticleId())) {
            return $stagingListing->getArticleId();
        }

        //associate by listing id in foreign numbers
        $sql =
            "SELECT af.artikel 
            FROM `artikelnummer_fremdnummern` AS `af`
            JOIN `artikel` AS `a` ON af.artikel = a.id
            WHERE af.shopid = :shop_id AND LOWER(af.bezeichnung) = 'ebaylisting' AND af.nummer = :article_number
              AND a.nummer <> 'DEL' AND a.geloescht = 0 AND a.intern_gesperrt = 0 
            LIMIT 1";
        $values = [
            'shop_id'        => $stagingListing->getShopId(),
            'article_number' => $stagingListing->getItemId(),
        ];
        $articleId = $this->db->fetchValue($sql, $values);

        if (empty($articleId)) {
            //associate by sku
            $sql = 'SELECT a.id 
                FROM `artikel` AS `a`
                WHERE a.geloescht = 0 AND a.intern_gesperrt=0 AND a.nummer=:article_number';
            $values = ['article_number' => $stagingListing->getSku()];

            $articleId = $this->db->fetchValue($sql, $values);
        }

        if (empty($articleId)) {
            //associate by foreign number
            $sql = "SELECT a.id 
                FROM `artikel` AS `a`
                JOIN `artikelnummer_fremdnummern` AS `af` ON af.artikel = a.id
                WHERE a.geloescht = 0 AND a.intern_gesperrt = 0 AND a.nummer <> ''
                AND af.aktiv = 1 AND (LOWER(af.bezeichnung) = 'sku' OR LOWER(af.bezeichnung) = 'bestandseinheit') 
                AND af.nummer = :article_number AND (af.shopid = :shop_id OR af.shopid = 0)
                ORDER BY af.shopid DESC LIMIT 1";
            $values = [
                'article_number' => $stagingListing->getSku(),
                'shop_id'        => $stagingListing->getShopId(),
            ];
            $articleId = $this->db->fetchValue($sql, $values);
        }

        return (int)$articleId;
    }

    /**
     * @param StagingListingVariationData $variation
     * @param int                         $shopId
     * @param int                         $parentArticleId
     *
     * @return int
     */
    public function searchForMatchingArticleIdForVariation(
        StagingListingVariationData $variation,
        int $shopId,
        int $parentArticleId
    ): int {
        if (!empty($variation->getArticleId())) {
            return $variation->getArticleId();
        }
        //associate by sku
        $sql = 'SELECT a.id 
                FROM `artikel` AS a
                WHERE a.geloescht = 0 AND a.intern_gesperrt = 0 AND a.nummer = :article_number';
        $values = ['article_number' => $variation->getSku()];
        $articleId = $this->db->fetchValue($sql, $values);

        if (empty($articleId)) {
            //associate by foreign number
            $sql = "SELECT a.id 
                FROM `artikel` AS `a`
                JOIN `artikelnummer_fremdnummern` AS `af` ON af.artikel = a.id
                WHERE a.geloescht = 0 AND a.intern_gesperrt = 0 AND a.nummer <> ''
                AND af.aktiv = 1 AND (LOWER(af.bezeichnung) = 'sku' OR LOWER(af.bezeichnung) = 'bestandseinheit') 
                AND af.nummer = :sku AND (af.shopid = :shop_id OR af.shopid = 0)
                ORDER BY af.shopid DESC LIMIT 1";
            $values = [
                'sku'     => $variation->getSku(),
                'shop_id' => $shopId,
            ];
            $articleId = $this->db->fetchValue($sql, $values);
        }

        if (empty($articleId)) {
            //associate by matrix combination
            $specifics = [];
            foreach ($variation->listSpecifics() as $dimension => $value) {
                $specifics[] = sprintf('(ma.name = %s AND mea.name = %s)',
                                       $this->db->escapeString($dimension),
                                       $this->db->escapeString($value));
            }
            $query = sprintf(
                '
                SELECT x.artikel AS `artikelId`
                FROM (
                     SELECT moza.artikel 
                     FROM `matrixprodukt_eigenschaftengruppen_artikel` AS `ma`
                     JOIN `matrixprodukt_eigenschaftenoptionen_artikel` AS `mea` on ma.id = mea.gruppe
                     JOIN `matrixprodukt_optionen_zu_artikel` AS `moza` ON moza.option_id = mea.id
                     WHERE ma.artikel = %d AND (%s)
                 ) AS `x` 
                GROUP BY x.artikel 
                HAVING COUNT(x.artikel) = %d',
                $parentArticleId,
                implode(' OR ', $specifics),
                count($specifics)
            );
            $foundCombinations = $this->db->fetchAll($query);
            if (count($foundCombinations) === 1) {
                $articleId = $foundCombinations[0]['artikelId'];
            }
        }

        return (int)$articleId;
    }

    /**
     * @param int $articleId
     * @param int $shopId
     *
     * @return bool
     */
    public function existsStagingListingsForArticleId(int $articleId, int $shopId): bool
    {
        $sql = "SELECT e.id 
                FROM `ebay_staging_listing` AS `e`
                LEFT JOIN `ebay_staging_listing_variant` AS `v` ON e.id = v.ebay_staging_listing_id 
                WHERE (e.article_id = :article_id or v.article_id = :article_id) AND e.shop_id = :shop_id AND e.status = 'Aktiv'";
        $values = [
            'article_id' => $articleId,
            'shop_id' => $shopId
        ];
        $stagingId = $this->db->fetchValue($sql, $values);

        return !empty($stagingId);
    }
}
