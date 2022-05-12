<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\CashPointClosing;

use Xentral\Modules\FiskalyApi\Exception\InvalidArgumentException;

class CashPointClosingTransactionBuyer
{
    /** @var string $name */
    private $name;

    /** @var string $buyerExportId */
    private $buyerExportId;

    /** @var $type */
    private $type;

    /** @var CashPointClosingTransactionAddress|null $address */
    private $address;

    /** @var string|null $vatIdNumber */
    private $vatIdNumber;

    /**
     * CashPointClosingTransactionBuyer constructor.
     *
     * @param string                                  $name
     * @param string                                  $buyerExportId
     * @param string                                  $type
     * @param CashPointClosingTransactionAddress|null $address
     * @param string|null                             $vatIdNumber
     */
    public function __construct(
        string $name,
        string $buyerExportId,
        string $type,
        ?CashPointClosingTransactionAddress $address = null,
        ?string $vatIdNumber = null
    ) {
        $this->setName($name);
        $this->setBuyerExportId($buyerExportId);
        $this->setType($type);
        $this->setAddress($address);
        $this->setVatIdNumber($vatIdNumber);
    }

    /**
     * @param $apiResult
     *
     * @return static
     */
    public static function fromApiResult(object $apiResult): self
    {
        return new self(
            $apiResult->name,
            $apiResult->buyer_export_id,
            $apiResult->type,
            empty($apiResult->address) ? null : CashPointClosingTransactionAddress::fromApiResult($apiResult->address),
            $apiResult->vat_id_number ?? null
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
            $dbState['name'],
            $dbState['buyer_export_id'],
            $dbState['type'],
            empty($dbState['address']) ? null : CashPointClosingTransactionAddress::fromDbState($dbState['address']),
            $dbState['vat_id_number'] ?? null
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $dbState = [
            'name'            => $this->getName(),
            'buyer_export_id' => $this->getBuyerExportId(),
            'type'            => $this->getType(),
        ];
        if ($this->address !== null) {
            $dbState['address'] = $this->address->toArray();
        }
        if ($this->vatIdNumber !== null) {
            $dbState['vat_id_number'] = $this->getVatIdNumber();
        }

        return $dbState;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = mb_substr($name, 0, 50);
    }

    /**
     * @return string
     */
    public function getBuyerExportId(): string
    {
        return $this->buyerExportId;
    }

    /**
     * @param string $buyerExportId
     */
    public function setBuyerExportId(string $buyerExportId): void
    {
        $this->buyerExportId = mb_substr($buyerExportId, 0, 50);
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
     * @return CashPointClosingTransactionAddress|null
     */
    public function getAddress(): ?CashPointClosingTransactionAddress
    {
        return $this->address === null ? null : CashPointClosingTransactionAddress::fromDbState(
            $this->address->toArray()
        );
    }

    /**
     * @param CashPointClosingTransactionAddress|null $address
     */
    public function setAddress(?CashPointClosingTransactionAddress $address): void
    {
        $this->address = $address === null ? null : CashPointClosingTransactionAddress::fromDbState(
            $address->toArray()
        );
    }

    /**
     * @return string|null
     */
    public function getVatIdNumber(): ?string
    {
        return $this->vatIdNumber;
    }

    /**
     * @param string|null $vatIdNumber
     */
    public function setVatIdNumber(?string $vatIdNumber): void
    {
        $vatIdNumber = $this->ensureVatIdNumber($vatIdNumber);
        $this->vatIdNumber = $vatIdNumber;
    }

    /**
     * @param string $type
     */
    private function ensureType(string $type): void
    {
        if (!in_array($type, ['Kunde', 'Mitarbeiter'])) {
            throw new InvalidArgumentException("'{$type}' is a invalid User Type");
        }
    }

    /**
     * @param string|null $vatIdNumber
     *
     * @return string|null
     */
    private function ensureVatIdNumber(?string $vatIdNumber): ?string
    {
        if ($vatIdNumber === null) {
            return null;
        }
        $vatIdNumber = trim($vatIdNumber);
        if (!preg_match('/^[A-Z]{2}.{1,13}$/', $vatIdNumber)) {
            throw new InvalidArgumentException("'{$vatIdNumber}' is an invalid VatIdNumber");
        }

        return $vatIdNumber;
    }
}
