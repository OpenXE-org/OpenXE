<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\Transaction;

use ArrayIterator;
use Countable;
use Exception;
use IteratorAggregate;

class TransactionReponseCollection implements IteratorAggregate, Countable
{
    /** @var array $transactionResponses */
    private $transactionResponses = [];

    /**
     * TransactionReponseCollection constructor.
     *
     * @param array $transactionResponses
     */
    public function __construct(array $transactionResponses = [])
    {
        foreach ($transactionResponses as $transactionResponse) {
            $this->addTransactionResponse($transactionResponse);
        }
    }

    /**
     * @param TransactionReponse $transactionReponse
     */
    public function addTransactionResponse(TransactionReponse $transactionReponse): void
    {
        $this->transactionResponses[] = TransactionReponse::fromDbState($transactionReponse->toArray());
    }

    /**
     * @param $apiResult
     *
     * @throws Exception
     * @return static
     */
    public static function fromApiResult(object $apiResult): self
    {
        $instance = new self();
        foreach ($apiResult as $item) {
            $instance->addTransactionResponse(TransactionReponse::fromApiResult($item));
        }

        return $instance;
    }

    /**
     * @param array $dbState
     *
     * @return static
     */
    public static function fromDbState(array $dbState): self
    {
        $instance = new self();
        foreach ($dbState as $item) {
            $instance->addTransactionResponse(TransactionReponse::fromDbState($item));
        }

        return $instance;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $dbState = [];
        /** @var TransactionReponse $item */
        foreach ($this as $item) {
            $dbState[] = $item->toArray();
        }

        return $dbState;
    }

    /**
     * @return array
     */
    public function toApiResult(): array
    {
        $dbState = [];
        /** @var TransactionReponse $item */
        foreach ($this as $item) {
            $dbState[] = $item->toApiResult();
        }

        return $dbState;
    }

    /**
     * @return array
     */
    public function getClientIds(): array
    {
        $clientIds = [];
        /** @var TransactionReponse $item */
        foreach ($this as $item) {
            $clientId = $item->getClientId();
            if (!in_array($clientId, $clientIds, true)) {
                $clientIds[] = $clientId;
            }
        }

        return $clientIds;
    }

    /**
     * @return array
     */
    public function getTrxIds(): array
    {
        $rxIds  = [];
        /** @var TransactionReponse $item */
        foreach ($this as $item) {
            $rxId = $item->getId();
            if (!in_array($rxId, $rxIds, true)) {
                $rxIds[] = $rxId;
            }
        }

        return $rxIds;
    }

    /**
     * @return array
     */
    public function getTransactionDates(): array
    {
        $dates = [];
        /** @var TransactionReponse $item */
        foreach ($this as $item) {
            $startDate = $item->getTimeStart();
            if($startDate === null) {
                continue;
            }
            $date = $startDate->format('Y-m-d');
            if(!in_array($date, $dates)) {
                $dates[] = $date;
            }
        }
        sort($dates);

        return $dates;
    }

    /**
     * @param string $clientId
     *
     * @return $this
     */
    public function filterClientId(string $clientId): self
    {
        $instance = new self();
        /** @var TransactionReponse $item */
        foreach ($this as $item) {
            if ($clientId !== $item->getClientId()) {
                continue;
            }
            $instance->addTransactionResponse($item);
        }

        return $instance;
    }

    /**
     * @param string $date
     *
     * @return $this
     */
    public function filterDate(string $date): self
    {
        $instance = new self();
        /** @var TransactionReponse $item */
        foreach ($this as $item) {
            $startDate = $item->getTimeStart();
            if($startDate === null) {
                continue;
            }
            if ($date !== $startDate->format('Y-m-d')) {
                continue;
            }
            $instance->addTransactionResponse($item);
        }

        return $instance;
    }


    /**
     * @param string $clientId
     * @param bool   $first
     *
     * @return TransactionReponse|null
     */
    public function getBoundedTransactionWithClientId(string $clientId, bool $first = true): ?TransactionReponse
    {
        $actualKey = null;
        $actualNumber = null;
        /** @var TransactionReponse $item */
        foreach ($this as $key => $item) {
            if ($clientId !== $item->getClientId()) {
                continue;
            }
            $number = $item->getNumber();
            if ($number === null) {
                continue;
            }
            if ($actualNumber === null || ($actualNumber > $number && $first) || ($actualNumber < $number && !$first)) {
                $actualKey = $key;
                $actualNumber = $number;
            }
        }

        return $actualKey === null ? null : TransactionReponse::fromDbState(
            $this->transactionResponses[$actualKey]->toArray()
        );
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->transactionResponses);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->transactionResponses);
    }
}
