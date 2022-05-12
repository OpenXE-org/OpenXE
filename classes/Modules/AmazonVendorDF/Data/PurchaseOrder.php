<?php

namespace Xentral\Modules\AmazonVendorDF\Data;

use DateTime;
use InvalidArgumentException;

class PurchaseOrder
{
    /** @var string */
    private $purchaseOrderNumber;
    /** @var DateTime|false */
    private $purchaseOrderDate;
    /** @var array|PurchaseOrderItem[] */
    private $items;
    /** @var SellingParty */
    private $sellingParty;
    /** @var Address */
    private $shipToParty;
    /** @var string */
    private $warehouseId;
    /** @var ShipmentDetails */
    private $shipmentDetails;
    /** @var array */
    private $rawData;

    public function __construct(
        string $purchaseOrderNumber,
        $purchaseOrderDate,
        array $items,
        SellingParty $sellingParty,
        string $warehouseId,
        Address $shipToParty,
        ShipmentDetails $shipmentDetails,
        array $rawData = []
    ) {
        $this->purchaseOrderNumber = $purchaseOrderNumber;
        $this->purchaseOrderDate = $purchaseOrderDate;
        $this->items = $items;
        $this->sellingParty = $sellingParty;
        $this->warehouseId = $warehouseId;
        $this->shipToParty = $shipToParty;
        $this->shipmentDetails = $shipmentDetails;
        $this->rawData = $rawData;
    }

    /** @return string */
    public function getPurchaseOrderNumber(): string
    {
        return $this->purchaseOrderNumber;
    }

    /** @return DateTime|false */
    public function getPurchaseOrderDate()
    {
        return $this->purchaseOrderDate;
    }

    /** @return array|PurchaseOrderItem[] */
    public function getItems()
    {
        return $this->items;
    }

    /** @return SellingParty */
    public function getSellingParty(): SellingParty
    {
        return $this->sellingParty;
    }

    public function getShipToParty(): Address
    {
        return $this->shipToParty;
    }

    public function getShipmentDetails(): ShipmentDetails
    {
        return $this->shipmentDetails;
    }

    public function getRawData(): array
    {
        return $this->rawData;
    }

    public static function fromPurchaseOrderResponse(array $data)
    {
        if (isset($data['payload'])) {
            $data = $data['payload'];
        }

        $items = array_map(
            function (array $item) {
                return PurchaseOrderItem::fromPurchaseOrderResponse($item);
            },
            $data['orderDetails']['items']
        );

        $sellingParty = new SellingParty($data['orderDetails']['sellingParty']['partyId']);
        $shipToParty = (new Address())
            ->setName($data['orderDetails']['shipToParty']['name'])
            ->setAddressLines(
                [
                    $data['orderDetails']['shipToParty']['addressLine1'],
                    $data['orderDetails']['shipToParty']['addressLine2'],
                    $data['orderDetails']['shipToParty']['addressLine3'],
                ]
            )
            ->setCity($data['orderDetails']['shipToParty']['city'])
            ->setStateOrRegion($data['orderDetails']['shipToParty']['stateOrRegion'])
            ->setPostalCode($data['orderDetails']['shipToParty']['postalCode'])
            ->setCountryCode($data['orderDetails']['shipToParty']['countryCode']);

        $shipmentDetails = new ShipmentDetails(
            $data['orderDetails']['shipmentDetails']['isPriorityShipment'],
            $data['orderDetails']['shipmentDetails']['isPslipRequired'],
            $data['orderDetails']['shipmentDetails']['shipMethod'],
            self::parseDate($data['orderDetails']['shipmentDetails']['shipmentDates']['requiredShipDate']),
            self::parseDate($data['orderDetails']['shipmentDetails']['shipmentDates']['promisedDeliveryDate']),
            isset($data['orderDetails']['shipmentDetails']['messageToCustomer'])
                ? $data['orderDetails']['shipmentDetails']['messageToCustomer']
                : ''
        );

        // @TODO billToParty needs to be set
        return new static(
            $data['purchaseOrderNumber'],
            self::parseDate($data['orderDetails']['orderDate']),
            $items,
            $sellingParty,
            $data['orderDetails']['shipFromParty']['partyId'],
            $shipToParty,
            $shipmentDetails,
            $data
        );
    }

    protected static function parseDate(string $iso8601DateString): DateTime
    {
        $date = DateTime::createFromFormat(
            DateTime::ISO8601,
            $iso8601DateString
        );

        if(!$date instanceof DateTime){
            throw new InvalidArgumentException("Date is not in ISO8601 format: {$iso8601DateString}");
        }

        return $date;
    }
}
