<?php

declare(strict_types=1);

namespace Xentral\Modules\Ebay\Service;

use Xentral\Components\Database\Database;
use Xentral\Modules\Ebay\Data\StagingListingData;
use Xentral\Modules\Ebay\Data\StagingListingPicture;
use Xentral\Modules\Ebay\Data\StagingListingVariationData;
use Xentral\Modules\Ebay\Exception\InvalidArgumentException;
use Xentral\Modules\Ebay\Exception\MissingValueException;
use Xentral\Modules\Ebay\Gateway\EbayListingGateway;
use Xentral\Modules\Ebay\Wrapper\EbayStockCalculationWrapperInterface;
use Xentral\Modules\Ebay\Wrapper\StockCalculationWrapper;

final class EbayListingService
{
    /** @var Database $db */
    private $db;

    /** @var EbayListingGateway $gateway */
    private $gateway;

    /** @var EbayListingXmlSerializer $serializer */
    private $serializer;

    /** @var StockCalculationWrapper $stockCalculationWrapper */
    private $stockCalculationWrapper;

    /**
     * @param EbayListingGateway                   $gateway
     * @param Database                             $database
     * @param EbayListingXmlSerializer             $serializer
     * @param EbayStockCalculationWrapperInterface $wrapper
     */
    public function __construct(
        EbayListingGateway $gateway,
        Database $database,
        EbayListingXmlSerializer $serializer,
        EbayStockCalculationWrapperInterface $wrapper
    ) {
        $this->gateway = $gateway;
        $this->db = $database;
        $this->serializer = $serializer;
        $this->stockCalculationWrapper = $wrapper;
    }

    /**
     * @param int $stagingListingId
     *
     * @return StagingListingData
     */
    public function associateStagingListing($stagingListingId): StagingListingData
    {
        $stagingListing = $this->gateway->getStagingListingByDatabaseId($stagingListingId);

        $stagingListing = $this->associateArticle($stagingListing);

        $stagingListing = $this->associateVariants($stagingListing);

        $this->saveStagingListing($stagingListing);

        return $stagingListing;
    }

    /**
     * @param StagingListingData $stagingListing
     *
     * @return StagingListingData
     */
    private function associateArticle(StagingListingData $stagingListing): StagingListingData
    {
        $articleId = $this->gateway->searchForMatchingArticleId($stagingListing);
        $stagingListing->setArticleId($articleId);

        return $stagingListing;
    }

    /**
     * @param StagingListingData $stagingListing
     *
     * @return StagingListingData
     */
    private function associateVariants(StagingListingData $stagingListing): StagingListingData
    {
        if (empty($stagingListing->getArticleId()) || empty($stagingListing->getVariations())) {
            return $stagingListing;
        }
        foreach ($stagingListing->getVariations() as $variation) {
            $articleId = $this->gateway->searchForMatchingArticleIdForVariation(
                $variation,
                $stagingListing->getShopId(),
                $stagingListing->getArticleId()
            );
            $variation->setArticleId($articleId);
        }

        return $stagingListing;
    }

    /**
     * @param StagingListingData $stagingListing
     *
     * @throws MissingValueException
     *
     * @return StagingListingData
     */
    public function saveStagingListing($stagingListing): StagingListingData
    {
        $stagingListingId = $stagingListing->getId();

        if (empty($stagingListingId)) {
            $query = sprintf(
                'INSERT INTO `ebay_staging_listing` (`shop_id`) VALUES (%d)',
                $stagingListing->getShopId()
            );
            $this->db->exec($query);
            $stagingListingId = $this->db->lastInsertId();
        }

        if(!empty($stagingListingId)){
            $sql = 'SELECT esl.id FROM `ebay_staging_listing` AS `esl` WHERE esl.id=:id';
            $stagingListingId = $this->db->fetchValue($sql,['id' => $stagingListingId]);
        }

        if (empty($stagingListingId)) {
            throw new MissingValueException('ID for Staging Listing dataset could not be found or created.');
        }

        $sql = 'UPDATE `ebay_staging_listing` SET
            `article_id` = :article_id,
            `type` = :listing_type,
            `title` = :title,
            `status` = :listing_status,
            `sku` = :sku,
            `description` = :description,
            `ebay_primary_category_id_external` = :primary_category,
            `ebay_secondary_category_id_external` = :secondary_category,
            `ebay_primary_store_category_id_external` = :primary_store_category,
            `ebay_secondary_store_category_id_external` = :secondary_store_category,
            `ebay_shipping_profile_id_external` = :shipping_profile,
            `ebay_payment_profile_id_external` = :payment_profile,
            `ebay_return_profile_id_external` = :return_profile,
            `ebay_plus` = :ebayplus,
            `ebay_price_suggestion` = :price_suggestion,
            `ebay_private_listing` = :private_listing,
            `condition_id_external` = :condition_id,
            `condition_display_name` = :condition_display_name,
            `condition_description` = :condition_description,
            `listing_duration` = :listing_duration,
            `inventory_tracking_method` = :inventory_tracking_method,
            `item_id_external` = :item_id,
            `delivery_time` = :delivery_time,
            `template_id` = :template_id
            WHERE id = :listing_id';
        $values = [
            'article_id'                => $stagingListing->getArticleId(),
            'listing_type'              => $stagingListing->getType(),
            'title'                     => $stagingListing->getTitle(),
            'listing_status'            => $stagingListing->getStatus(),
            'sku'                       => $stagingListing->getSku(),
            'description'               => $stagingListing->getDescription(),
            'primary_category'          => $stagingListing->getPrimaryCategoryId(),
            'secondary_category'        => $stagingListing->getSecondaryCategoryId(),
            'primary_store_category'    => $stagingListing->getPrimaryStoreCategoryId(),
            'secondary_store_category'  => $stagingListing->getSecondaryStoreCategoryId(),
            'shipping_profile'          => $stagingListing->getShippingProfileId(),
            'payment_profile'           => $stagingListing->getPaymentProfileId(),
            'return_profile'            => $stagingListing->getReturnProfileId(),
            'ebayplus'                  => $stagingListing->isEbayPlus(),
            'price_suggestion'          => $stagingListing->isPriceSuggestion(),
            'private_listing'           => $stagingListing->isPrivateListing(),
            'condition_id'              => $stagingListing->getConditionId(),
            'condition_display_name'    => $stagingListing->getConditionDisplayName(),
            'condition_description'     => $stagingListing->getConditionDescription(),
            'listing_duration'          => $stagingListing->getListingDuration(),
            'inventory_tracking_method' => $stagingListing->getInventoryTrackingMethod(),
            'item_id'                   => $stagingListing->getItemId(),
            'delivery_time'             => $stagingListing->getDeliveryTime(),
            'template_id'               => $stagingListing->getTemplateId(),
            'listing_id'                => $stagingListingId,
        ];
        $this->db->perform($sql, $values);

        foreach ($stagingListing->listSpecifics() as $specificName => $specificValue) {
            $this->saveSpecific($stagingListingId, $specificName, $specificValue);
        }

        if (!empty($stagingListing->getVariations())) {
            foreach ($stagingListing->getVariations() as $variation) {
                $this->saveStagingListingVariation($stagingListingId, $variation);
            }
        }

        if (!empty($stagingListing->listPictures())) {
            foreach ($stagingListing->listPictures() as $picture) {
                $picture->setStagingListingId($stagingListingId);
                $this->savePictureHostingServicePicture($picture);
            }
        }

        return $this->gateway->getStagingListingByDatabaseId($stagingListingId);
    }

    /**
     * @param int    $stagingListingId
     * @param string $specificName
     * @param string $specificValue
     *
     * @throws InvalidArgumentException
     */
    private function saveSpecific($stagingListingId, $specificName, $specificValue): void
    {
        if (empty($specificName)) {
            throw new InvalidArgumentException('Required argument "specificName" is empty or invalid.');
        }
        if (empty($stagingListingId)) {
            throw new InvalidArgumentException('Required argument "stagingListingId" is empty or invalid.');
        }

        $sql = 'SELECT esls.id FROM `ebay_staging_listing_specific` AS `esls` 
                WHERE `ebay_staging_listing_id` = :listing_id AND `property` = :specific_name';
        $values = [
            'listing_id'    => $stagingListingId,
            'specific_name' => $specificName,
        ];
        $specificId = $this->db->fetchValue($sql, $values);

        $sql = 'UPDATE `ebay_staging_listing_specific` SET `value` = :value WHERE `id` = :specific_id';
        $values = [
            'value'       => $specificValue,
            'specific_id' => $specificId,
        ];

        if (empty($specificId)) {
            $sql = 'INSERT INTO `ebay_staging_listing_specific` 
                (`ebay_staging_listing_id`, `property`, `value`) VALUES (:listing_id, :specific_name, :value)';
            $values = [
                'listing_id'    => $stagingListingId,
                'specific_name' => $specificName,
                'value'         => $specificValue,
            ];
        }
        $this->db->perform($sql, $values);
    }

    /**
     * @param int                         $stagingListingId
     * @param StagingListingVariationData $variation
     */
    private function saveStagingListingVariation($stagingListingId, $variation): void
    {
        $specifics = $variation->listSpecifics();
        $conditions = [];
        foreach ($specifics as $property => $value) {
            $conditions[] = sprintf(
                '(eslvs.property = %s AND eslvs.value = %s)',
                $this->db->escapeString($property),
                $this->db->escapeString($value)
            );
        }
        $condition = implode(' OR ', $conditions);
        if (empty($conditions)) {
            $condition = 0;
        }

        $query = sprintf(
            '
            SELECT eslv.id
            FROM `ebay_staging_listing_variant_specific` AS `eslvs`
            JOIN `ebay_staging_listing_variant` AS `eslv` ON eslv.id = eslvs.ebay_staging_listing_variant_id
            WHERE eslv.ebay_staging_listing_id = %s AND (%s)
            GROUP BY eslv.id
            HAVING COUNT(eslv.id) = %d',
            $stagingListingId,
            $condition,
            count($conditions)
        );
        $stagingListingVariationId = $this->db->fetchValue($query);
        if (empty($stagingListingVariationId)) {
            $query = 'INSERT INTO `ebay_staging_listing_variant` () VALUES ()';
            $this->db->exec($query);
            $stagingListingVariationId = $this->db->lastInsertId();
        }

        $sql = 'UPDATE `ebay_staging_listing_variant` SET
            `article_id` = :article_id, `sku` = :sku, `ebay_staging_listing_id` = :listing_id
            WHERE id = :variation_id';
        $values = [
            'article_id'   => $variation->getArticleId(),
            'sku'          => $variation->getSku(),
            'listing_id'   => $stagingListingId,
            'variation_id' => $stagingListingVariationId,
        ];
        $this->db->perform($sql, $values);

        foreach ($specifics as $property => $value) {
            $sql = 'SELECT `id` 
                FROM `ebay_staging_listing_variant_specific` 
                WHERE `ebay_staging_listing_variant_id`=:variation_id AND `property`=:property AND `value`=:value';
            $values = [
                'variation_id' => $stagingListingVariationId,
                'property'     => $property,
                'value'        => $value,
            ];
            $specificMissing = empty($this->db->fetchValue($sql, $values));

            if ($specificMissing) {
                $sql = 'INSERT INTO `ebay_staging_listing_variant_specific` 
                    (`ebay_staging_listing_variant_id`, `property`, `value`) 
                    VALUES (:variation_id, :property, :value)';
                $this->db->perform($sql, $values);
            }
        }
    }

    /**
     * @param StagingListingPicture $picture
     */
    private function savePictureHostingServicePicture(StagingListingPicture $picture): void
    {
        $sql = 'SELECT `id` FROM `ebay_picture_hosting_service` 
            WHERE `url`=:url 
            AND `ebay_staging_listing_id`=:listing_id 
            AND `ebay_staging_listing_variation_id`=:variation_id';
        $values = [
            'url'          => $picture->getUrl(),
            'listing_id'   => $picture->getStagingListingId(),
            'variation_id' => $picture->getStagingListingVariantId(),
        ];

        $pictureId = $this->db->fetchValue($sql, $values);
        if (!empty($pictureId)) {
            return;
        }

        $sql = 'INSERT INTO `ebay_picture_hosting_service` 
            (`ebay_staging_listing_id`, `ebay_staging_listing_variation_id`, `file_id`, `url`) 
            VALUES 
            (:listing_id,:variation_id,:file_id,:url)';
        $values = [
            'listing_id'   => $picture->getStagingListingId(),
            'variation_id' => $picture->getStagingListingVariantId(),
            'file_id'      => $picture->getFileId(),
            'url'          => $picture->getUrl(),
        ];
        $this->db->perform($sql, $values);
    }

    /**
     * @param int    $shopId
     * @param object $item
     *
     * @return StagingListingData
     */
    public function synchronizeItemData($shopId, $item): StagingListingData
    {
        $stagingListing = new StagingListingData($shopId);

        $stagingListingId = $this->gateway->tryGetStagingListingIdByItemId((string)$item->ItemID);
        if (!empty($stagingListingId)) {
            $stagingListing = $this->gateway->getStagingListingByDatabaseId($stagingListingId);
        }

        $listingStatus = 'Aktiv';
        if (!empty($item->ListingDetails->EndingReason)) {
            $listingStatus = 'Beendet';
        }
        $stagingListing->setType((string)$item->ListingType);
        $stagingListing->setTitle((string)$item->Title);
        $stagingListing->setSku((string)$item->SKU);
        $stagingListing->setDescription('');
        $stagingListing->setPrimaryCategoryId((string)$item->PrimaryCategory->CategoryID);
        $stagingListing->setPrimaryStoreCategoryId((string)$item->Storefront->StoreCategoryID);
        $stagingListing->setSecondaryStoreCategoryId((string)$item->Storefront->StoreCategoryID2);
        $stagingListing->setShippingProfileId((string)$item->SellerProfiles->SellerShippingProfile->ShippingProfileID);
        $stagingListing->setPaymentProfileId((string)$item->SellerProfiles->SellerPaymentProfile->PaymentProfileID);
        $stagingListing->setReturnProfileId((string)$item->SellerProfiles->SellerReturnProfile->ReturnProfileID);
        $stagingListing->setDeliveryTime((string)$item->DispatchTimeMax);
        $stagingListing->setItemId((string)$item->ItemID);
        $stagingListing->setInventoryTrackingMethod((string)$item->InventoryTrackingMethod);
        $stagingListing->setConditionId((string)$item->ConditionID);
        $stagingListing->setListingDuration((string)$item->ListingDuration);
        $stagingListing->setConditionDisplayName((string)$item->ConditionDisplayName);
        $stagingListing->setConditionDescription((string)$item->ConditionDescription);
        $stagingListing->setPrivateListing(strtolower((string)$item->PrivateListing) === 'true');
        $stagingListing->setEbayPlus(strtolower((string)$item->eBayPlus) === 'true');
        $stagingListing->setStatus($listingStatus);

        if (!empty($item->ItemSpecifics->NameValueList)) {
            $stagingListing->setSpecifics([]);
            foreach ($item->ItemSpecifics->NameValueList as $itemSpecific) {
                $stagingListing->addSpecific((string)$itemSpecific->Name, (string)$itemSpecific->Value);
            }
        }

        if (!empty($item->PictureDetails->PictureURL)) {
            foreach ($item->PictureDetails->PictureURL as $pictureUrl) {
                $stagingListing->addPicture(
                    new StagingListingPicture(
                        str_replace('$_1.', '$_10.', (string)$pictureUrl)
                    )
                );
            }
        }

        if (!empty($item->Variations)) {
            foreach ($item->Variations->Variation as $variation) {
                $stagingListingVariation = new StagingListingVariationData();
                $stagingListingVariation->setSku((string)$variation->SKU);
                foreach ($variation->VariationSpecifics->NameValueList as $specifics) {
                    $stagingListingVariation->addSpecifics((string)$specifics->Name, (string)$specifics->Value);
                }
                $stagingListing->addVariation($stagingListingVariation);
            }
        }

        return $this->saveStagingListing($stagingListing);
    }

    /**
     * @param int $stagingId
     *
     * @return string
     */
    public function getStockSyncBody($stagingId): string
    {
        $staging = $this->gateway->getStagingListingByDatabaseId($stagingId);

        $stocksForArticles = [];

        $stocksForArticles[$staging->getArticleId()] = $this->stockCalculationWrapper->calculateStock(
            $staging->getArticleId(),
            $staging->getShopId()
        );
        foreach ($staging->getVariations() as $variation) {
            if (!empty($variation->getArticleId()) && !isset($stocksForArticles[$variation->getArticleId()])) {
                $stocksForArticles[$variation->getArticleId()] = $this->stockCalculationWrapper->calculateStock(
                    $variation->getArticleId(),
                    $staging->getShopId()
                );
            }
        }

        return $this->serializer->createStockSyncXmlString($staging, $stocksForArticles);
    }
}
