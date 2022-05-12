<?php

declare(strict_types=1);

namespace Xentral\Modules\ShopimporterAmazon\Service;

use Xentral\Components\Database\Database;

final class AmazonDocumentService implements AmazonDocumentInterface
{
    /** @var Database $db */
    private $db;

    /**
     * AmazonDocumentService constructor.
     *
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * @param int    $orderId
     * @param string $sku
     * @param string $itemId
     *
     * @return int|null
     */
    public function getArticleBySku(int $orderId, string $sku, string $itemId = ''): ?int
    {
        if (!empty($itemId)) {
            $articleId = (int)$this->db->fetchValue(
                'SELECT `artikel` FROM `auftrag_position` WHERE `webid` = :webid AND `auftrag` = :order_id LIMIT 1',
                [
                    'webid'    => $itemId,
                    'order_id' => $orderId,
                ]
            );
            if ($articleId > 0) {
                return $articleId;
            }
        }
        if (empty($sku)) {
            return null;
        }
        $articleId = (int)$this->db->fetchValue(
            "SELECT af.artikel 
            FROM `artikelnummer_fremdnummern` AS af 
            INNER JOIN artikel AS art ON af.artikel = art.id AND art.geloescht <> 1 
            WHERE af.nummer <> '' AND af.nummer = :sku AND af.aktiv = 1 
            LIMIT 1",
            ['sku' => $sku]
        );
        if ($articleId > 0) {
            return $articleId;
        }

        $articleId = (int)$this->db->fetchValue(
            "SELECT art.id 
            FROM artikel AS art 
            WHERE art.geloescht <> 1 AND art.nummer = :sku AND art.nummer <> '' AND art.nummer <> 'DEL' 
            LIMIT 1",
            ['sku' => $sku]
        );

        return $articleId > 0 ? $articleId : null;
    }

    /**
     * @param int         $orderId
     * @param string|null $itemId
     *
     * @return array|null
     */
    public function getPositionsFromOrderId(int $orderId, ?string $itemId = null): ?array
    {
        if (!empty($itemId)) {
            return $this->db->fetchAll(
                'SELECT * 
                FROM `auftrag_position` 
                WHERE `auftrag` = :order_id 
                ORDER BY `webid` = :webid DESC, `sort`, `id` 
                LIMIT 1',
                [
                    'order_id' => $orderId,
                    'webid'    => $itemId,
                ]
            );
        }

        return $this->db->fetchAll(
            'SELECT * 
            FROM `auftrag_position` 
            WHERE `auftrag` = :order_id 
            ORDER BY `sort`, `id` 
            LIMIT 1',
            [
                'order_id' => $orderId,
            ]
        );
    }

    /**
     * @param int $shopId
     *
     * @return array|null
     */
    public function getShippingArticleIdsByShopId(int $shopId): ?array
    {
        $shopExport = $this->db->fetchRow(
            'SELECT `artikelportoermaessigt`, `artikelporto` FROM `shopexport` WHERE `id` = :shop_id',
            ['shop_id' => $shopId]
        );
        if (empty($shopExport)) {
            return null;
        }

        $articleIds = [];
        if (!empty($shopExport['artikelportoermaessigt'])) {
            $articleIds[] = (int)$shopExport['artikelportoermaessigt'];
        }
        if (!empty($shopExport['artikelporto'])) {
            $articleIds[] = (int)$shopExport['artikelporto'];
        }

        return empty($articleIds) ? null : array_unique($articleIds);
    }

    /**
     * @param int   $invoiceId
     * @param array $shippingArticleIds
     *
     * @return float
     */
    public function getShippingAmountInCreditNotes(int $invoiceId, array $shippingArticleIds): float
    {
        return (float)$this->db->fetchValue(
            "SELECT SUM(cnp.preis * cnp.menge)
            FROM `gutschrift` AS `cn` 
            INNER JOIN `gutschrift_position` AS `cnp` ON cn.id = cnp.gutschrift
            INNER JOIN `artikel` AS `art` ON cnp.artikel = art.id
            WHERE cn.rechnungid = :invoice_id AND cn.rechnungid != 0 AND cn.status <> 'storniert' 
                  AND (art.porto = 1 OR art.id IN (:shipping_article_ids))",
            [
                'invoice_id'           => $invoiceId,
                'shipping_article_ids' => $shippingArticleIds,
            ]
        );
    }

    /**
     * @param int $invoiceId
     * @param int $articleId
     *
     * @return float
     */
    public function getArticleQuantityInCreditNotes(int $invoiceId, int $articleId): float
    {
        return (float)$this->db->fetchValue(
            "SELECT SUM(gspos.menge)
            FROM `gutschrift` AS `gs` 
            INNER JOIN `gutschrift_position` AS `gspos` ON gs.id = gspos.gutschrift AND gspos.artikel = :article_id
            WHERE gs.rechnungid = :invoice_id AND gs.rechnungid != 0 AND gs.status <> 'storniert'",
            [
                'invoice_id' => $invoiceId,
                'article_id' => $articleId,
            ]
        );
    }

    /**
     * @param int $orderId
     * @param int $articleId
     *
     * @return float
     */
    public function getArticleQuantityInOrder(int $orderId, int $articleId): float
    {
        return (float)$this->db->fetchValue(
            "SELECT SUM(op.menge)
            FROM `auftrag` AS `o` 
            INNER JOIN `auftrag_position` AS `op` ON o.id = op.auftrag AND op.artikel = :article_id
            WHERE o.id = :order_id",
            [
                'order_id'   => $orderId,
                'article_id' => $articleId,
            ]
        );
    }

    /**
     * @param int   $orderId
     * @param array $shippingArticleIds
     *
     * @return float
     */
    public function getShippingAmountInOrder(int $orderId, array $shippingArticleIds): float
    {
        return (float)$this->db->fetchValue(
            "SELECT SUM(op.preis * op.menge)
            FROM `auftrag` AS `o` 
            INNER JOIN `auftrag_position` AS `op` ON o.id = op.auftrag
            INNER JOIN `artikel` AS `art` ON op.artikel = art.id
            WHERE o.id = :order_id AND (art.porto = 1 OR art.id IN (:shipping_article_ids))",
            [
                'order_id'             => $orderId,
                'shipping_article_ids' => $shippingArticleIds,
            ]
        );
    }

    /**
     * @param int $orderId
     *
     * @return array
     */
    public function getInvoicesByOrderId(int $orderId): array
    {
        return $this->db->fetchRow(
            "SELECT * 
            FROM `rechnung` 
            WHERE `auftragid` = :order_id AND `status` <> 'angelegt' 
            ORDER BY `status` = 'storniert'",
            ['order_id' => $orderId]
        );
    }

    /**
     * @param string $extId
     *
     * @return array
     */
    public function getOrderByExtId(string $extId): array
    {
        return $this->db->fetchRow(
            "SELECT * 
            FROM `auftrag` 
            WHERE `shopextid` = :ext_id AND `shopextid` <> '' AND `shopextid` IS NOT NULL AND `status` <> 'storniert'
            LIMIT 1",
            ['ext_id' => $extId]
        );
    }

    /**
     * @param array       $invoiceIds
     * @param string|null $documentDate
     *
     * @return int|null
     */
    public function getCreditNoteIdByInvoiceIds(array $invoiceIds, ?string $documentDate = null): ?int
    {
        if (!empty($documentDate)) {
            $creditNoteId = $this->db->fetchValue(
                "SELECT gs.id 
                FROM `gutschrift` AS `gs` 
                WHERE gs.rechnungid IN (:invoice_ids) AND gs.rechnungid <> 0 AND gs.rechnungid <> '' 
                  AND (gs.datum = CURDATE() OR gs.datum = :document_date) 
                ORDER BY gs.datum = :document_date DESC
                LIMIT 1",
                [
                    'invoice_ids'   => $invoiceIds,
                    'document_date' => $documentDate,
                ]

            );
        } else {
            $creditNoteId = $this->db->fetchValue(
                'SELECT gs.id 
                FROM `gutschrift` AS `gs` 
                WHERE gs.rechnungid IN (:invoice_ids) AND gs.rechnungid <> 0 AND gs.rechnungid <> \'\' 
                  AND gs.datum = CURDATE() 
                LIMIT 1',
                [
                    'invoice_ids' => $invoiceIds,
                ]
            );
        }

        return $creditNoteId === false ? null : (int)$creditNoteId;
    }

    /**
     * @param int   $articleId
     * @param array $invoiceIds
     *
     * @return array
     */
    public function getCreditNotesByArticlesAndInvoiceIds(int $articleId, array $invoiceIds): array
    {
        return $this->db->fetchPairs(
            'SELECT gs.id, gs.rechnungid 
            FROM `gutschrift` AS `gs` 
            INNER JOIN `gutschrift_position` AS `gspos` ON gs.id = gspos.gutschrift AND gspos.artikel = :article_id
            WHERE gs.rechnungid IN (:invoice_ids)  AND gs.rechnungid!=0 AND gs.rechnungid != 0
            LIMIT 1',
            [
                'article_id'  => $articleId,
                'invoice_ids' => $invoiceIds,
            ]

        );
    }
}
