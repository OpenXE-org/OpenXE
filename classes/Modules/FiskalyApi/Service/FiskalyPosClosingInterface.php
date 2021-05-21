<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Service;

use Xentral\Modules\FiskalyApi\Data\Transaction\TransactionReponseCollection;

interface FiskalyPosClosingInterface
{
    public function getNextCashPointClosingExportId(string $clientId): int;

    public function getOpenTransactions(string $clientId): TransactionReponseCollection;
}
