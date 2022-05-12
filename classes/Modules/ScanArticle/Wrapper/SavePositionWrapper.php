<?php

namespace Xentral\Modules\ScanArticle\Wrapper;

use erpAPI;
use Xentral\Components\Database\Database;

class SavePositionWrapper
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
     * @param int   $articleId
     * @param int   $orderId
     * @param float $amount
     */
    public function saveOrderPosition($articleId, $orderId, $amount)
    {
        $this->erp->AddArtikelAuftrag($articleId, $orderId, $amount);
    }

    /**
     * @param $articleId
     * @param $purchaseOrderId
     * @param $amount
     */
    public function savePurchaseOrderPosition($articleId, $purchaseOrderId, $amount)
    {
        $supplierId = $this->db->fetchValue(
            "SELECT `adresse` FROM `bestellung` WHERE `id` = :purchaseOrderId",
            ['purchaseOrderId' => $purchaseOrderId]
        );

        $purchasePriceId = $this->erp->Einkaufspreis($articleId, $amount, $supplierId);
        $this->erp->AddBestellungPosition($purchaseOrderId, $purchasePriceId, $amount, '0000-00-00');
    }
}
