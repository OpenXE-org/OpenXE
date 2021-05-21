<?php

declare(strict_types=1);

namespace Xentral\Modules\EbayPayment\Service;

interface EbayPaymentDocumentServiceInterface
{
    public function getOrderShopIdFromOrderId(string $externalOrderId): ?array;

    public function getEbayShops(bool $onlyWithRestAccount, string $textToShowOnNotActivatedRestApi): array;
}
