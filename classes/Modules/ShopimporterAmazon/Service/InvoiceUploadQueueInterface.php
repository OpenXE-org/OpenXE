<?php

declare(strict_types=1);

namespace Xentral\Modules\ShopimporterAmazon\Service;

use DateTimeInterface;
use Xentral\Modules\ShopimporterAmazon\Data\InvoiceUpload;

interface InvoiceUploadQueueInterface
{
    /**
     * get next Invoice Request to Invoice-information and PDF to Amazon. This has to be sent in 3 seconds interval
     *
     * @param int               $shopId
     * @param DateTimeInterface $startDate
     *
     * @return InvoiceUpload|null
     */
    public function getNextInvoiceUploadRequest(int $shopId, DateTimeInterface $startDate): ?InvoiceUpload;
}
