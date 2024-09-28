<?php

// SPDX-FileCopyrightText: 2024 Andreas Palm
//
// SPDX-License-Identifier: AGPL-3.0-only

namespace Xentral\Modules\Onlineshop\Data;

class OrderStatusUpdateRequest
{
    /**
     * @var int ID of the (primary/imported) order (ERP domain)
     */
    public int $orderId;

    /**
     * @var string ID of the order (Shop domain)
     */
    public string $shopOrderId;

    /**
     * @var OrderStatus current order status
     */
    public OrderStatus $orderStatus;

    /**
     * @var Shipment[] list of shipments for this order
     */
    public array $shipments = array();

    public function getTrackingNumberList() : array {
        $list = [];
        foreach ($this->shipments as $shipment) {
            if (!empty($shipment->trackingNumber))
                $list[] = $shipment->trackingNumber;
        }
        return $list;
    }

    public function getTrackingUrlList() : array {
        $list = [];
        foreach ($this->shipments as $shipment) {
            if (!empty($shipment->trackingUrl))
                $list[] = $shipment->trackingUrl;
        }
        return $list;
    }
}
