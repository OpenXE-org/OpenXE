<?php

declare(strict_types=1);

namespace Xentral\Modules\ShopimporterAmazon\Service;

interface AmazonDocumentInterface
{
    public function getArticleBySku(int $orderId, string $sku, string $itemId = ''): ?int;

    public function getPositionsFromOrderId(int $orderId, ?string $itemId = null): ?array;

    public function getShippingArticleIdsByShopId(int $shopId): ?array;

    public function getShippingAmountInCreditNotes(int $invoiceId, array $shippingArticleIds): float;

    public function getArticleQuantityInCreditNotes(int $invoiceId, int $articleId): float;

    public function getArticleQuantityInOrder(int $orderId, int $articleId): float;

    public function getShippingAmountInOrder(int $orderId, array $shippingArticleIds): float;

    public function getInvoicesByOrderId(int $orderId): array;

    public function getOrderByExtId(string $extId): array;

    public function getCreditNoteIdByInvoiceIds(array $invoiceIds, ?string $documentDate = null): ?int;

    public function getCreditNotesByArticlesAndInvoiceIds(int $articleId, array $invoiceIds): array;
}
