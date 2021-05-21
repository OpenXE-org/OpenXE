<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\CashPointClosing;

use DateTime;
use DateTimeInterface;
use Xentral\Modules\FiskalyApi\Exception\InvalidArgumentException;

class TransactionHead
{
    /** @var string $txId */
    private $txId;

    /** @var string $transactionExportId */
    private $transactionExportId;

    /** @var string $closingClientId */
    private $closingClientId;

    /** @var string $type */
    private $type;

    /** @var bool $storno */
    private $storno;

    /** @var int $number */
    private $number;

    /** @var DateTimeInterface $timestampStart */
    private $timestampStart;

    /** @var DateTimeInterface $timestampEnd */
    private $timestampEnd;

    /** @var CashPointClosingTransactionUser $user */
    private $user;

    /** @var CashPointClosingTransactionBuyer $buyer */
    private $buyer;

    /** @var CashPointClosingTransactionLineReferenceCollection $references */
    private $references;

    /** @var array|null $allocationGroups */
    private $allocationGroups;

    /**
     * TransactionHead constructor.
     *
     * @param string                                                   $txId
     * @param string                                                   $transactionExportId
     * @param string                                                   $closingClientId
     * @param string                                                   $type
     * @param bool                                                     $storno
     * @param int                                                      $number
     * @param DateTimeInterface                                        $timestampStart
     * @param DateTimeInterface                                        $timestampEnd
     * @param CashPointClosingTransactionUser                          $user
     * @param CashPointClosingTransactionBuyer                         $buyer
     * @param CashPointClosingTransactionLineReferenceCollection|null $references
     * @param array|null                                               $allocationGroups
     */
    public function __construct(
        string $txId,
        string $transactionExportId,
        string $closingClientId,
        string $type,
        bool $storno,
        int $number,
        DateTimeInterface $timestampStart,
        DateTimeInterface $timestampEnd,
        CashPointClosingTransactionUser $user,
        CashPointClosingTransactionBuyer $buyer,
        ?CashPointClosingTransactionLineReferenceCollection $references = null,
        ?array $allocationGroups = null
    ) {
        $this->setTxId($txId);
        $this->setTransactionExportId($transactionExportId);
        $this->setClosingClientId($closingClientId);
        $this->setType($type);
        $this->setStorno($storno);
        $this->setNumber($number);
        $this->setTimestampStart($timestampStart);
        $this->setTimestampEnd($timestampEnd);
        $this->setUser($user);
        $this->setBuyer($buyer);
        $this->setReferences($references);
        $this->setAllocationGroups($allocationGroups);
    }

    /**
     * @param $apiResult
     *
     * @return static
     */
    public static function fromApiResult(object $apiResult): self
    {
        return new self(
            $apiResult->tx_id,
            $apiResult->transaction_export_id,
            $apiResult->closing_client_id,
            $apiResult->type,
            (bool)$apiResult->storno,
            (int)$apiResult->number,
            (new DateTime())->setTimestamp($apiResult->timestamp_start),
            (new DateTime())->setTimestamp($apiResult->timestamp_end),
            CashPointClosingTransactionUser::fromApiResult($apiResult->user),
            CashPointClosingTransactionBuyer::fromApiResult($apiResult->buyer),
            !empty($apiResult->references) ? CashPointClosingTransactionLineReferenceCollection::fromApiResult(
                $apiResult->references
            ) : null,
            $apiResult->allocation_groups ?? null
        );
    }

    /**
     * @param array $dbState
     *
     * @return static
     */
    public static function fromDbState(array $dbState): self
    {
        return new self(
            $dbState['tx_id'],
            $dbState['transaction_export_id'],
            $dbState['closing_client_id'],
            $dbState['type'],
            (bool)$dbState['storno'],
            (int)$dbState['number'],
            (new DateTime())->setTimestamp($dbState['timestamp_start']),
            (new DateTime())->setTimestamp($dbState['timestamp_end']),
            CashPointClosingTransactionUser::fromDbState($dbState['user']),
            CashPointClosingTransactionBuyer::fromDbState($dbState['buyer']),
            !empty($dbState['references']) ? CashPointClosingTransactionLineReferenceCollection::fromDbState(
                $dbState['references']
            ) : null,
            $dbState['allocation_groups'] ?? null
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $dbState = [
            'tx_id'                 => $this->getTxId(),
            'transaction_export_id' => $this->getTransactionExportId(),
            'closing_client_id'     => $this->getClosingClientId(),
            'type'                  => $this->getType(),
            'storno'                => $this->isStorno(),
            'number'                => $this->getNumber(),
            'timestamp_start'       => $this->getTimestampStart()->getTimestamp(),
            'timestamp_end'         => $this->getTimestampEnd()->getTimestamp(),
            'user'                  => $this->getUser()->toArray(),
            'buyer'                 => $this->getBuyer()->toArray(),
        ];
        if ($this->references !== null) {
            $dbState['references'] = $this->getReferences()->toArray();
        }
        if ($this->allocationGroups !== null) {
            $dbState['allocation_groups'] = $this->getAllocationGroups();
        }

        return $dbState;
    }

    /**
     * @return string
     */
    public function getTxId(): string
    {
        return $this->txId;
    }

    /**
     * @param string $txId
     */
    public function setTxId(string $txId): void
    {
        $this->txId = $txId;
    }

    /**
     * @return string
     */
    public function getTransactionExportId(): string
    {
        return $this->transactionExportId;
    }

    /**
     * @param string $transactionExportId
     */
    public function setTransactionExportId(string $transactionExportId): void
    {
        $this->transactionExportId = $transactionExportId;
    }

    /**
     * @return string
     */
    public function getClosingClientId(): string
    {
        return $this->closingClientId;
    }

    /**
     * @param string $closingClientId
     */
    public function setClosingClientId(string $closingClientId): void
    {
        $this->closingClientId = $closingClientId;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->ensureType($type);
        $this->type = $type;
    }

    /**
     * @return bool
     */
    public function isStorno(): bool
    {
        return $this->storno;
    }

    /**
     * @param bool $storno
     */
    public function setStorno(bool $storno): void
    {
        $this->storno = $storno;
    }

    /**
     * @return int
     */
    public function getNumber(): int
    {
        return $this->number;
    }

    /**
     * @param int $number
     */
    public function setNumber(int $number): void
    {
        $this->number = $number;
    }

    /**
     * @return DateTimeInterface
     */
    public function getTimestampStart(): DateTimeInterface
    {
        return $this->timestampStart;
    }

    /**
     * @param DateTimeInterface $timestampStart
     */
    public function setTimestampStart(DateTimeInterface $timestampStart): void
    {
        $this->timestampStart = $timestampStart;
    }

    /**
     * @return DateTimeInterface
     */
    public function getTimestampEnd(): DateTimeInterface
    {
        return $this->timestampEnd;
    }

    /**
     * @param DateTimeInterface $timestampEnd
     */
    public function setTimestampEnd(DateTimeInterface $timestampEnd): void
    {
        $this->timestampEnd = $timestampEnd;
    }

    /**
     * @return CashPointClosingTransactionUser
     */
    public function getUser(): CashPointClosingTransactionUser
    {
        return $this->user;
    }

    /**
     * @param CashPointClosingTransactionUser $user
     */
    public function setUser(CashPointClosingTransactionUser $user): void
    {
        $this->user = $user;
    }

    /**
     * @return CashPointClosingTransactionBuyer
     */
    public function getBuyer(): CashPointClosingTransactionBuyer
    {
        return $this->buyer;
    }

    /**
     * @param CashPointClosingTransactionBuyer $buyer
     */
    public function setBuyer(CashPointClosingTransactionBuyer $buyer): void
    {
        $this->buyer = $buyer;
    }

    /**
     * @return CashPointClosingTransactionLineReferenceCollection|null
     */
    public function getReferences(): ?CashPointClosingTransactionLineReferenceCollection
    {
        return $this->references === null ? null : CashPointClosingTransactionLineReferenceCollection::fromDbState(
            $this->references->toArray()
        );
    }

    /**
     * @param CashPointClosingTransactionLineReferenceCollection|null $references
     */
    public function setReferences(?CashPointClosingTransactionLineReferenceCollection $references): void
    {
        $this->references = $references === null ? null : CashPointClosingTransactionLineReferenceCollection::fromDbState(
            $references->toArray()
        );
    }

    /**
     * @return array|null
     */
    public function getAllocationGroups(): ?array
    {
        return $this->allocationGroups;
    }

    /**
     * @param array|null $allocationGroups
     */
    public function setAllocationGroups(?array $allocationGroups): void
    {
        $this->allocationGroups = $allocationGroups;
    }


    /**
     * @param string $type
     */
    private function ensureType(string $type): void
    {
        $validTypes = [
            'Beleg',
            'AVTransfer',
            'AVBestellung',
            'AVTraining',
            'AVBelegstorno',
            'AVBelegabbruch',
            'AVSachbezug',
            'AVSonstige',
            'AVRechnung',
        ];
        if (!in_array($type, $validTypes)) {
            throw new InvalidArgumentException("{$type} is an invalid Type");
        }
    }
}
