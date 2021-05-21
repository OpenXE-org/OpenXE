<?php

namespace Xentral\Modules\CopperSurcharge\Wrapper;

interface DocumentPositionWrapperInterface
{
    /**
     * @param string $doctype
     * @param int    $docId
     * @param int    $articleId
     * @param array  $articleData
     * @param float  $amount
     * @param float  $price
     * @param string $currency
     * @param string $description
     *
     * @return int
     */
    public function addPositionManuallyWithPrice(
        string $doctype,
        int $docId,
        int $articleId,
        array $articleData,
        float $amount,
        float $price,
        string $currency = 'EUR',
        string $description = ''
    ): int;
}
