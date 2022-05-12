<?php

declare(strict_types=1);

namespace Xentral\Modules\Ebay\Wrapper;

use erpAPI;
use Xentral\Components\Database\Database;

/**
 * Anti-Corruption-Layer für erpApi->ArtikelAnzahlVerkaufbar()
 */
final class StockCalculationWrapper implements EbayStockCalculationWrapperInterface
{
    /** @var erpAPI $erp */
    private $erp;

    /**
     * @var Database
     */
    private $db;

    /**
     * @var array
     */
    private $maximumStockForShop = [];

    /**
     * @param erpAPI   $erp
     * @param Database $db
     */
    public function __construct($erp, $db)
    {
        $this->erp = $erp;
        $this->db = $db;
    }

    /**
     * @param int $articleId
     * @param int $shopId
     *
     * @return float
     */
    public function calculateStock($articleId, $shopId): float
    {
        if(empty($articleId)){
            return 0;
        }

        $pseudoStorage = trim($this->erp->GetArtikelShopEinstellung('pseudolager', $articleId, $shopId));

        if($pseudoStorage === '' && $this->erp->ModulVorhanden('pseudostorage')){
            $values = ['shop_id' => $shopId];
            $sql = 'SELECT p.formula FROM `pseudostorage_shop` AS `p` WHERE p.shop_id = :shop_id';
            $pseudoStorage = trim($this->db->fetchValue($sql, $values));
        }

        if(!empty($pseudoStorage)) {
            $this->erp->RunHook('remote_send_article_list_pseudostorage', 3, $shopId, $articleId, $pseudoStorage);
        }

        if ($pseudoStorage !== '') {
            return $this->recalculateForMaximumStock($pseudoStorage > 0 ? floor($pseudoStorage) : 0, $shopId);
        }

        $sql = 'SELECT s.lagergrundlage FROM `shopexport` AS `s` WHERE s.id = :shop_id';
        $values = ['shop_id' => $shopId];
        $lagergrundlage = $this->db->fetchValue($sql, $values);

        $sql = 'SELECT p.projektlager 
                FROM `projekt` AS `p` 
                JOIN `artikel` AS `a` ON p.id = a.projekt 
                WHERE a.id = :article_id';
        $values = ['article_id' => $articleId];
        $projektlager = $this->db->fetchValue($sql, $values);

        $calculatedStock = (float)$this->erp->ArtikelAnzahlVerkaufbar(
            $articleId,
            0,
            $projektlager,
            $shopId,
            $lagergrundlage
        );

        return $this->recalculateForMaximumStock($calculatedStock > 0 ? floor($calculatedStock) : 0, $shopId);
    }

    /**
     * @param float $originalStock
     * @param int   $shopId
     *
     * @return float
     */
    private function recalculateForMaximumStock(float $originalStock, int $shopId): float
    {
        if (!isset($this->maximumStockForShop[$shopId])) {
            $this->getMaximumStockForShop($shopId);
        }

        if ($this->maximumStockForShop[$shopId] > 0) {
            return $originalStock < $this->maximumStockForShop[$shopId]
                ? $originalStock : $this->maximumStockForShop[$shopId];
        }

        return $originalStock;
    }

    /**
     * @param int $shopId
     */
    private function getMaximumStockForShop(int $shopId): void
    {
        $sql = "SELECT s.einstellungen_json
                  FROM `shopexport` AS `s`
                  WHERE s.id = :shop_id
                  LIMIT 1";

        $values = ['shop_id' => $shopId];
        $importerSettings = $this->db->fetchValue($sql, $values);

        $maximumStock = 0;
        if (!empty(json_decode($importerSettings, true))) {
            $importerSettings = json_decode($importerSettings, true);
            $maximumStock = $importerSettings['felder']['lagerbestandmaxmenge'];
        }

        $this->maximumStockForShop[$shopId] = (float)$maximumStock;
    }
}
