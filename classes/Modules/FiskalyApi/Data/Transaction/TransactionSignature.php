<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\Transaction;

use stdClass;

class TransactionSignature
{
    /** @var string $value */
    private $value;

    /** @var string $algorithm */
    private $algorithm;

    /** @var int $counter */
    private $counter;

    /** @var string $publicKey */
    private $publicKey;

    /**
     * TransactionSignature constructor.
     *
     * @param string $value
     * @param string $algorithm
     * @param int    $counter
     * @param string $publicKey
     */
    public function __construct(string $value, string $algorithm, int $counter, string $publicKey)
    {
        $this->setValue($value);
        $this->setAlgorithm($algorithm);
        $this->setCounter($counter);
        $this->setPublicKey($publicKey);
    }

    /**
     * @param $apiResult
     *
     * @return static
     */
    public static function fromApiResult(object $apiResult): self
    {
        return new self(
            $apiResult->value,
            $apiResult->algorithm,
            (int)$apiResult->counter,
            $apiResult->public_key
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
            $dbState['value'],
            $dbState['algorithm'],
            (int)$dbState['counter'],
            $dbState['public_key']
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'value'      => $this->getValue(),
            'algorithm'  => $this->getAlgorithm(),
            'counter'    => $this->getCounter(),
            'public_key' => $this->getPublicKey(),
        ];
    }

    /**
     * @return stdClass
     */
    public function toApiResult(): stdClass
    {
        $apiResult = new stdClass();

        $apiResult->value = $this->getValue();
        $apiResult->algorithm = $this->getAlgorithm();
        $apiResult->counter = $this->getCounter();
        $apiResult->public_key = $this->getPublicKey();

        return $apiResult;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getAlgorithm(): string
    {
        return $this->algorithm;
    }

    /**
     * @param string $algorithm
     */
    public function setAlgorithm(string $algorithm): void
    {
        $this->algorithm = $algorithm;
    }

    /**
     * @return int
     */
    public function getCounter(): int
    {
        return $this->counter;
    }

    /**
     * @param int $counter
     */
    public function setCounter(int $counter): void
    {
        $this->counter = $counter;
    }

    /**
     * @return string
     */
    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    /**
     * @param string $publicKey
     */
    public function setPublicKey(string $publicKey): void
    {
        $this->publicKey = $publicKey;
    }
}
