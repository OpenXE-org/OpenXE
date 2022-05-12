<?php

declare(strict_types=1);

namespace Xentral\Modules\ShopimporterAmazon\Service;

use Exception;
use Xentral\Components\Database\Database;
use Xentral\Modules\ShopimporterAmazon\Data\InvoiceUpload;
use Xentral\Modules\ShopimporterAmazon\Exception\InvalidArgumentException;
use Xentral\Modules\ShopimporterAmazon\Exception\InvoiceUploadNotFoundException;

final class InvoiceUploadDocumentService implements InvoiceUploadDocumentInterface
{
    /** @var Database $db */
    private $db;

    /**
     * InvoiceUploadDocumentService constructor.
     *
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * @param int $id
     *
     * @throws InvoiceUploadNotFoundException
     *
     * @return InvoiceUpload
     */
    public function getById(int $id): InvoiceUpload
    {
        $dbState = $this->db->fetchRow(
            "SELECT *
            FROM `shopimporter_amazon_invoice_upload`
            WHERE `id` = :id ",
            [
                'id' => $id,
            ]
        );

        if (empty($dbState)) {
            throw new InvoiceUploadNotFoundException("invoiceUpload with Id {$id} not found");
        }

        return InvoiceUpload::fromDbState($dbState);
    }

    /**
     * @param InvoiceUpload $invoiceUpload
     *
     * @return int
     */
    public function create(InvoiceUpload $invoiceUpload): int
    {
        if ($invoiceUpload->getId() !== null) {
            throw new InvalidArgumentException('InvoiceUpload-object has already an database-assignment');
        }
        $query = $this->db->insert()
            ->into('shopimporter_amazon_invoice_upload')
            ->cols(
                $invoiceUpload->toArray()
            );
        $this->db->perform(
            $query->getStatement(),
            $query->getBindValues()
        );

        return $this->db->lastInsertId();
    }

    /**
     * @param InvoiceUpload $invoiceUpload
     */
    public function update(InvoiceUpload $invoiceUpload): void
    {
        if ($invoiceUpload->getId() === null) {
            throw new InvalidArgumentException('InvoiceUpload-object has no database assignment');
        }
        $query = $this->db->update()
            ->table('shopimporter_amazon_invoice_upload')
            ->where('id=:id')
            ->bindValue('id', $invoiceUpload->getId())
            ->cols($invoiceUpload->toArray());
        $this->db->perform(
            $query->getStatement(),
            $query->getBindValues()
        );
    }
}
