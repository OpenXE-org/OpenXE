<?php

declare(strict_types=1);

namespace Xentral\Modules\CopperSurcharge;

use Xentral\Modules\CopperSurcharge\Data\CopperSurchargeData;
use Xentral\Modules\CopperSurcharge\Service\CopperSurchargeCalculator;
use Xentral\Modules\CopperSurcharge\Service\DocumentGateway;
use Xentral\Modules\CopperSurcharge\Service\PurchasePriceGateway;
use Xentral\Modules\CopperSurcharge\Service\DocumentService;
use Xentral\Modules\CopperSurcharge\Service\RawMaterialGateway;
use Xentral\Modules\CopperSurcharge\Wrapper\DocumentPositionWrapper;
use Xentral\Modules\CopperSurcharge\Wrapper\DocumentPositionWrapperInterface;

final class CopperSurchargeCalculatorFactory
{

    /** @var RawMaterialGateway $rawMaterialGateway */
    private $rawMaterialGateway;

    /** @var PurchasePriceGateway $purchasePriceGateway */
    private $purchasePriceGateway;

    /** @var DocumentPositionWrapper $documentPositionWrapper */
    private $documentPositionWrapper;

    /** @var DocumentService $documentPositionService */
    private $documentService;

    /** @var DocumentGateway $documentGateway */
    private $documentGateway;

    /**
     * @param PurchasePriceGateway             $purchasePriceGateway
     * @param RawMaterialGateway               $rawMaterialGateway
     * @param DocumentPositionWrapperInterface $documentPositionWrapper
     * @param DocumentService                  $documentService
     * @param DocumentGateway                  $documentGateway
     */
    public function __construct(
        PurchasePriceGateway $purchasePriceGateway,
        RawMaterialGateway $rawMaterialGateway,
        DocumentPositionWrapperInterface $documentPositionWrapper,
        DocumentService $documentService,
        DocumentGateway $documentGateway
    ) {
        $this->purchasePriceGateway = $purchasePriceGateway;
        $this->rawMaterialGateway = $rawMaterialGateway;
        $this->documentPositionWrapper = $documentPositionWrapper;
        $this->documentService = $documentService;
        $this->documentGateway = $documentGateway;
    }

    /**
     * @param CopperSurchargeData $configData
     *
     * @return CopperSurchargeCalculator
     */
    public function createCopperSurchargeCalculator(CopperSurchargeData $configData): CopperSurchargeCalculator
    {
        return new CopperSurchargeCalculator(
            $this->purchasePriceGateway,
            $this->rawMaterialGateway,
            $this->documentPositionWrapper,
            $this->documentService,
            $this->documentGateway,
            $configData
        );
    }
}
