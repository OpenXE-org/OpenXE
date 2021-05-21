<?php

namespace Xentral\Modules\AmazonVendorDF\Data;

use DateTime;

class ShipmentDetails
{
    /** @var bool */
    private $isPriorityShipment;
    /** @var bool */
    private $isPslipRequired;
    /** @var string */
    private $shipMethod;
    /** @var DateTime */
    private $promisedDeliveryDate;
    /** @var DateTime */
    private $requiredShipDate;
    /** @var string */
    private $messageToCustomer;

    public function __construct(
        bool $isPriorityShipment,
        bool $isPslipRequired,
        string $shipMethod,
        DateTime $requiredShipDate,
        DateTime $promisedDeliveryDate,
        string $messageToCustomer
    ) {
        $this->isPriorityShipment = $isPriorityShipment;
        $this->isPslipRequired = $isPslipRequired;
        $this->shipMethod = $shipMethod;
        $this->requiredShipDate = $requiredShipDate;
        $this->promisedDeliveryDate = $promisedDeliveryDate;
        $this->messageToCustomer = $messageToCustomer;
    }

    public function isPriorityShipment(): bool
    {
        return $this->isPriorityShipment;
    }

    public function isPslipRequired(): bool
    {
        return $this->isPslipRequired;
    }


    public function getShipMethod(): string
    {
        return $this->shipMethod;
    }

    public function getRequiredShipDate(): DateTime
    {
        return $this->requiredShipDate;
    }

    public function getPromisedDeliveryDate(): DateTime
    {
        return $this->promisedDeliveryDate;
    }

    public function getMessageToCustomer(): string
    {
        return $this->messageToCustomer;
    }
}
