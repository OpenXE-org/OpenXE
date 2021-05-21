<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\CashPointClosing;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Xentral\Modules\FiskalyApi\Exception\InvalidArgumentException;

class CashPointClosingTransactionLineReferenceCollection implements IteratorAggregate, Countable
{
    public const TYPE_TRANSACTION = 'Transaktion';
    public const TYPE_EXTERNAL_INVOICE = 'ExterneRechnung';
    public const TYPE_EXTERNAL_DELIVERY_NOTE = 'ExternerLieferschein';
    public const TYPE_EXTERNAL_OTHER  = 'ExterneSonstige';
    public const TYPE_INTERNAL_TRANSACTION = 'InterneTransaktion';

    /** @var CashPointClosingTransactionLineReference[] $references */
    private $references = [];

    /**
     * CashPointClosingTransactionLineReferenceCollection constructor.
     *
     * @param array $references
     */
    public function __construct(array $references = [])
    {
        foreach ($references as $reference) {
            $this->addReference($reference);
        }
    }

    /**
     * @param CashPointClosingTransactionLineReference $reference
     */
    public function addReference(CashPointClosingTransactionLineReference $reference): void
    {
        $this->references[] = $reference::fromDbState($reference->toArray());
    }

    /**
     * @param $apiResult
     *
     * @return static
     */
    public static function fromApiResult(object $apiResult): self
    {
        $instance = new self();
        foreach ($apiResult as $item) {
            switch ($item->type) {
                case self::TYPE_TRANSACTION:
                    $instance->addReference(CashPointClosingTransaktionReference::fromApiResult($item));
                    break;
                case self::TYPE_EXTERNAL_INVOICE:
                case self::TYPE_EXTERNAL_DELIVERY_NOTE:
                    $instance->addReference(CashPointClosingExternalDocumentReference::fromApiResult($item));
                    break;
                case self::TYPE_EXTERNAL_OTHER:
                    $instance->addReference(CashPointClosingOtherReference::fromApiResult($item));
                    break;
                case self::TYPE_INTERNAL_TRANSACTION:
                    $instance->addReference(CashPointClosingInternalTransaktionReference::fromApiResult($item));
                    break;
                default:
                    throw new InvalidArgumentException("unknown Reference {$item->type}");
            }
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
            switch ($item['type']) {
                case self::TYPE_TRANSACTION:
                    $instance->addReference(CashPointClosingTransaktionReference::fromDbState($item));
                    break;
                case self::TYPE_EXTERNAL_INVOICE:
                case self::TYPE_EXTERNAL_DELIVERY_NOTE:
                    $instance->addReference(CashPointClosingExternalDocumentReference::fromDbState($item));
                    break;
                case self::TYPE_EXTERNAL_OTHER:
                    $instance->addReference(CashPointClosingOtherReference::fromDbState($item));
                    break;
                case self::TYPE_INTERNAL_TRANSACTION:
                    $instance->addReference(CashPointClosingInternalTransaktionReference::fromDbState($item));
                    break;
                default:
                    throw new InvalidArgumentException("unknown Reference {$item['type']}");
            }
        }

        return $instance;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $dbState = [];
        foreach ($this as $item) {
            $dbState[] = $item->toArray();
        }

        return $dbState;
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->references);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->references);
    }
}
