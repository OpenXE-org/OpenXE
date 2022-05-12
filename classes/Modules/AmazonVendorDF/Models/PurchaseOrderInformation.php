<?php

declare(strict_types=1);

namespace Xentral\Modules\AmazonVendorDF\Models;

use DateTime;
use Xentral\Modules\AmazonVendorDF\Data\PurchaseOrder;
use Xentral\Modules\AmazonVendorDF\Data\ShippingLabel;

class PurchaseOrderInformation
{
    /** Step 1: Purchase order resulted in an entry in the database which was not processed yet */
    public const STATUS_UNPROCESSED = null;
    /** Step 2: Purchase order resulted in an order but was not acknowledged yet */
    public const STATUS_PROCESSING = 'processing';

    /** Step 3: Acknowledgement was sent, but it was not yet cleared by Amazon */
    public const STATUS_ACKNOWLEDGEMENT_SENT = 'acknowledgement_sent';
    /** Step 3b: Purchase could not be processed automatically and requires user input  */
    public const STATUS_WAITING_FOR_USER_INPUT = 'waiting_for_user_input';
    /** Step 3c: There was an error on the remote end preventing the acknowledgement from being processed*/
    public const STATUS_ACKNOWLEDGEMENT_FAILED = 'acknowledgement_failed';
    /** Step 4: Acknowledgement was accepted by Amazon */
    public const STATUS_ACKNOWLEDGEMENT_ACCEPTED = 'acknowledgement_accepted';
    /** Step 4b: Acknowledgement was rejected by amazon */
    public const STATUS_ACKNOWLEDGEMENT_REJECTED = 'acknowledgement_rejected';

    /** Step 5: Shipping label was requested, but not yet provided by Amazon */
    public const STATUS_SHIPPING_LABEL_REQUESTED = 'shipping_label_requested';
    /** Step 6: Shipping label request was successful and can be downloaded */
    public const STATUS_SHIPPING_LABEL_ACCEPTED = 'shipping_label_accepted';
    /** Step 6b: Shipping label request was rejected by amazon */
    public const STATUS_SHIPPING_LABEL_REJECTED = 'shipping_label_rejected';

    /** Step 7:  */
    public const STATUS_SHIPMENT_CONFIRMATION_SENT = 'shipment_confirmation_sent';

    /** Rejected by user input */
    public const STATUS_REJECTED_BY_USER_INPUT = 'rejected_by_user_input';

    /** @var string */
    private $externalId;
    /** @var string */
    private $raw;
    /** @var null|int */
    private $orderId;
    /** @var bool */
    private $acknowledged = false;
    /** @var null|string */
    private $acknowledgementTransactionId;
    /** @var bool */
    private $shippingLabelRequested = false;
    /** @var null|string */
    private $shippingLabelRequestTransactionId;
    /** @var null|string */
    private $shippingLabelData;
    /** @var DateTime|null */
    private $createdAt;
    /** @var Datetime|null */
    private $updatedAt;
    /** @var string */
    private $status;
    /** @var string */
    private $shipmentConfirmationTransactionId;

    public function __construct(string $externalId)
    {
        $this->externalId = $externalId;
    }

    /**
     * @return null|string
     */
    public function getShipmentConfirmationTransactionId(): ?string
    {
        return $this->shipmentConfirmationTransactionId;
    }

    /**
     * @param string $shipmentConfirmationTransactionId
     */
    public function setShipmentConfirmationTransactionId(string $shipmentConfirmationTransactionId): void
    {
        $this->shipmentConfirmationTransactionId = $shipmentConfirmationTransactionId;
    }

    /**
     * @return bool
     */
    public function wasShipmentConfirmationSent(): bool
    {
        return $this->shipmentConfirmationTransactionId !== null;
    }

    /**
     * @return string
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function canFetchShippingLabels(): bool
    {
        return $this->status === self::STATUS_SHIPPING_LABEL_ACCEPTED
            || (empty($this->shippingLabelData) && $this->status === self::STATUS_SHIPMENT_CONFIRMATION_SENT);
    }

    public function hasShippingLabel(): bool
    {
        return $this->shippingLabelData !== null;
    }

    public function getPurchaseOrderNumber(): string
    {
        return $this->externalId;
    }

    public function getRawJson(): ?string
    {
        return $this->raw;
    }

    public function getOrderId(): ?int
    {
        return $this->orderId;
    }

    /**
     * @param int|null $orderId
     */
    public function setOrderId(?int $orderId): void
    {
        $this->orderId = $orderId;
    }

    public function isAcknowledged(): bool
    {
        return $this->acknowledged;
    }

    /**
     * @param bool $acknowledged
     */
    public function setAcknowledged(bool $acknowledged): void
    {
        $this->acknowledged = $acknowledged;
    }

    public function getAcknowledgementTransactionId(): ?string
    {
        return $this->acknowledgementTransactionId;
    }

    /**
     * @param string|null $acknowledgementTransactionId
     */
    public function setAcknowledgementTransactionId(?string $acknowledgementTransactionId): void
    {
        $this->acknowledgementTransactionId = $acknowledgementTransactionId;
    }

    public function isShippingLabelRequested(): bool
    {
        return $this->shippingLabelRequested;
    }

    /**
     * @param bool $shippingLabelRequested
     */
    public function setShippingLabelRequested(bool $shippingLabelRequested): void
    {
        $this->shippingLabelRequested = $shippingLabelRequested;
    }

    /**
     * @return string
     */
    public function getExternalId(): string
    {
        return $this->externalId;
    }

    /**
     * @return string|null
     */
    public function getShippingLabelRequestTransactionId(): ?string
    {
        return $this->shippingLabelRequestTransactionId;
    }

    /**
     * @param string|null $shippingLabelRequestTransactionId
     */
    public function setShippingLabelRequestTransactionId(?string $shippingLabelRequestTransactionId): void
    {
        $this->shippingLabelRequestTransactionId = $shippingLabelRequestTransactionId;
    }

    /**
     * @return ShippingLabel[]
     */
    public function getShippingLabels(): array
    {
        $decodedShippingLabels = json_decode($this->shippingLabelData, true);

        return array_map(
            function (array $data) {
                $shippingLabel = new ShippingLabel(
                    $data['purchase_order_number'],
                    $data['encodedLabelData']
                );
                $shippingLabel->setTrackingNumber($data['tracking_number']);

                return $shippingLabel;
            },
            $decodedShippingLabels
        );
    }

    public function setShippingLabels(array $shippingLabels): void
    {
        $this->shippingLabelData = json_encode($shippingLabels);
    }

    public function getShippingLabelData(): ?string
    {
        return $this->shippingLabelData;
    }

    public function setShippingLabelData(string $shippingLabelData): void
    {
        $this->shippingLabelData = $shippingLabelData;
    }

    public function setRaw(string $raw): void
    {
        $this->raw = $raw;
    }

    public function getPurchaseOrder(): PurchaseOrder
    {
        return PurchaseOrder::fromPurchaseOrderResponse(json_decode($this->raw, true));
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     */
    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param Datetime $updatedAt
     */
    public function setUpdatedAt(Datetime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
