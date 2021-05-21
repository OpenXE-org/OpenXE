<?php

namespace Xentral\Modules\ScanArticle\Wrapper;

use Xentral\Components\Database\Database;
use erpAPI;
use Xentral\Modules\ScanArticle\Exception\InvalidArgumentException;

class PriceWrapper
{
    /** @var erpAPI $erp */
    private $erp;

    /** @var Database $db */
    private $db;

    /**
     * @param erpAPI   $erp
     * @param Database $database
     */
    public function __construct(erpAPI $erp, Database $database)
    {
        $this->erp = $erp;
        $this->db = $database;
    }

    /**
     * @param int    $articleId
     * @param float  $amount
     * @param int    $adressId
     * @param string $currency
     *
     * @return int|mixed|null
     */
    private function getSellingPrice(
        $articleId,
        $amount,
        $adressId = 0,
        $currency = ''
    ) {
        $returnCurrencyRef = '';

        return $this->erp->GetVerkaufspreis(
            $articleId,
            $amount,
            $adressId,
            $currency,
            $returnCurrencyRef,
            false,
            false
        );
    }

    /**
     * @param int    $articleId
     * @param float  $amount
     * @param int    $orderId
     * @param string $currency
     *
     * @return int|mixed|null
     */
    public function getOrderSellingPrice(
        $articleId,
        $amount,
        $orderId,
        $currency = ''
    ) {
        $adressId = $this->db->fetchValue(
            "SELECT `adresse` FROM `auftrag` WHERE id = :orderId",
            ['orderId' => $orderId]
        );

        return $this->getSellingPrice(
            $articleId,
            $amount,
            $adressId,
            $currency
        );
    }

    /**
     * @param int   $articleId
     * @param float $amount
     * @param int   $purchaseOrderId
     *
     * @return int | null
     */
    private function getPurchasePriceId($articleId, $amount, $purchaseOrderId)
    {
        $supplierId = $this->db->fetchValue(
            "SELECT `adresse` FROM `bestellung` WHERE id = :purchaseOrderId",
            ['purchaseOrderId' => $purchaseOrderId]
        );

        if (!empty($supplierId)) {
            $purchasePriceId = $this->erp->Einkaufspreis($articleId, $amount, $supplierId);
            if (!empty($purchasePriceId)) {
                return $purchasePriceId;
            }
        }

        return null;
    }

    /**
     * @param int   $articleId
     * @param float $amount
     * @param int   $purchaseOrderId
     *
     * @return float | null
     */
    public function getPurchaseOrderPurchasePrice($articleId, $amount, $purchaseOrderId)
    {
        $purchasePriceId = $this->getPurchasePriceId($articleId, $amount, $purchaseOrderId);

        if (!empty($purchasePriceId)) {
            return (float)$this->db->fetchValue(
                "SELECT `preis` FROM `einkaufspreise` WHERE id = :purchasePriceId",
                ['purchasePriceId' => $purchasePriceId]
            );
        } else {
            throw new InvalidArgumentException('Purchase-Order-Id can not be empty');
        }
    }
}
