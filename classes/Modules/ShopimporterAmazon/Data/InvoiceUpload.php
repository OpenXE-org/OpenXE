<?php

declare(strict_types=1);

namespace Xentral\Modules\ShopimporterAmazon\Data;

use DateTimeInterface;
use DateTime;

class InvoiceUpload
{
    private const SENT_AT_NULL_THRESHOLD = '1970-01-02 00:00:00';

    /** @var int|null $id */
    private $id;

    /** @var int $shopId */
    private $shopId;

    /** @var int $internalOrderId */
    private $internalOrderId;

    /** @var int $invoiceId */
    private $invoiceId;

    /** @var int $creditNoteId */
    private $creditNoteId;

    /** @var string $orderId */
    private $orderId;

    /** @var string $shippingId */
    private $shippingId;

    /** @var string $status */
    private $status;

    /** @var DateTimeInterface|null $createdAt */
    private $createdAt;

    /** @var DateTimeInterface|null $sentAt */
    private $sentAt;

    /** @var string $report */
    private $report;

    /** @var string $marketplace */
    private $marketplace;

    /** @var float $totalAmount */
    private $totalAmount;

    /** @var float $totalVatAmount */
    private $totalVatAmount;

    /** @var string $transactionId */
    private $transactionId;

    /** @var int $countSent */
    private $countSent;

    /** @var int|null $fileId */
    private $fileId;

    /** @var string $invoiceNumber */
    private $invoiceNumber;

    /** @var string $errorCode */
    private $errorCode;

    /** @var string $errorMessage */
    private $errorMessage;

    /**
     * InvoiceUpload constructor.
     *
     * @param int                    $shopId
     * @param int                    $internalOrderId
     * @param string                 $orderId
     * @param string                 $shippingId
     * @param string                 $transactionId
     * @param string                 $marketplace
     * @param string                 $invoiceNumber
     * @param float                  $totalAmount
     * @param float                  $totalVatAmount
     * @param int                    $invoiceId
     * @param int                    $creditNoteId
     * @param DateTimeInterface|null $createdAt
     * @param string                 $status
     * @param string                 $report
     * @param string                 $errorCode
     * @param string                 $errorMessage
     * @param int                    $fileId
     * @param int                    $countSent
     * @param DateTimeInterface|null $sentAt
     * @param int|null               $id
     */
    public function __construct(
        int $shopId,
        int $internalOrderId,
        string $orderId,
        string $shippingId,
        string $transactionId,
        string $marketplace,
        string $invoiceNumber,
        float $totalAmount,
        float $totalVatAmount,
        int $invoiceId,
        int $creditNoteId = 0,
        ?DateTimeInterface $createdAt = null,
        string $status = '',
        string $report = '',
        string $errorCode = '',
        string $errorMessage = '',
        int $fileId = 0,
        int $countSent = 0,
        ?DateTimeInterface $sentAt = null,
        ?int $id = null
    ) {
        $this->shopId = $shopId;
        $this->internalOrderId = $internalOrderId;
        $this->orderId = $orderId;
        $this->shippingId = $shippingId;
        $this->transactionId = $transactionId;
        $this->marketplace = $marketplace;
        $this->invoiceNumber = $invoiceNumber;
        $this->totalAmount = $totalAmount;
        $this->totalVatAmount = $totalVatAmount;
        $this->invoiceId = $invoiceId;
        $this->creditNoteId = $creditNoteId;
        $this->status = $status;
        $this->report = $report;
        $this->errorCode = $errorCode;
        $this->errorMessage = $errorMessage;
        $this->fileId = $fileId;
        $this->countSent = $countSent;
        $this->id = $id;
        $this->createdAt = $createdAt;
        $this->setSentAt($sentAt);
    }

    /**
     * @param array $dbState
     *
     * @return static
     */
    public static function fromDbState(array $dbState): self
    {
        $createdAt = $dbState['created_at'] === '0000-00-00 00:00:00' || $dbState['created_at'] === null
            ? '' : $dbState['created_at'];
        $sentAt = $dbState['sent_at'] === '0000-00-00 00:00:00' || $dbState['sent_at'] === null
            ? '' : $dbState['sent_at'];

        return new self(
            (int)$dbState['shop_id'],
            (int)$dbState['int_order_id'],
            (string)$dbState['orderid'],
            (string)$dbState['shippingid'],
            (string)$dbState['transaction_id'],
            (string)$dbState['marketplace'],
            (string)$dbState['invoice_number'],
            (float)$dbState['total_amount'],
            (float)$dbState['total_vat_amount'],
            (int)$dbState['invoice_id'],
            (int)$dbState['credit_note_id'],
            DateTime::createFromFormat('Y-m-d H:i:s', $createdAt) ?: null,
            (string)$dbState['status'],
            (string)$dbState['report'],
            (string)$dbState['error_code'],
            (string)$dbState['error_message'],
            (int)$dbState['file_id'],
            (int)$dbState['count_sent'],
            DateTime::createFromFormat('Y-m-d H:i:s', $sentAt) ?: null,
            empty($dbState['id']) ? null : (int)$dbState['id']
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id'               => $this->id,
            'shop_id'          => $this->shopId,
            'int_order_id'     => $this->internalOrderId,
            'invoice_id'       => $this->invoiceId,
            'file_id'          => $this->fileId,
            'orderid'          => $this->orderId,
            'shippingid'       => $this->shippingId,
            'created_at'       => $this->createdAt === null ? null : $this->createdAt->format('Y-m-d H:i:s'),
            'sent_at'          => $this->sentAt === null ? null : $this->sentAt->format('Y-m-d H:i:s'),
            'report'           => $this->report,
            'marketplace'      => $this->marketplace,
            'status'           => $this->status,
            'error_code'       => $this->errorCode,
            'error_message'    => $this->errorMessage,
            'invoice_number'   => $this->invoiceNumber,
            'total_amount'     => $this->totalAmount,
            'total_vat_amount' => $this->totalVatAmount,
            'credit_note_id'   => $this->creditNoteId,
            'transaction_id'   => $this->transactionId,
            'count_sent'       => $this->countSent,
        ];
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     *
     * @return self
     */
    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getShopId(): int
    {
        return $this->shopId;
    }

    /**
     * @param int $shopId
     *
     * @return self
     */
    public function setShopId(int $shopId): self
    {
        $this->shopId = $shopId;

        return $this;
    }

    /**
     * id of table auftrag
     *
     * @return int
     */
    public function getInternalOrderId(): int
    {
        return $this->internalOrderId;
    }

    /**
     * @param int $internalOrderId
     *
     * @return self
     */
    public function setInternalOrderId(int $internalOrderId): self
    {
        $this->internalOrderId = $internalOrderId;

        return $this;
    }

    /**
     * @return int
     */
    public function getInvoiceId(): int
    {
        return $this->invoiceId;
    }

    /**
     * @param int $invoiceId
     *
     * @return self
     */
    public function setInvoiceId(int $invoiceId): self
    {
        $this->invoiceId = $invoiceId;

        return $this;
    }

    /**
     * @return int
     */
    public function getCreditNoteId(): int
    {
        return $this->creditNoteId;
    }

    /**
     * @param int $creditNoteId
     *
     * @return self
     */
    public function setCreditNoteId(int $creditNoteId): self
    {
        $this->creditNoteId = $creditNoteId;

        return $this;
    }

    /**
     * column internet in table auftrag (order-number from Amazon)"
     *
     * @return string
     */
    public function getExternalOrderId(): string
    {
        return $this->orderId;
    }

    /**
     * @param string $orderId
     *
     * @return self
     */
    public function setExternalOrderId(string $orderId): self
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * @return string
     */
    public function getShippingId(): string
    {
        return $this->shippingId;
    }

    /**
     * @param string $shippingId
     *
     * @return self
     */
    public function setShippingId(string $shippingId): self
    {
        $this->shippingId = $shippingId;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     *
     * @return self
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @param DateTimeInterface|null $createdAt
     *
     * @return self
     */
    public function setCreatedAt(?DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getSentAt(): ?DateTimeInterface
    {
        return $this->sentAt;
    }

    /**
     * @param DateTimeInterface|null $sentAt
     *
     * @return self
     */
    public function setSentAt(?DateTimeInterface $sentAt): self
    {
        if ($sentAt === null || $sentAt <= new DateTime(self::SENT_AT_NULL_THRESHOLD)) {
            $this->sentAt = null;

            return $this;
        }
        $this->sentAt = $sentAt;

        return $this;
    }

    /**
     * @return string
     */
    public function getReport(): string
    {
        return $this->report;
    }

    /**
     * @param string $report
     *
     * @return self
     */
    public function setReport(string $report): self
    {
        $this->report = $report;

        return $this;
    }

    /**
     * @return string
     */
    public function getMarketplace(): string
    {
        return $this->marketplace;
    }

    /**
     * @param string $marketplace
     *
     * @return self
     */
    public function setMarketplace(string $marketplace): self
    {
        $this->marketplace = $marketplace;

        return $this;
    }

    /**
     * @return float
     */
    public function getTotalAmount(): float
    {
        return $this->totalAmount;
    }

    /**
     * @param float $totalAmount
     *
     * @return self
     */
    public function setTotalAmount(float $totalAmount): self
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }

    /**
     * @return float
     */
    public function getTotalVatAmount(): float
    {
        return $this->totalVatAmount;
    }

    /**
     * @param float $totalVatAmount
     *
     * @return self
     */
    public function setTotalVatAmount(float $totalVatAmount): self
    {
        $this->totalVatAmount = $totalVatAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getTransactionId(): string
    {
        return $this->transactionId;
    }

    /**
     * @param string $transactionId
     *
     * @return self
     */
    public function setTransactionId(string $transactionId): self
    {
        $this->transactionId = $transactionId;

        return $this;
    }

    /**
     * @return int
     */
    public function getCountSent(): int
    {
        return $this->countSent;
    }

    /**
     * @param int $countSent
     *
     * @return self
     */
    public function setCountSent(int $countSent): self
    {
        $this->countSent = $countSent;

        return $this;
    }

    /**
     * @param int $incrementation
     *
     * @return $this
     */
    public function increaseCountSent(int $incrementation = 1): self
    {
        $this->countSent += $incrementation;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getFileId(): ?int
    {
        return $this->fileId;
    }

    /**
     * @param int|null $fileId
     *
     * @return self
     */
    public function setFileId(?int $fileId): self
    {
        $this->fileId = $fileId;

        return $this;
    }

    /**
     * @return string
     */
    public function getInvoiceNumber(): string
    {
        return $this->invoiceNumber;
    }

    /**
     * @param string $invoiceNumber
     *
     * @return self
     */
    public function setInvoiceNumber(string $invoiceNumber): self
    {
        $this->invoiceNumber = $invoiceNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    /**
     * @param string $errorCode
     *
     * @return self
     */
    public function setErrorCode(string $errorCode): self
    {
        $this->errorCode = $errorCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    /**
     * @param string $errorMessage
     *
     * @return self
     */
    public function setErrorMessage(string $errorMessage): self
    {
        $this->errorMessage = $errorMessage;

        return $this;
    }
}
