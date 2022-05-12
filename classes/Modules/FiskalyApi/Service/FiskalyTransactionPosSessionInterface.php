<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Service;

use Xentral\Modules\FiskalyApi\Data\Export;
use Xentral\Modules\FiskalyApi\Data\Transaction\TransactionReponse;
use Xentral\Modules\FiskalyApi\Data\Transaction\TransactionReponseCollection;
use Xentral\Modules\FiskalyApi\Data\Transaction\TransactionRequest;

interface FiskalyTransactionPosSessionInterface
{
    public function get(string $trxId): ?array;

    public function insertTransactions(TransactionReponseCollection $transactionResponseCollection): void;

    public function getTransactionIdFromTrxId(string $trxId): ?int;

    public function tryMapDocument(string $trxId, string $document, int $documentId): int;

    public function create(
        ?TransactionRequest $request,
        ?TransactionReponse $response
    ): int;

    public function update(
        int $fiskalyTransactionPosSessionId,
        TransactionRequest $request,
        TransactionReponse $response
    ): void;

    public function createOrUpdateExport(Export $export): void;

    public function updateExport(Export $export): void;

    public function getExportUrlsNotInDms(string $tssId): array;

    public function getUuIdsByState(string $state, ?string $tssId = null): array;
}
