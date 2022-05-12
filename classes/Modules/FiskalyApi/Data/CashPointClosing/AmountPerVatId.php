<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\CashPointClosing;

use Xentral\Modules\FiskalyApi\Exception\InvalidArgumentException;

class AmountPerVatId
{
    /** @var int $vatDefinitionExportId */
    private $vatDefinitionExportId;

    /** @var float|null $inclVat */
    private $inclVat;

    /** @var float|null $exclVat */
    private $exclVat;

    /** @var float|null $vat */
    private $vat;

    /**
     * AmountPerVatId constructor.
     *
     * @param int        $vatDefinitionExportId
     * @param float|null $inclVat
     * @param float|null $exclVat
     * @param float|null $vat
     */
    public function __construct(int $vatDefinitionExportId, ?float $inclVat, ?float $exclVat = null, ?float $vat = null)
    {
        $this->setVatDefinitionExportId($vatDefinitionExportId);
        $this->setAmounts($inclVat, $exclVat, $vat);
    }

    /**
     * @param $apiResult
     *
     * @return static
     */
    public static function fromApiResult(object $apiResult): self
    {
        return new self(
            (int)$apiResult->vat_definition_export_id,
            $apiResult->incl_vat === null ? null : (float)$apiResult->incl_vat,
            $apiResult->excl_vat === null ? null : (float)$apiResult->excl_vat,
            $apiResult->vat === null ? null : (float)$apiResult->vat
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
            (int)$dbState['vat_definition_export_id'],
            $dbState['incl_vat'] === null ? null : (float)$dbState['incl_vat'],
            $dbState['excl_vat'] === null ? null : (float)$dbState['excl_vat'],
            $dbState['vat'] === null ? null : (float)$dbState['vat']
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'vat_definition_export_id' => $this->getVatDefinitionExportId(),
            'incl_vat'                 => $this->getInclVat(),
            'excl_vat'                 => $this->getExclVat(),
            'vat'                      => $this->getVat(),
        ];
    }

    /**
     * @return int
     */
    public function getVatDefinitionExportId(): int
    {
        return $this->vatDefinitionExportId;
    }

    /**
     * @param int $vatDefinitionExportId
     */
    public function setVatDefinitionExportId(int $vatDefinitionExportId): void
    {
        if ($vatDefinitionExportId <= 0 || $vatDefinitionExportId > 9999999999) {
            throw new InvalidArgumentException(
                "{$vatDefinitionExportId} is an invalid vat_definition_export_id. [1 .. 9999999999]"
            );
        }
        if ($vatDefinitionExportId >= 8 && $vatDefinitionExportId > 999) {
            throw new InvalidArgumentException(
                "{$vatDefinitionExportId} is an invalid vat_definition_export_id. [8 - 999] are reserved"
            );
        }
        $this->vatDefinitionExportId = $vatDefinitionExportId;
    }

    /**
     * @return float
     */
    public function getInclVat(): float
    {
        return (float)number_format($this->inclVat, 5, '.', '');
    }

    /**
     * @param float|null $inclVat
     * @param float|null $exclVat
     * @param float|null $vat
     */
    public function setAmounts(?float $inclVat, ?float $exclVat, ?float $vat = null): void
    {
        $isInclVatNull = $inclVat === null;
        $isExclVatNull = $exclVat === null;
        $isVatNull = $vat === null;
        if (!$isInclVatNull) {
            $inclVat = (float)number_format(round($inclVat, 5), 5, '.', '');
        }
        if (!$isExclVatNull) {
            $exclVat = (float)number_format(round($exclVat, 5), 5, '.', '');
        }
        if (!$isVatNull) {
            $vat = (float)number_format(round($vat, 5), 5, '.', '');
        }
        if ($isInclVatNull) {
            if ($isExclVatNull || $isVatNull) {
                throw new InvalidArgumentException("VatInfos: two or three Values must not be null");
            }
            $inclVat = (float)number_format(round($vat + $exclVat, 5), 5, '.', '');
        } elseif ($isExclVatNull) {
            if ($isVatNull) {
                throw new InvalidArgumentException("VatInfos: two or three Values must not be null");
            }
            $exclVat = (float)number_format(round($inclVat - $vat, 5), 5, '.', '');
        } elseif ($isVatNull) {
            $vat = (float)number_format(round($inclVat - $exclVat, 5), 5, '.', '');
        } elseif (round($inclVat, 5) !== round(round($exclVat, 5) + round($vat, 5), 5)) {
            throw new InvalidArgumentException("VatInfos: {$inclVat} is not {$exclVat} + {$vat}");
        }

        if (
            ($inclVat > 0 && $exclVat <= 0)
            || ($inclVat > 0 && $exclVat > $inclVat)
            || ($inclVat < 0 && $exclVat >= 0)
            || ($inclVat < 0 && $exclVat < $inclVat)
            || ($inclVat === 0 && $exclVat !== 0)
        ) {
            throw new InvalidArgumentException("inclVat '{$inclVat}' is not matching to exclVat {$exclVat}");
        }
        $this->inclVat = $inclVat;
        $this->exclVat = $exclVat;
        $this->vat = $vat;
    }

    /**
     * @return float|null
     */
    public function getExclVat(): ?float
    {
        return (float)number_format($this->exclVat, 5, '.', '');
    }

    /**
     * @return float|null
     */
    public function getVat(): ?float
    {
        return (float)number_format($this->vat, 5, '.', '');
    }
}
