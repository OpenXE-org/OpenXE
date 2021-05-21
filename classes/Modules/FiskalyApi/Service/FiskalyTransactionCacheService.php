<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Service;

use Aura\SqlQuery\Exception;
use Xentral\Modules\FiskalyApi\Data\Transaction\TransactionReponse;
use Xentral\Modules\FiskalyApi\Transaction\Transaction;

class FiskalyTransactionCacheService
{
    /** @var array */
    private $transactions = [];

    /** @var array $transactionResponse */
    private $transactionResponse = [];

    /** @var array $documentMappings */
    private $documentMappings = [];

    /** @var array $error */
    private $error = [];

    public function __construct()
    {
    }

    /**
     * @param int    $id
     * @param string $document
     * @param int    $documentId
     */
    public function addDocumentMapping(int $id, string $document, int $documentId): void
    {
        $this->documentMappings[$id][] = ['document' => $document, 'document_id' => $documentId];
    }

    /**
     * @param int $id
     *
     * @return array
     */
    public function getDocumentMappings(int $id): array
    {
        return $this->documentMappings[$id] ?? [];
    }

    /**
     * @param int         $id
     * @param Transaction $transaction
     *
     * @return void
     */
    public function put(int $id, Transaction $transaction): void
    {
        $this->transactions[$id] = $transaction;
    }

    /**
     * @param int                $id
     * @param TransactionReponse $transactionResponse
     */
    public function putTransactionResponse(int $id, TransactionReponse $transactionResponse): void
    {
        $this->transactionResponse[$id] = TransactionReponse::fromDbState($transactionResponse->toArray());
    }

    /**
     * @param int $id
     *
     * @return TransactionReponse
     */
    public function getTransactionResponse(int $id): TransactionReponse
    {
        return TransactionReponse::fromDbState($this->transactionResponse[$id]->toArray());
    }

    /**
     * @param int    $id
     * @param string $errorMessage
     * @param string $sma
     */
    public function putErrorMessage(int $id, string $errorMessage, string $sma): void
    {
        $this->error[$id] = ['sma' => $sma, 'error_message' => $errorMessage];
    }

    /**
     * @param int $id
     *
     * @return array|null
     */
    public function getErrorMessage(int $id): ?array
    {
        if (!isset($this->error[$id])) {
            return null;
        }

        return $this->error[$id];
    }

    /**
     * @param int $id
     *
     * @return Transaction
     */
    public function get(int $id): Transaction
    {
        return $this->transactions[$id];
    }

    /**
     * @param int $id
     */
    public function reset(int $id): void
    {
        if (isset($this->documentMappings[$id])) {
            unset($this->documentMappings[$id]);
        }
        if (isset($this->transactionResponse[$id])) {
            unset($this->transactionResponse[$id]);
        }
        if (isset($this->error[$id])) {
            unset($this->error[$id]);
        }
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public function hasTransaction(int $id): bool
    {
        return array_key_exists($id, $this->transactionResponse);
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public function hasError(int $id): bool
    {
        return array_key_exists($id, $this->error);
    }

    public function getNextOpenKey(): int
    {
        if (empty($this->transactionResponse)) {
            return 0;
        }
        $keys = array_diff(range(0, count($this->transactionResponse)), array_keys($this->transactionResponse));

        return reset($keys);
    }
}
