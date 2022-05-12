<?php

declare(strict_types=1);

namespace Xentral\Modules\ShopimporterAmazon\Service;

use Xentral\Modules\ShopimporterAmazon\Data\InvoiceUpload;

interface InvoiceUploadDocumentInterface
{
    /**
     * @param InvoiceUpload $invoiceUpload
     *
     * @return int
     */
    public function create(InvoiceUpload $invoiceUpload): int;

    /**
     * @param InvoiceUpload $invoiceUpload
     */
    public function update(InvoiceUpload $invoiceUpload): void;
}
