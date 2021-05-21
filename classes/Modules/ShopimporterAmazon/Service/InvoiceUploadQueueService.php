<?php

declare(strict_types=1);

namespace Xentral\Modules\ShopimporterAmazon\Service;

use DateTimeInterface;
use Exception;
use Xentral\Components\Database\Database;
use Xentral\Modules\ShopimporterAmazon\Data\InvoiceUpload;

class InvoiceUploadQueueService implements InvoiceUploadQueueInterface
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
     * get next Invoice Request to Invoice-information and PDF to Amazon. This has to be sent in 3 seconds interval
     *
     * @param int               $shopId
     * @param DateTimeInterface $startDate
     *
     * @return InvoiceUpload|null
     */
    public function getNextInvoiceUploadRequest(int $shopId, DateTimeInterface $startDate): ?InvoiceUpload
    {
        $dbState = $this->db->fetchRow(
            "SELECT saiu.*
            FROM `shopimporter_amazon_invoice_upload` AS `saiu`
            INNER JOIN `rechnung` AS `i` ON saiu.invoice_id = i.id
                                                  AND i.datum >= :start_date
            WHERE ( saiu.sent_at IS NULL OR saiu.sent_at <= :sent_at OR saiu.status = '') 
                         AND saiu.shop_id = :shop_id AND saiu.status <> 'error' AND saiu.marketplace <> '' 
                AND saiu.invoice_id > 0
            ORDER BY saiu.created_at 
            LIMIT 1 ",
            [
                'shop_id'    => $shopId,
                'start_date' => $startDate->format('Y-m-d'),
                'sent_at'    => '1970-01-02 00:00:00'
            ]
        );
        if (empty($dbState)) {
            return null;
        }

        return InvoiceUpload::fromDbState($dbState);
    }
}
