<?php

declare(strict_types=1);

namespace Xentral\Modules\PaymentTransaction\Service;

interface PaymentDocumentServiceInterface
{
    public function getOrderIdFromCreditNoteId(int $creditNoteId): ?array;

    public function getOrderByOrderId(string $externalOrderId): ?array;

    public function getCreditNoteFromInvoiceId(int $invoiceId): ?array;

    public function getInvoiceByIntOrderId(int $intOrderId): ?array;
}
