<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Service;

use Xentral\Modules\FiskalyApi\Data\CashPointClosing\CashPointClosingApiResponse;
use Xentral\Modules\FiskalyApi\Data\Transaction\TransactionReponse;

interface FiskalyCashPointClosingDBInterface
{
    public function create(CashPointClosingApiResponse $cashPointClosingApiResponse): int;

    public function update(CashPointClosingApiResponse $cashPointClosingApiResponse): void;

    public function get(int $id): ?CashPointClosingApiResponse;

    public function getIdByClosingId(string $closingId): ?int;

    public function getByClosingId(string $closingId): ?CashPointClosingApiResponse;

    public function createTransactionMapping(
        CashPointClosingApiResponse $cashPointClosing,
        TransactionReponse $transaction
    ): int;

    public function getClosingIdsByState(string $clientId, string $state): array;
}
