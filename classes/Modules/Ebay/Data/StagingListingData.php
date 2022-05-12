<?php

declare(strict_types=1);

namespace Xentral\Modules\Ebay\Data;

use Xentral\Modules\Ebay\Exception\InvalidArgumentException;

final class StagingListingData
{
    /** @var int $id */
    private $id;

    /** @var int $articleId */
    private $articleId = 0;

    /** @var string $type */
    private $type = '';

    /** @var string $status */
    private $status = '';

    /** @var string $sku */
    private $sku = '';

    /** @var string $title */
    private $title = '';

    /** @var string $description */
    private $description = '';

    /** @var string $primaryCategoryId */
    private $primaryCategoryId = '';

    /** @var string $secondaryCategoryId */
    private $secondaryCategoryId = '';

    /** @var string $primaryStoreCategoryId */
    private $primaryStoreCategoryId = '';

    /** @var string $secondaryStoreCategoryId */
    private $secondaryStoreCategoryId = '';

    /** @var string $shippingProfileId */
    private $shippingProfileId = '';

    /** @var string $returnProfileId */
    private $returnProfileId = '';

    /** @var string $paymentProfileId */
    private $paymentProfileId = '';

    /** @var string $deliveryTime */
    private $deliveryTime = '';

    /** @var string $itemId */
    private $itemId = '';

    /** @var string $inventoryTrackingMethod */
    private $inventoryTrackingMethod = '';

    /** @var string $conditionId */
    private $conditionId = '0';

    /** @var string $conditionDisplayName */
    private $conditionDisplayName = '';

    /** @var string $conditionDescription */
    private $conditionDescription = '';

    /** @var string $listingDuration */
    private $listingDuration = '';

    /** @var bool $ebayPlus */
    private $ebayPlus = false;

    /** @var bool $privateListing */
    private $privateListing = false;

    /** @var bool $priceSuggestion */
    private $priceSuggestion = false;

    /** @var int $shopId */
    private $shopId;

    /** @var int $templateId */
    private $templateId = 0;

    /** @var array $variations */
    private $variations = [];

    /** @var array $specifics */
    private $specifics = [];

    /** @var array $pictures */
    private $pictures = [];

    /**
     * StagingListing constructor.
     *
     * @param int $shopId
     * @param int $databaseId
     *
     * @throws InvalidArgumentException
     */
    public function __construct($shopId, $databaseId = 0)
    {
        if (empty($shopId)) {
            throw new InvalidArgumentException('Required argument "shopId" is empty.');
        }
        $this->shopId = (int)$shopId;
        $this->id = $databaseId;
    }

    /**
     * @param array $data
     *
     * @return StagingListingData
     */
    public static function fromDbState($data): StagingListingData
    {
        $stagingListing = new StagingListingData($data['shop_id'], $data['id']);

        $stagingListing->setArticleId($data['article_id']);
        $stagingListing->setType($data['type']);
        $stagingListing->setTitle($data['title']);
        $stagingListing->setStatus($data['status']);
        $stagingListing->setSku($data['sku']);
        $stagingListing->setDescription($data['description']);
        $stagingListing->setPrimaryCategoryId($data['ebay_primary_category_id_external']);
        $stagingListing->setSecondaryCategoryId($data['ebay_secondary_category_id_external']);
        $stagingListing->setShippingProfileId($data['ebay_shipping_profile_id_external']);
        $stagingListing->setPaymentProfileId($data['ebay_payment_profile_id_external']);
        $stagingListing->setReturnProfileId($data['ebay_return_profile_id_external']);
        $stagingListing->setPrivateListing(!empty($data['ebay_private_listing']));
        $stagingListing->setDeliveryTime($data['delivery_time']);
        $stagingListing->setInventoryTrackingMethod($data['inventory_tracking_method']);
        $stagingListing->setConditionId($data['condition_id_external']);
        $stagingListing->setConditionDisplayName($data['condition_display_name']);
        $stagingListing->setConditionDescription($data['condition_description']);
        $stagingListing->setListingDuration($data['listing_duration']);
        $stagingListing->setDeliveryTime($data['delivery_time']);
        $stagingListing->setEbayPlus((bool)$data['ebay_plus']);
        $stagingListing->setPriceSuggestion((bool)$data['ebay_price_suggestion']);
        $stagingListing->setItemId($data['item_id_external']);
        $stagingListing->setTemplateId((int)$data['template_id']);

        return $stagingListing;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     */
    public function setDescription($description): void
    {
        $this->description = (string)$description;
    }

    /**
     * @return int
     */
    public function getShopId(): int
    {
        return $this->shopId;
    }

    /**
     * @return array
     */
    public function listSpecifics(): array
    {
        return $this->specifics;
    }

    /**
     * @param array $specifics
     */
    public function setSpecifics($specifics): void
    {
        $this->specifics = $specifics;
    }

    /**
     * @param string $specificName
     * @param string $specificValue
     */
    public function addSpecific($specificName, $specificValue = ''): void
    {
        if (!empty($specificName)) {
            $this->specifics[$specificName] = $specificValue;
        }
    }

    /**
     * @return array
     */
    public function listPictures(): array
    {
        return $this->pictures;
    }


    /**
     * @param StagingListingPicture $picture
     */
    public function addPicture(StagingListingPicture $picture): void
    {
        $pictureExists = false;
        foreach ($this->pictures as $existingPicture) {
            if ($existingPicture->getUrl() === $picture->getUrl()) {
                $pictureExists = true;
                break;
            }
        }

        if (!$pictureExists) {
            $this->pictures[] = $picture;
        }
    }

    /**
     * @return string
     */
    public function getPrimaryStoreCategoryId(): string
    {
        return $this->primaryStoreCategoryId;
    }

    /**
     * @param string $primaryStoreCategoryId
     */
    public function setPrimaryStoreCategoryId($primaryStoreCategoryId): void
    {
        $this->primaryStoreCategoryId = $primaryStoreCategoryId;
    }

    /**
     * @return string
     */
    public function getSecondaryStoreCategoryId(): string
    {
        return $this->secondaryStoreCategoryId;
    }

    /**
     * @param string $secondaryStoreCategoryId
     */
    public function setSecondaryStoreCategoryId($secondaryStoreCategoryId): void
    {
        $this->secondaryStoreCategoryId = $secondaryStoreCategoryId;
    }

    /**
     * @param StagingListingVariationData $variation
     */
    public function addVariation($variation): void
    {
        if (empty($variation->listSpecifics())) {
            return;
        }

        $variationExists = false;
        foreach ($this->variations as $existingVariation) {
            $existingSpecifics = $existingVariation->listSpecifics();
            $variationExists = $this->identicalVariationExistsWithinListing($variation, $existingSpecifics);
            if ($variationExists) {
                break;
            }
        }

        if (!$variationExists) {
            $this->variations[] = $variation;
        }
    }

    /**
     * @param StagingListingVariationData $variation
     * @param array                       $existingSpecifics
     *
     * @return bool
     */
    private function identicalVariationExistsWithinListing($variation, $existingSpecifics): bool
    {
        $variationExists = false;
        foreach ($variation->listSpecifics() as $propertyToCheck => $valueToCheck) {
            $variationExists = false;
            if (isset($existingSpecifics[$propertyToCheck]) && $existingSpecifics[$propertyToCheck] === $valueToCheck) {
                $variationExists = true;
            } else {
                break;
            }
        }

        return $variationExists;
    }

    /**
     * @return array
     */
    public function getVariations(): array
    {
        return $this->variations;
    }

    /**
     * @param array $variations
     */
    public function setVariations($variations): void
    {
        $this->variations = $variations;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id'                                  => $this->getId(),
            'article_id'                          => $this->getArticleId(),
            'type'                                => $this->getType(),
            'title'                               => $this->getTitle(),
            'status'                              => $this->getStatus(),
            'sku'                                 => $this->getSku(),
            'ebay_primary_category_id_external'   => $this->getPrimaryCategoryId(),
            'ebay_secondary_category_id_external' => $this->getSecondaryCategoryId(),
            'ebay_shipping_profile_id_external'   => $this->getShippingProfileId(),
            'ebay_payment_profile_id_external'    => $this->getPaymentProfileId(),
            'ebay_return_profile_id_external'     => $this->getReturnProfileId(),
            'item_id_external'                    => $this->getItemId(),
            'delivery_time'                       => $this->getDeliveryTime(),
            'inventory_tracking_method'           => $this->getInventoryTrackingMethod(),
            'condition_id_external'               => $this->getConditionId(),
            'condition_display_name'              => $this->getConditionDisplayName(),
            'condition_description'               => $this->getConditionDescription(),
            'listing_duration'                    => $this->getListingDuration(),
            'ebay_plus'                           => $this->isEbayPlus(),
            'ebay_price_suggestion'               => $this->isPriceSuggestion(),
            'ebay_private_listing'                => $this->isPrivateListing(),
            'variations'                          => $this->getVariationsAsArray(),
            'template_id'                         => $this->getTemplateId(),
        ];
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getArticleId(): int
    {
        return $this->articleId;
    }

    /**
     * @param int $articleId
     */
    public function setArticleId($articleId): void
    {
        $this->articleId = (int)$articleId;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type): void
    {
        $this->type = (string)$type;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title): void
    {
        $this->title = (string)$title;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status): void
    {
        $this->status = (string)$status;
    }

    /**
     * @return string
     */
    public function getSku(): string
    {
        return $this->sku;
    }

    /**
     * @param string $sku
     */
    public function setSku($sku): void
    {
        $this->sku = (string)$sku;
    }

    /**
     * @return string
     */
    public function getPrimaryCategoryId(): string
    {
        return $this->primaryCategoryId;
    }

    /**
     * @param string $primaryCategoryId
     */
    public function setPrimaryCategoryId($primaryCategoryId): void
    {
        $this->primaryCategoryId = (string)$primaryCategoryId;
    }

    /**
     * @return string
     */
    public function getSecondaryCategoryId(): string
    {
        return $this->secondaryCategoryId;
    }

    /**
     * @param string $secondaryCategoryId
     */
    public function setSecondaryCategoryId($secondaryCategoryId): void
    {
        $this->secondaryCategoryId = (string)$secondaryCategoryId;
    }

    /**
     * @return string
     */
    public function getShippingProfileId(): string
    {
        return $this->shippingProfileId;
    }

    /**
     * @param string $shippingProfileId
     */
    public function setShippingProfileId($shippingProfileId): void
    {
        $this->shippingProfileId = (string)$shippingProfileId;
    }

    /**
     * @return string
     */
    public function getPaymentProfileId(): string
    {
        return $this->paymentProfileId;
    }

    /**
     * @param string $paymentProfileId
     */
    public function setPaymentProfileId($paymentProfileId): void
    {
        $this->paymentProfileId = (string)$paymentProfileId;
    }

    /**
     * @return string
     */
    public function getReturnProfileId(): string
    {
        return $this->returnProfileId;
    }

    /**
     * @param string $returnProfileId
     */
    public function setReturnProfileId($returnProfileId): void
    {
        $this->returnProfileId = (string)$returnProfileId;
    }

    /**
     * @return string
     */
    public function getItemId(): string
    {
        return $this->itemId;
    }

    /**
     * @param string $itemId
     */
    public function setItemId($itemId): void
    {
        $this->itemId = (string)$itemId;
    }

    /**
     * @return string
     */
    public function getDeliveryTime(): string
    {
        return $this->deliveryTime;
    }

    /**
     * @param string $deliveryTime
     */
    public function setDeliveryTime($deliveryTime): void
    {
        $this->deliveryTime = $deliveryTime;
    }

    /**
     * @return string
     */
    public function getInventoryTrackingMethod(): string
    {
        return $this->inventoryTrackingMethod;
    }

    /**
     * @param string $inventoryTrackingMethod
     */
    public function setInventoryTrackingMethod($inventoryTrackingMethod): void
    {
        $this->inventoryTrackingMethod = $inventoryTrackingMethod;
    }

    /**
     * @return string
     */
    public function getConditionId(): string
    {
        return $this->conditionId;
    }

    /**
     * @param string $conditionId
     */
    public function setConditionId(string $conditionId): void
    {
        $this->conditionId = $conditionId;
    }

    /**
     * @return string
     */
    public function getConditionDisplayName(): string
    {
        return $this->conditionDisplayName;
    }

    /**
     * @param string $conditionDisplayName
     */
    public function setConditionDisplayName($conditionDisplayName): void
    {
        $this->conditionDisplayName = $conditionDisplayName;
    }

    /**
     * @return string
     */
    public function getConditionDescription(): string
    {
        return $this->conditionDescription;
    }

    /**
     * @param string $conditionDescription
     */
    public function setConditionDescription($conditionDescription): void
    {
        $this->conditionDescription = $conditionDescription;
    }

    /**
     * @return string
     */
    public function getListingDuration(): string
    {
        return $this->listingDuration;
    }

    /**
     * @param string $listingDuration
     */
    public function setListingDuration($listingDuration): void
    {
        $this->listingDuration = $listingDuration;
    }

    /**
     * @return bool
     */
    public function isEbayPlus(): bool
    {
        return $this->ebayPlus;
    }

    /**
     * @param bool $ebayPlus
     */
    public function setEbayPlus($ebayPlus): void
    {
        $this->ebayPlus = $ebayPlus;
    }

    /**
     * @return bool
     */
    public function isPriceSuggestion(): bool
    {
        return $this->priceSuggestion;
    }

    /**
     * @param bool $priceSuggestion
     */
    public function setPriceSuggestion($priceSuggestion): void
    {
        $this->priceSuggestion = $priceSuggestion;
    }

    /**
     * @return bool
     */
    public function isPrivateListing(): bool
    {
        return $this->privateListing;
    }

    /**
     * @param bool $privateListing
     */
    public function setPrivateListing(bool $privateListing): void
    {
        $this->privateListing = $privateListing;
    }

    /**
     * @return array
     */
    protected function getVariationsAsArray(): array
    {
        $variations = [];
        foreach ($this->variations as $variation) {
            $variations[] = $variation->toArray();
        }

        return $variations;
    }

    /**
     * @return int
     */
    public function getTemplateId(): int
    {
        return $this->templateId;
    }

    /**
     * @param int $templateId
     */
    public function setTemplateId($templateId): void
    {
        $this->templateId = $templateId;
    }

    /**
     * @return array
     */
    public function validate(): array
    {
        $errors = [];

        if ($this->shopId <= 0) {
            $errors['shop_id'][] = 'The "shopId" property must be greater than zero.';
        }

        return $errors;
    }
}
