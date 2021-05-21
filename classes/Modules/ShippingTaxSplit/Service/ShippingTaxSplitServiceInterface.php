<?php

namespace Xentral\Modules\ShippingTaxSplit\Service;

interface ShippingTaxSplitServiceInterface
{
    /**
     * Delete Shipping Position by OrderId
     *
     * @param int $orderId
     *
     * @return bool
     */
    public function deleteShippingPositionsByOrderId($orderId);


    /**
     * @param int   $orderId
     * @param int   $articleId
     * @param float $amount
     *
     * @return bool
     */
    public function addOrReplaceShippingPositionToOrder($orderId, $articleId, $amount);
}
