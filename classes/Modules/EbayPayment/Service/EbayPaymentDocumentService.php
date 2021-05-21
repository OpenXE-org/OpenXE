<?php

declare(strict_types=1);

namespace Xentral\Modules\EbayPayment\Service;

use Xentral\Components\Database\Database;

final class EbayPaymentDocumentService implements EbayPaymentDocumentServiceInterface
{
    /** @var Database $db */
    private $db;

    /**
     * EbayPaymentDocumentService constructor.
     *
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * @param string $externalOrderId
     *
     * @return array|null
     */
    public function getOrderShopIdFromOrderId(string $externalOrderId): ?array
    {
        $orderShopId = $this->db->fetchRow(
            "SELECT o.internet, o.shop, o.waehrung, o.gesamtsumme
            FROM `auftrag` AS `o` 
            INNER JOIN `shopexport` AS `s` ON o.shop = s.id AND s.aktiv = 1 AND s.modulename = 'shopimporter_ebay'
            INNER JOIN `ebay_rest_token` AS `ert` ON s.id = ert.shopexport_id AND ert.type = 'User Access Token'
            WHERE o.internet = :external_order_id
            LIMIT 1",
            ['external_order_id' => $externalOrderId]
        );
        if (empty($orderShopId)) {
            return null;
        }

        return $orderShopId;
    }

    /**
     * @param bool   $onlyWithRestAccount
     * @param string $textToShowOnNotActivatedRestApi
     *
     * @return array
     */
    public function getEbayShops(bool $onlyWithRestAccount, string $textToShowOnNotActivatedRestApi): array
    {
        if ($onlyWithRestAccount) {
            $shopsDb = $this->db->fetchPairs(
                "SELECT s.id, s.bezeichnung
                FROM `shopexport` AS `s`
                INNER JOIN `ebay_rest_token` AS `ert` ON s.id = ert.shopexport_id AND ert.type = 'User Access Token'
                WHERE s.aktiv = 1 AND s.modulename = 'shopimporter_ebay'
                GROUP BY s.id"
            );
        } else {
            $shopsDb = $this->db->fetchPairs(
                "SELECT s.id, IF(ert.id IS NULL, CONCAT(s.bezeichnung, ' {$textToShowOnNotActivatedRestApi}') , s.bezeichnung)
                FROM `shopexport` AS `s`
                LEFT JOIN `ebay_rest_token` AS `ert` ON s.id = ert.shopexport_id AND ert.type = 'User Access Token'
                WHERE s.aktiv = 1 AND s.modulename = 'shopimporter_ebay'
                GROUP BY s.id"
            );
        }

        $shops = [];
        foreach ($shopsDb as $shopId => $shopName) {
            $shops[(int)$shopId] = $shopName;
        }

        return $shops;
    }
}
