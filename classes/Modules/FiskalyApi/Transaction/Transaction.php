<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Transaction;

use Xentral\Modules\FiskalyApi\Transaction\Payment\BasePayment;
use Xentral\Modules\FiskalyApi\Transaction\Payment\OrderLineItem;
use Xentral\Modules\FiskalyApi\Transaction\VatAmount\BaseVatAmount;
use Xentral\Modules\FiskalyApi\UuidTool;

/**
 * Class Transaction
 *
 * @package Xentral\Modules\FiskalyApi\Transaction
 */
class Transaction
{
    /** @var array */
    private $amountsPerVatRate;

    /** @var BasePayment[] */
    private $amountsPerPaymentType;

    private $oderLineItems;

    /** @var string */
    private $uuid;

    /** @var string */
    private $clientUuid;

    /** @var int */
    private $lastRevision = -1;

    /** @var int */
    private $startTime;

    /** @var int */
    private $endTime;

    /** @var string */
    private $clientSerialNumber;

    /** @var string */
    private $certificateSerial;

    // TODO Signature object

    /** @var string */
    private $signature;

    /** @var string */
    private $publicKey;

    /** @var string */
    private $signatureAlgorithm;

    /** @var int */
    private $signatureCounter;

    /** @var string $qrCodeData */
    private $qrCodeData;

    /**
     * @return int
     */
    public function getTransactionNumber(): int
    {
        return $this->transactionNumber;
    }

    /**
     * @param int $transactionNumber
     */
    public function setTransactionNumber(int $transactionNumber): void
    {
        $this->transactionNumber = $transactionNumber;
    }

    /** @var int */
    private $transactionNumber;

    /**
     * Transaction constructor.
     *
     * @param array $amountsPerPaymentType
     * @param array $amountsPerVatRate
     * @param OrderLineItem[] $orderLineItems
     * @param string $clientId
     * @param string|null $uuid
     */
    public function __construct(
        array $amountsPerPaymentType,
        array $amountsPerVatRate,
        array $orderLineItems,
        string $clientId,
        string $uuid = null
    ) {
        $this->amountsPerPaymentType = $amountsPerPaymentType;
        $this->amountsPerVatRate = $amountsPerVatRate;
        $this->oderLineItems = $orderLineItems;
        $this->uuid = $uuid;
        $this->clientUuid = $clientId;
        if (empty($this->uuid)) {
            $this->uuid = UuidTool::generateUuid();
        }
    }

    /**
     * @return BaseVatAmount[]
     */
    public function getAmountsPerVatRate(): array
    {
        return $this->amountsPerVatRate;
    }

    /**
     * @return BasePayment[]
     */
    public function getAmountsPerPaymentType(): array
    {
        return $this->amountsPerPaymentType;
    }

    /**
     * @return OrderLineItem[]
     */
    public function getOrderLineItems(): array
    {
        return $this->oderLineItems;
    }

    /* @return string */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @return int
     */
    public function getLastRevision(): int
    {
        return $this->lastRevision;
    }

    /**
     * @return int
     */
    public function getStartTime(): int
    {
        return $this->startTime;
    }

    /**
     * @param int $startTime
     */
    public function setStartTime(int $startTime): void
    {
        $this->startTime = $startTime;
    }

    /**
     * @return int
     */
    public function getEndTime(): int
    {
        return $this->endTime;
    }

    /**
     * @param int $endTime
     */
    public function setEndTime(int $endTime): void
    {
        $this->endTime = $endTime;
    }

    /**
     * @return string
     */
    public function getClientSerialNumber(): string
    {
        return $this->clientSerialNumber;
    }

    /**
     * @param string $clientSerialNumber
     */
    public function setClientSerialNumber(string $clientSerialNumber): void
    {
        $this->clientSerialNumber = $clientSerialNumber;
    }

    /**
     * @return string
     */
    public function getCertificateSerial(): string
    {
        return $this->certificateSerial;
    }

    /**
     * @param string $certificateSerial
     */
    public function setCertificateSerial(string $certificateSerial): void
    {
        $this->certificateSerial = $certificateSerial;
    }

    /**
     * @return string
     */
    public function getSignature(): string
    {
        return $this->signature;
    }

    /**
     * @param string $signature
     */
    public function setSignature(string $signature): void
    {
        $this->signature = $signature;
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

    /**
     * @return string
     */
    public function getSignatureAlgorithm(): string
    {
        return $this->signatureAlgorithm;
    }

    /**
     * @param string $signatureAlgorithm
     */
    public function setSignatureAlgorithm(string $signatureAlgorithm): void
    {
        $this->signatureAlgorithm = $signatureAlgorithm;
    }

    /**
     * @return int
     */
    public function getSignatureCounter(): int
    {
        return $this->signatureCounter;
    }

    /**
     * @param int $signatureCounter
     */
    public function setSignatureCounter(int $signatureCounter): void
    {
        $this->signatureCounter = $signatureCounter;
    }



    /**
     * @return string
     */
    public function getClientUuid(): string
    {
        return $this->clientUuid;
    }

    /**
     * @return bool
     */
    public function isLastRevisionSet(): bool
    {
        return $this->lastRevision > -1;
    }

    /**
     * @param int $lastRevision
     */
    public function setLastRevision(int $lastRevision): void
    {
        $this->lastRevision = $lastRevision;
    }
}
