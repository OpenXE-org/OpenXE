<?php

declare(strict_types=1);

namespace Xentral\Modules\Datanorm\Service;

use Xentral\Modules\Datanorm\Data\DatanormATypeData;
use Xentral\Modules\Datanorm\Data\DatanormBTypeData;

final class DatanormConverter
{
    /**
     * @param DatanormATypeData $aType
     *
     * @return array
     */
    public function transformATypeToArticleArray(DatanormATypeData $aType): array
    {
        $article = [];
        $article['nummer'] = $aType->getArticleNumber();
        $article['name_de'] = $aType->getShortDescription1();
        $article['anabregs_text'] = $aType->getShortDescription2();
        $article['ean'] = $aType->getEan();
        $article['herstellernummer'] = $aType->getProducerNumber();
        $article['einheit'] = $aType->getPackingUnit();

        if ($aType->getArticleType() === '1') {
            $article['lagerartikel'] = 1;
        }

        if ($aType->getWorkflowState() === 'L') {
            $article['intern_gesperrt'] = 1;
            $article['intern_gesperrtgrund'] = 'DATANORM';
        }

        $article['umsatzsteuer'] = 'normal';
        if ($aType->getMwstType() === 3) {
            $article['umsatzsteuer'] = 'ermaessigt';
        }

        return $article;
    }

    /**
     * @param DatanormBTypeData $bType
     *
     * @return array
     */
    public function transformBTypeToArticleArray(DatanormBTypeData $bType): array
    {
        $article = [];

        if (!empty($bType->getEan())) {
            $article['ean'] = $bType->getEan();
        }

        if ($bType->getProcessingFlag() === 'L') {
            $article['intern_gesperrt'] = 1;
            $article['intern_gesperrtgrund'] = 'DATANORM';
        }

        if (!empty($bType->getAltArticleNumber())) {
            $article['herstellernummer'] = $bType->getAltArticleNumber();
        }

        if ($bType->getCopperWeightIndicator() != '0' && $bType->getCopperWeightIndicator() != '') {
            $article['internerkommentar'] =
                'Kupfer-Gewichtsmerker: ' . $bType->getCopperWeightIndicator() . PHP_EOL .
                'Kupfer-Kennzahl: ' . $bType->getCopperWeightIndicator() . PHP_EOL .
                'Kupfer-Gewicht: ' . $bType->getCopperWeightIndicator();
        }

        if (!empty($article)) {
            $article['nummer'] = $bType->getArticleNumber();
        }

        return $article;
    }

    /**
     * @param int    $articleId
     * @param string $priceMark
     * @param string $currency
     * @param int    $amount
     * @param float  $price
     * @param int    $supplierId
     * @param string $discountFlag1
     * @param float $discount1
     * @param string $discountFlag2
     * @param float $discount2
     * @param string $discountFlag3
     * @param float $discount3
     *
     * @return array
     */
    public function transformToPriceArray(
        int $articleId,
        string $priceMark,
        string $currency,
        int $amount,
        float $price,
        int $supplierId,
        string $discountFlag1,
        float $discount1,
        string $discountFlag2,
        float $discount2,
        string $discountFlag3,
        float $discount3
    ): array {
        $sellingPrice = [];
        $purchasePrices = [];

        if ($priceMark === '2') {
            $purchasePrices[] = [
                'article_id'    => $articleId,
                'address_id'    => $supplierId,
                'currency_code' => $currency,
                'quantity_from' => $amount,
                'price'         => $price,
            ];
        } else {
            $sellingPrice = [
                'currency_code' => $currency,
                'quantity_from' => $amount,
                'article_id'    => $articleId,
                'price'         => $price,
            ];

            if ($priceMark === '1') {
                if (!empty($discountFlag1)) {
                    $discountPrice1 = $this->calculateDiscountPrice($price, $discountFlag1, $discount1);
                    if (!empty($discountPrice1)) {
                        $purchasePrices[] = [
                            'article_id'    => $articleId,
                            'address_id'    => $supplierId,
                            'currency_code' => $currency,
                            'quantity_from' => $amount,
                            'price'         => $discountPrice1,
                        ];
                    }
                }

                if (!empty($discountFlag2)) {
                    $discountPrice2 = $this->calculateDiscountPrice($price, $discountFlag2, $discount2);
                    if (!empty($discountPrice2)) {
                        $purchasePrices[] = [
                            'article_id'    => $articleId,
                            'address_id'    => $supplierId,
                            'currency_code' => $currency,
                            'quantity_from' => $amount,
                            'price'         => $discountPrice2,
                        ];
                    }
                }

                if (!empty($discountFlag3)) {
                    $discountPrice3 = $this->calculateDiscountPrice($price, $discountFlag3, $discount3);
                    if (!empty($discountPrice3)) {
                        $purchasePrices[] = [
                            'article_id'    => $articleId,
                            'address_id'    => $supplierId,
                            'currency_code' => $currency,
                            'quantity_from' => $amount,
                            'price'         => $discountPrice3,
                        ];
                    }
                }
            }
        }

        return [
            'sellingPrice'   => $sellingPrice,
            'purchasePrices' => $purchasePrices,
        ];
    }

    /**
     * @param float  $price
     * @param string $discountFlag
     * @param float  $discount
     *
     * @return float
     */
    private function calculateDiscountPrice(float $price, string $discountFlag, float $discount)
    {
        $discountPrice = 0.0;

        // Discount
        if ($discountFlag === '1') {
            $discountPrice = $price - ($price * ($discount / 100));
        } // Factor
        elseif ($discountFlag === '2') {
            $discountPrice = $price * $discount;
        } // Surcharge
        elseif ($discountFlag === '3') {
            $discountPrice = $price + ($price * ($discount / 100));
        }

        return $discountPrice;
    }

}
