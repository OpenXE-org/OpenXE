<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\CashPointClosing;

use ArrayIterator;
use Countable;
use IteratorAggregate;

class CashPointClosingApiResponseCollection implements IteratorAggregate, Countable
{
    /** @var CashPointClosingApiResponse[] */
    private $cashPointClosingApiResponses = [];

    /**
     * CashPointClosingApiResponseCollection constructor.
     *
     * @param CashPointClosingApiResponse[] $cashPointClosingApiResponses
     */
    public function __construct(array $cashPointClosingApiResponses = [])
    {
        foreach ($cashPointClosingApiResponses as $apiResponse) {
            $this->addCashPointClosingApiResponse($apiResponse);
        }
    }

    /**
     * @param CashPointClosingApiResponse $apiResponse
     */
    public function addCashPointClosingApiResponse(CashPointClosingApiResponse $apiResponse): void
    {
        $this->cashPointClosingApiResponses[] = CashPointClosingApiResponse::fromDbState($apiResponse->toArray());
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
            $instance->addCashPointClosingApiResponse(CashPointClosingApiResponse::fromApiResult($item));
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
            $instance->addCashPointClosingApiResponse(CashPointClosingApiResponse::fromDbState($item));
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
     * @return array
     */
    public function toApiResult(): array
    {
        $apiResult = [];
        foreach ($this as $item) {
            $dbState[] = $item->toApiResult();
        }

        return $apiResult;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->cashPointClosingApiResponses);
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->cashPointClosingApiResponses);
    }
}
