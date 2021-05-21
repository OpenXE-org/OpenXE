<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\CashPointClosing;

use Xentral\Modules\FiskalyApi\Exception\InvalidArgumentException;

class BusinessCase
{
    /** @var string $type */
    private $type;

    /** @var AmountPerVatIdCollection $amountsPerVatId */
    private $amountsPerVatId;

    /** @var string|null $name */
    private $name;

    /** @var string|null $purchaserAgencyId */
    private $purchaserAgencyId;

    /**
     * BusinessCase constructor.
     *
     * @param string      $type
     * @param AmountPerVatIdCollection $amountsPerVatId
     * @param string|null $name
     * @param string|null $purchaserAgencyId
     */
    public function __construct(
        string $type,
        AmountPerVatIdCollection $amountsPerVatId,
        ?string $name = null,
        ?string $purchaserAgencyId = null
    ) {
        $this->ensureType($type);
        $this->type = $type;
        $this->name = $name;
        $this->purchaserAgencyId = $purchaserAgencyId;
        $this->amountsPerVatId = AmountPerVatIdCollection::fromDbState($amountsPerVatId->toArray());
    }

    /**
     * @param $apiResult
     *
     * @return static
     */
    public static function fromApiResult(object $apiResult): self
    {
        return new self(
            $apiResult->type,
            AmountPerVatIdCollection::fromApiResult($apiResult->amounts_per_vat_id),
            $apiResult->name ?? null,
            $apiResult->purchaser_agency_id ?? null
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
            $dbState['type'],
            AmountPerVatIdCollection::fromDbState($dbState['amounts_per_vat_id']),
            $dbState['name'] ?? null,
            $dbState['purchaser_agency_id'] ?? null
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $dbState = [
            'type' => $this->getType(),
            'amounts_per_vat_id' => $this->amountsPerVatId->toArray(),
        ];
        if($this->name !== null) {
            $dbState['name'] = $this->getName();
        }
        if($this->purchaserAgencyId !== null) {
            $dbState['purchaser_agency_id'] = $this->getPurchaserAgencyId();
        }

        return $dbState;
    }

    /**
     * @return float
     */
    public function getSumInclVat(): float
    {
        return $this->amountsPerVatId->getSumInclVat();
    }

    /**
     * @param string $type
     */
    private function ensureType(string $type): void
    {
        if (
        !in_array(
            $type,
            [
                'Anfangsbestand',
                'Umsatz',
                'Pfand',
                'PfandRueckzahlung',
                'MehrzweckgutscheinKauf',
                'MehrzweckgutscheinEinloesung',
                'EinzweckgutscheinKauf',
                'EinzweckgutscheinEinloesung',
                'Forderungsentstehung',
                'Forderungsaufloesung',
                'Anzahlungseinstellung',
                'Anzahlungsaufloesung',
                'Privateinlage',
                'Privatentnahme',
                'Geldtransit',
                'DifferenzSollIst',
                'TrinkgeldAG',
                'TrinkgeldAN',
                'Auszahlung',
                'Einzahlung',
                'Rabatt',
                'Aufschlag',
                'Lohnzahlung',
                'ZuschussEcht',
                'ZuschussUnecht',
            ]
        )) {
            throw new InvalidArgumentException("invalid type {$type}");
        }
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
        $this->type = $type;
    }

    /**
     * @return AmountPerVatIdCollection
     */
    public function getAmountsPerVatId(): AmountPerVatIdCollection
    {
        return AmountPerVatIdCollection::fromDbState($this->amountsPerVatId->toArray());
    }

    /**
     * @param AmountPerVatIdCollection $amountsPerVatId
     */
    public function setAmountsPerVatId(AmountPerVatIdCollection $amountsPerVatId): void
    {
        $this->amountsPerVatId = AmountPerVatIdCollection::fromDbState($amountsPerVatId->toArray());
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getPurchaserAgencyId(): ?string
    {
        return $this->purchaserAgencyId;
    }

    /**
     * @param string|null $purchaserAgencyId
     */
    public function setPurchaserAgencyId(?string $purchaserAgencyId): void
    {
        $this->purchaserAgencyId = $purchaserAgencyId;
    }
}
