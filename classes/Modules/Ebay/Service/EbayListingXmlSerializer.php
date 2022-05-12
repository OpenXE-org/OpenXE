<?php

namespace Xentral\Modules\Ebay\Service;

use Xentral\Modules\Ebay\Data\StagingListingData;

final class EbayListingXmlSerializer
{
    /**
     * @param StagingListingData $stagingListing
     * @param array              $stocksForArticles
     *
     * @return string
     */
    public function createStockSyncXmlString(StagingListingData $stagingListing, $stocksForArticles): string
    {
        $syncBody = '<ReviseFixedPriceItemRequest version="1.0" xmlns="urn:ebay:apis:eBLBaseComponents">';
        $syncBody .= '<Version>1137</Version>';
        $syncBody .= '<MessageID>' . $stagingListing->getSku() . '</MessageID>';
        $syncBody .= '<Item>
        <ItemID>' . $stagingListing->getItemId() . '</ItemID>';

        if (!empty($stagingListing->getVariations())) {
            $syncBody .= '<Variations>';
            foreach ($stagingListing->getVariations() as $variation) {
                $quantity = 0;

                if (!empty($variation->getArticleId()) && array_key_exists(
                        $variation->getArticleId(),
                        $stocksForArticles
                    )) {
                    $quantity = $stocksForArticles[$variation->getArticleId()];
                    $syncBody .= '
                <Variation>';
                    $syncBody .= '<Quantity>' . $quantity . '</Quantity><SKU>' . $variation->getSku() . '</SKU>';
                    $syncBody .= '<VariationSpecifics>';
                    foreach ($variation->listSpecifics() as $specificName => $specificValue) {
                        $syncBody .= '
                <NameValueList>';
                        $syncBody .= '<Name>' . $specificName . '</Name>';
                        $syncBody .= '<Value>' . $specificValue . '</Value>';
                        $syncBody .= '</NameValueList>';
                    }
                    $syncBody .= '</VariationSpecifics>';
                    $syncBody .= '</Variation>';
                }
            }
            $syncBody .= '</Variations>';
        } else {
            $quantity = 0;
            if (!empty($stagingListing->getArticleId()) && array_key_exists(
                    $stagingListing->getArticleId(),
                    $stocksForArticles
                )) {
                $quantity = $stocksForArticles[$stagingListing->getArticleId()];
            }

            $syncBody .= '<Quantity>' . $quantity . '</Quantity>';
        }

        $syncBody .= '</Item></ReviseFixedPriceItemRequest>';


        return $syncBody;
    }
}
