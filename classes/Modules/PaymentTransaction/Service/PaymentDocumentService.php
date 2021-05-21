<?php

declare(strict_types=1);

namespace Xentral\Modules\PaymentTransaction\Service;

use Xentral\Components\Database\Database;

final class PaymentDocumentService implements PaymentDocumentServiceInterface
{
    /** @var Database $db */
    private $db;

    /**
     * PaymentDocumentService constructor.
     *
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * @param int $creditNoteId
     *
     * @return array|null
     */
    public function getOrderIdFromCreditNoteId(int $creditNoteId): ?array
    {
        $order = $this->db->fetchRow(
            "SELECT o.internet, cn.soll, cn.waehrung
            FROM `gutschrift` AS `cn` 
            LEFT JOIN `rechnung` AS `i` ON cn.rechnungid = i.status
            LEFT JOIN `auftrag` AS `o` 
                ON i.auftragid = o.id AND o.status <> 'angelegt' AND o.status <> 'storniert' AND o.internet <> ''
            WHERE cn.id = :credit_note_id AND cn.status <> 'storniert'
            LIMIT 1",
            ['credit_note_id' => $creditNoteId]
        );
        return $order === false ? null : $order;
    }

    /**
     * @param string $externalOrderId
     *
     * @return array|null
     */
    public function getOrderByOrderId(string $externalOrderId): ?array
    {
        if (empty($externalOrderId)) {
            return null;
        }

        $order = $this->db->fetchRow(
            "SELECT * 
            FROM `auftrag` 
            WHERE `internet` = :external_order_id AND `status` <> 'storniert' 
            ORDER BY `id` DESC 
            LIMIT 1",
            ['external_order_id' => $externalOrderId]
        );

        if (empty($order)) {
            return null;
        }

        return $order;
    }

    /**
     * @param int $invoiceId
     *
     * @return array|null
     */
    public function getCreditNoteFromInvoiceId(int $invoiceId): ?array
    {
        if ($invoiceId <= 0) {
            return null;
        }
        $creditNote = $this->db->fetchRow(
            "SELECT * 
            FROM `gutschrift` WHERE `rechnungid` = :invoice_id AND `status` <> 'storniert' 
            ORDER BY `id` DESC LIMIT 1",
            ['invoice_id' => $invoiceId]
        );

        if ($creditNote === false) {
            return null;
        }

        return $creditNote;
    }

    /**
     * @param int $intOrderId
     *
     * @return array|null
     */
    public function getInvoiceByIntOrderId(int $intOrderId): ?array
    {
        if ($intOrderId <= 0) {
            return null;
        }
        $invoice = $this->db->fetchRow(
            "SELECT * 
            FROM `rechnung` WHERE `auftragid` = :int_order_id AND `status` <> 'angelegt' 
            ORDER BY `id` DESC LIMIT 1",
            ['int_order_id' => $intOrderId]
        );

        if ($invoice === false) {
            return null;
        }

        return $invoice;
    }
}
