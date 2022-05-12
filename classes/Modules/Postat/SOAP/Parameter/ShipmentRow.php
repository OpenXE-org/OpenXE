<?php

namespace Xentral\Modules\Postat\SOAP\Parameter;

use Xentral\Modules\Postat\SOAP\ParameterInterface;
use Xentral\Modules\Postat\SOAP\PostAtException;

class ShipmentRow implements ParameterInterface
{
    /** var array $shipmentRow */
    private $shipmentRow;

    public function __construct(array $shipmentRow)
    {
        if (empty($shipmentRow['row'])) {
            throw new PostAtException('The given shipment data is invalid');
        }

        $this->shipmentRow = $shipmentRow;
    }

    public function getData(): array
    {
        return $this->shipmentRow;
    }
}
