<?php

declare(strict_types=1);

namespace Xentral\Modules\Ebay\Wrapper;

interface EbayStockCalculationWrapperInterface
{
    /**
     * @param int $articleId
     * @param int $shopId
     *
     * @return float
     */
    public function calculateStock($articleId, $shopId): float;
}
