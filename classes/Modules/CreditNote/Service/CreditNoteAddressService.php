<?php

declare(strict_types=1);

namespace Xentral\Modules\CreditNote\Service;

use Xentral\Components\Database\Database;

class CreditNoteAddressService
{
    public const ADDRESS_FIELDS = [
        'abteilung',
        'adresszusatz',
        'anschreiben',
        'ansprechpartner',
        'bundesstaat',
        'email',
        'gln',
        'land',
        'name',
        'ort',
        'plz',
        'strasse',
        'telefax',
        'telefon',
        'titel',
        'typ',
        'unterabteilung',
    ];

    private $db;

    /**
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->db = $database;
    }

    /**
     * Apply the correct billing address to data array that represents a Credit Note.
     *
     * If an order-specific billing address was explicitly defined while creating the
     * invoice, that one should be used also in the credit note.
     *
     * If custom billing address was not found, check whether a dedicated billing
     * address has been defined in the customer address data.
     *
     * If neither is found, return the data unchanged.
     *
     * @param int   $creditNoteId
     * @param array $data         Associative array containing the Credit Note data to be saved to db.
     *
     * @return array $data The updated Credit Note data array.
     */
    public function applyBillingAddressToCreditNoteArray(int $creditNoteId, array $data): array
    {
        $invoice = $this->getInvoiceById((int)$data['rechnungid']);

        if ($invoice) {
            return $this->getBillingAddressFromDocument($invoice, $data);
        }

        // Invoice was not found, so fall back to using the billing address
        return $this->getBillingAddressFromCustomer($creditNoteId, $data);
    }

    /**
     * if credit note was create from return order there will be a fallback to order if exists
     *
     * If invoice does not exists because return order was created manualy by order or
     * address is set to not create only deliverynotes on shipping-process
     * billing address need to come order if exists
     *
     * @param int   $creditNoteId
     * @param array $data
     *
     * @return array
     */
    public function applyBillingAddressFromReturnOrderToCreditNoteArray(int $creditNoteId, array $data): array
    {
        $invoice = $this->getInvoiceById((int)$data['rechnungid']);

        if ($invoice) {
            return $this->getBillingAddressFromDocument($invoice, $data);
        }

        $order = $this->getOrderById((int)$data['auftragid']);
        if (!empty($order) && null === $this->getInvoiceAddressFromAddressIdIfExists((int)$order['adresse'])) {
            return $this->getBillingAddressFromDocument($order, $data);
        }

        // Invoice was not found, so fall back to using the billing address
        return $this->getBillingAddressFromCustomer($creditNoteId, $data);
    }

    /**
     * Copy all address fields from the invoice into the credit note data.
     *
     * @param array $invoice
     * @param array $data
     *
     * @return array $data
     */
    private function getBillingAddressFromDocument(array $invoice, array $data): array
    {
        foreach (self::ADDRESS_FIELDS as $field) {
            $data[$field] = $invoice[$field];
        }

        return $data;
    }

    private function getInvoiceAddressFromAddressIdIfExists(int $addressId): ?array
    {
        $address = $this->getAddressById($addressId);
        if (!(bool)$address['abweichende_rechnungsadresse']) {
            return null;
        }

        $addressData = [];
        foreach (self::ADDRESS_FIELDS as $field) {
            // The address fields in the 'gutschrift' and 'adresse' table are otherwise
            // identical, but the billing address fields have the 'rechnung_' prefix.
            $addressData[$field] = $address["rechnung_{$field}"];
        }

        return $addressData;
    }

    private function getBillingAddressFromCustomer(int $creditNoteId, array $data): array
    {
        $creditNote = $this->getCreditNoteById($creditNoteId);

        $invoiceAddressData = $this->getInvoiceAddressFromAddressIdIfExists((int)$creditNote['adresse']);
        if($invoiceAddressData === null) {
            return $data;
        }

        foreach ($invoiceAddressData as $field => $value) {
            $data[$field] = $value;
        }

        return $data;
    }

    /**
     * Get credit note db row based on the row id.
     *
     * @param $id
     *
     * @return array
     */
    private function getCreditNoteById(int $id): array
    {
        return $this->db->fetchRow(
            'SELECT * FROM `gutschrift` WHERE `id` = :id',
            [
                'id' => $id,
            ]
        );
    }

    /**
     * Get invoice db row based on the row id.
     *
     * @param $id
     *
     * @return array
     */
    private function getInvoiceById(int $id): array
    {
        if ($id === 0) {
            return [];
        }
        return $this->db->fetchRow(
            'SELECT * FROM `rechnung` WHERE `id` = :id',
            [
                'id' => $id,
            ]
        );
    }

    /**
     * @param int $id
     *
     * @return array
     */
    private function getOrderById(int $id): array
    {
        if ($id === 0) {
            return [];
        }
        return $this->db->fetchRow(
            'SELECT * FROM `auftrag` WHERE `id` = :id',
            [
                'id' => $id,
            ]
        );
    }

    /**
     * Get address db row based on the row id.
     *
     * @param $id
     *
     * @return array
     */
    private function getAddressById(int $id): array
    {
        if ($id === 0) {
            return [];
        }
        return $this->db->fetchRow(
            'SELECT * FROM adresse WHERE id = :id',
            [
                'id' => $id,
            ]
        );
    }
}
