<?php

declare(strict_types=1);

namespace Xentral\Modules\CopperSurcharge\Data;

use Xentral\Modules\CopperSurcharge\Exception\ValidationFailedException;

final class CopperSurchargeData
{
    /**
     *  a surcharge position gets added to every copper article
     */
    public const POSITION_TYPE_ALWAYS = 0;
    /**
     * only one surcharge position gets added for all copper articles
     */
    public const POSITION_TYPE_ONETIME = 1;

    /**
     * a surcharge position gets added to all position groups
     */
    public const POSITION_TYPE_GROUP = 2;

    /**
     * calculation base is the date from the offer of the order
     */
    public const DOCUMENT_CONVERSION_FROM_OFFER = 0;
    //public const DOCUMENT_CONVERSION_CREATE_NEW = 1;

    /**
     * calculation base is the date from the order of the invoice
     */
    public const INVOICE_CREATE_POS_BY_ORDER_DATE = 0;

    /**
     * calculation base is the delivery date of the invoice
     */
    public const INVOICE_CREATE_POS_BY_DELIVERY_DATE = 1;

    /**
     * calculation base is the date from the invoice
     */
    public const INVOICE_CREATE_POS_BY_INVOICE_DATE = 2;

    /**
     * calculation base is the date from the offer of the invoice
     */
    public const INVOICE_CREATE_POS_BY_OFFER_DATE = 3;

    /**
     * data gets managed in the app raw materials
     */
    public const SURCHARGE_MAINTENANCE_TYPE_APP = 0;

    /**
     * data gets managed in the additional fields of the copper article
     */
    public const SURCHARGE_MAINTENANCE_TYPE_ARTICLE = 1;

    /** @var int $copperSurchargeArticleId */
    private $copperSurchargeArticleId;

    /** @var int $surchargePositionType */
    private $surchargePositionType;

    /** @var int $surchargeDocumentConversion */
    private $surchargeDocumentConversion;

    /** @var int $surchargeInvoice */
    private $surchargeInvoice;

    /** @var float $surchargeDeliveryCosts */
    private $surchargeDeliveryCosts;

    /** @var string $surchargeCopperBase */
    private $surchargeCopperBase;

    /** @var float $surchargeCopperBaseStandard */
    private $surchargeCopperBaseStandard;

    /** @var int $copperNumberOption */
    private $copperNumberOption;

    /** @var int $surchargeMaintenaceType */
    private $surchargeMaintenanceType;

    /**
     * @param int    $articleId
     * @param int    $surchargePositionType
     * @param int    $surchargeDocumentConversion
     * @param int    $surchargeInvoice
     * @param float  $surchargeDeliveryCosts
     * @param string $surchargeCopperBase
     * @param float  $surchargeCopperBaseStandard
     * @param int    $surchargeMaintenanceType
     * @param string $copperNumberOption
     *
     */
    public function __construct(
        int $articleId,
        int $surchargePositionType,
        int $surchargeDocumentConversion,
        int $surchargeInvoice,
        float $surchargeDeliveryCosts,
        string $surchargeCopperBase,
        float $surchargeCopperBaseStandard,
        int $surchargeMaintenanceType,
        string $copperNumberOption
    ) {
        $this->validate(
            $articleId,
            $surchargeDeliveryCosts,
            $surchargeCopperBaseStandard,
            $surchargeMaintenanceType,
            $copperNumberOption
        );

        $this->copperSurchargeArticleId = $articleId;
        $this->surchargePositionType = $surchargePositionType;
        $this->surchargeDocumentConversion = $surchargeDocumentConversion;
        $this->surchargeInvoice = $surchargeInvoice;
        $this->surchargeDeliveryCosts = $surchargeDeliveryCosts;
        $this->surchargeCopperBase = $surchargeCopperBase;
        $this->surchargeCopperBaseStandard = $surchargeCopperBaseStandard;
        $this->copperNumberOption = $copperNumberOption;
        $this->surchargeMaintenanceType = $surchargeMaintenanceType;
    }

    /**
     * @param int    $articleId
     * @param float  $surchargeDeliveryCosts
     * @param float  $surchargeCopperBaseStandard
     *
     * @param int    $surchargeMaintenanceType
     * @param string $copperNumberOption
     */
    private function validate(
        int $articleId,
        float $surchargeDeliveryCosts,
        float $surchargeCopperBaseStandard,
        int $surchargeMaintenanceType,
        string $copperNumberOption
    ) {
        if (empty($articleId)) {
            throw new ValidationFailedException('copper surcharge article is missing');
        }

        if (empty($surchargeDeliveryCosts)) {
            throw new ValidationFailedException('surcharge delivery costs are missing');
        }

        if (empty($surchargeCopperBaseStandard)) {
            throw new ValidationFailedException('surcharge copper base standard is missing');
        }

        if ($surchargeMaintenanceType === self::SURCHARGE_MAINTENANCE_TYPE_ARTICLE && empty($copperNumberOption)) {
            throw new ValidationFailedException('copper number option is missing');
        }
    }

    /**
     * @return int article id of the surcharge article
     */
    public function getCopperSurchargeArticleId(): int
    {
        return $this->copperSurchargeArticleId;
    }

    /**
     * 0 = add always a surcharge position to every copper article,
     * 1 = add only one surcharge position for all copper articles,
     * 2 = add a surcharge position in every position group
     *
     * @return int
     */
    public function getSurchargePositionType(): int
    {
        return $this->surchargePositionType;
    }

    /**
     * decides which date is the base if a surcharge positions is added to an order
     * 0 = date of offer (fallback current date)
     * 1 = date of the order
     *
     * @return int
     */
    public function getSurchargeDocumentConversion(): int
    {
        return $this->surchargeDocumentConversion;
    }

    /**
     * decides which date is the base if a surcharge positions is added to an invoice
     * 0 = from the order (fallback current date)
     * 1 = delivery date (fallback current date)
     * 2 = from the invoice
     * 3 = from the offer (fallback current date)
     *
     * @return int
     */
    public function getSurchargeInvoice(): int
    {
        return $this->surchargeInvoice;
    }

    /**
     * part of the calculation, default 1, in percent
     *
     * @return float
     */
    public function getSurchargeDeliveryCosts(): float
    {
        return $this->surchargeDeliveryCosts;
    }

    /**
     * can be used instead of surchargeCopperBaseStandard for specific articles, EUR/100kg
     *
     * @return string
     */
    public function getSurchargeCopperBase(): string
    {
        return $this->surchargeCopperBase;
    }

    /**
     * part of the the calculation, default 150, EUR/100kg
     *
     * @return float
     */
    public function getSurchargeCopperBaseStandard(): float
    {
        return $this->surchargeCopperBaseStandard;
    }

    /**
     * describes which additional field in the article contains the weight,
     * only useful with surchargeMaintenanceType = 1
     *
     * @return string
     */
    public function getCopperNumberOption(): string
    {
        return $this->copperNumberOption;
    }

    /**
     * choice how copper articles should hold their necessary infos
     * 0 = with the app 'raw materials'
     * 1 = with additional article fields
     *
     * @return int
     */
    public function getSurchargeMaintenanceType(): int
    {
        return $this->surchargeMaintenanceType;
    }
}
