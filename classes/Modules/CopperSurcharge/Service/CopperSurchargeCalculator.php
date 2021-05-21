<?php

declare(strict_types=1);

namespace Xentral\Modules\CopperSurcharge\Service;

use DateTimeImmutable;
use DateTimeInterface;
use Xentral\Modules\CopperSurcharge\Data\CopperSurchargeData;
use Xentral\Modules\CopperSurcharge\Data\DocumentPositionData;
use Xentral\Modules\CopperSurcharge\Exception\InvalidDateFormatException;
use Xentral\Modules\CopperSurcharge\Wrapper\DocumentPositionWrapper;
use Xentral\Modules\CopperSurcharge\Wrapper\DocumentPositionWrapperInterface;

final class CopperSurchargeCalculator
{
    /** @var RawMaterialGateway $rawMaterialGateway */
    private $rawMaterialGateway;

    /** @var PurchasePriceGateway $purchasePriceGateway */
    private $purchasePriceGateway;

    /** @var DocumentPositionWrapper $documentPositionWrapper */
    private $documentPositionWrapper;

    /** @var CopperSurchargeData $config */
    private $config;

    /** @var DocumentService $documentService */
    private $documentService;

    /** @var DocumentGateway $documentGateway */
    private $documentGateway;

    /**
     * @param PurchasePriceGateway             $purchasePriceGateway
     * @param RawMaterialGateway               $rawMaterialGateway
     * @param DocumentPositionWrapperInterface $documentPositionWrapper
     * @param DocumentService                  $documentService
     * @param DocumentGateway                  $documentGateway
     * @param CopperSurchargeData              $copperSurchargeConfig
     */
    public function __construct(
        PurchasePriceGateway $purchasePriceGateway,
        RawMaterialGateway $rawMaterialGateway,
        DocumentPositionWrapperInterface $documentPositionWrapper,
        DocumentService $documentService,
        DocumentGateway $documentGateway,
        CopperSurchargeData $copperSurchargeConfig
    ) {
        $this->purchasePriceGateway = $purchasePriceGateway;
        $this->rawMaterialGateway = $rawMaterialGateway;
        $this->documentPositionWrapper = $documentPositionWrapper;
        $this->documentService = $documentService;
        $this->config = $copperSurchargeConfig;
        $this->documentGateway = $documentGateway;
    }

    /**
     * @param string                       $docType
     * @param int                          $docId
     * @param array|DocumentPositionData[] $possibleCopperPositions
     * @param array                        $copperPositionsInPartsList
     *
     * @return int
     */
    public function handleCopperSurchargePositions(
        string $docType,
        int $docId,
        array $possibleCopperPositions,
        array $copperPositionsInPartsList
    ): int {
        $calcDate = $this->evaluateCalcDate($docType, $docId);

        if ($this->config->getSurchargePositionType() === CopperSurchargeData::POSITION_TYPE_ALWAYS) {
            $this->createManyPositions(
                $docType,
                $docId,
                $possibleCopperPositions,
                $copperPositionsInPartsList,
                $calcDate
            );

            return 0;
        } elseif ($this->config->getSurchargePositionType() === CopperSurchargeData::POSITION_TYPE_ONETIME) {
            $this->createSinglePosition(
                $docType,
                $docId,
                $possibleCopperPositions,
                $copperPositionsInPartsList,
                $calcDate
            );

            return 1;
        } else {
            $this->createGroupPositions($docType, $docId, $copperPositionsInPartsList, $calcDate);

            return 2;
        }
    }

    /**
     * Because there is no connection between positions,
     * all surcharge positions get deleted and recreated in the next step
     *
     * @param string $docType
     * @param int    $docId
     */
    public function resetDocument(string $docType, int $docId): void
    {
        $this->resetBetweenPositions($docId, $docType, $this->config->getCopperSurchargeArticleId());
        $this->documentService->deleteCopperSurchargePositions(
            $docType,
            $docId,
            $this->config->getCopperSurchargeArticleId()
        );
        $this->documentService->updatePositionSorts($docType, $docId);
    }

    /**
     * @param string                       $docType
     * @param int                          $docId
     * @param array|DocumentPositionData[] $copperPositions
     * @param array                        $copperPositionsInPartsList
     * @param DateTimeInterface            $calcDate
     *
     */
    private function createManyPositions(
        string $docType,
        int $docId,
        array $copperPositions,
        array $copperPositionsInPartsList,
        DateTimeInterface $calcDate
    ): void {
        foreach ($copperPositions as $position) {
            $amount = $this->calcAmount($docType, $position->getPositionId(), $position->getArticleId());
            $copperBase = $this->getCopperBase($position->getArticleId());

            $price = $this->calculateCopperSurchargePrice(
                $copperBase,
                $amount,
                $calcDate
            );

            $newPosId = $this->addCopperSurchargePosition(
                $docType,
                $docId,
                $price,
                $amount,
                $copperBase,
                $position->getCurrency(),
                $calcDate
            );

            $this->documentService->updatePositionSort($docType, $docId, $position->getPositionId(), $newPosId);
        }

        if (empty($copperPositionsInPartsList)) {
            return;
        }
        foreach ($copperPositionsInPartsList as $partListPosition) {
            $partListData = $this->evaluateSurchargeDataForPartList(
                $partListPosition['article_id'],
                $partListPosition['pos_id'],
                $calcDate,
                $docType,
                $partListPosition['amount']
            );

            $newPosId = $this->addCopperSurchargePosition(
                $docType,
                $docId,
                $partListData['price'],
                $partListData['amount'],
                $partListData['copper_base'],
                $partListPosition['currency'],
                $calcDate
            );

            $this->documentService->updatePositionSort($docType, $docId, $partListData['position_id'], $newPosId);
        }
    }

    /**
     * @param int               $partListHeadId
     * @param int               $positionId
     * @param DateTimeInterface $calcDate
     * @param string            $docType
     * @param float             $partListPositionAmount
     *
     * @return array
     */
    private function evaluateSurchargeDataForPartList(
        int $partListHeadId,
        int $positionId,
        DateTimeInterface $calcDate,
        string $docType,
        float $partListPositionAmount
    ): array {
        $copperArticles =
            $this->getCopperArticlesFromPartList(
                $partListHeadId,
                $this->config->getSurchargeMaintenanceType(),
                $this->config->getCopperNumberOption(),
                $this->config->getCopperSurchargeArticleId()
            );
        $amount = 0;
        $price = 0.0;
        $copperBase = 0.0;

        foreach ($copperArticles as $copperArticle) {
            $amount += $copperArticle['amount'];
            $copperBase = $this->getCopperBase($copperArticle['article_id']);

            $price += $this->calculateCopperSurchargePrice(
                $copperBase,
                $amount,
                $calcDate
            );
        }

        return [
            'amount'      => $amount * $partListPositionAmount,
            'price'       => $price * $partListPositionAmount,
            'copper_base' => $copperBase,
            'position_id' => $this->documentGateway->evaluatePartListLastPositionId($docType, $positionId),
        ];
    }

    /**
     * @param string            $docType
     * @param int               $docId
     * @param float             $price
     * @param float             $amount
     * @param float             $copperBase
     * @param                   $currency
     * @param DateTimeInterface $calcDate
     *
     * @return int
     */
    private function addCopperSurchargePosition(
        string $docType,
        int $docId,
        float $price,
        float $amount,
        float $copperBase,
        $currency,
        DateTimeInterface $calcDate
    ): int {
        $copperSurchargeArticleId = $this->config->getCopperSurchargeArticleId();
        $articleData = $this->documentGateway->getArticleData($copperSurchargeArticleId);
        $description = $this->findCopperSurchargeArticleDescription(
            $amount,
            $copperBase,
            $price,
            $articleData,
            $calcDate
        );

        return $this->documentPositionWrapper->addPositionManuallyWithPrice(
            $docType,
            $docId,
            $copperSurchargeArticleId,
            $articleData,
            1,
            $price,
            $currency,
            $description
        );
    }

    /**
     * @param float             $amount
     * @param float             $copperBase
     * @param float             $price
     * @param array             $articleData
     * @param DateTimeInterface $calcDate
     *
     * @return string
     */
    private function findCopperSurchargeArticleDescription(
        float $amount,
        float $copperBase,
        float $price,
        array $articleData,
        DateTimeInterface $calcDate
    ): string {
        $description = $articleData['description'];

        $delPrice = $this->purchasePriceGateway->getDelCopperPriceByDate(
            $calcDate,
            $this->config->getCopperSurchargeArticleId()
        );

        $price = number_format($price, 2, ",", ".");
        $delPrice = number_format($delPrice, 2, ",", ".");
        $copperBase = number_format($copperBase, 2, ",", ".");
        $amount = str_replace('.', ',', $amount);

        $description = str_replace('{NETPRICE}', $price, $description);
        $description = str_replace('{ARTIKELNUMMER}', $articleData['number'], $description);
        $description = str_replace('{ARTIKELNAME}', $articleData['name_de'], $description);
        $description = str_replace('{COPPERBASIS}', $copperBase, $description);
        $description = str_replace('{COPPERNUMBER}', $amount, $description);
        $description = str_replace('{DELVALUE}', $delPrice, $description);

        return $description;
    }


    /**
     * @param string                       $docType
     * @param int                          $docId
     * @param array|DocumentPositionData[] $copperPositions
     * @param array                        $copperPositionsInPartsList
     * @param DateTimeInterface            $calcDate
     *
     * @return int
     */
    private function createSinglePosition(
        string $docType,
        int $docId,
        array $copperPositions,
        array $copperPositionsInPartsList,
        DateTimeInterface $calcDate
    ): int {
        $price = 0.0;
        $totalAmount = 0.0;
        $currency = 'EUR';
        $copperBase = 0.0;
        foreach ($copperPositions as $position) {
            $currency = $position->getCurrency();
            $amount = $this->calcAmount($docType, $position->getPositionId(), $position->getArticleId());
            $totalAmount += $amount;
            $copperBase = $this->getCopperBase($position->getArticleId());
            $price += $this->calculateCopperSurchargePrice(
                $copperBase,
                $amount,
                $calcDate
            );
        }

        if (!empty($copperPositionsInPartsList)) {
            foreach ($copperPositionsInPartsList as $position) {
                $partListData = $this->evaluateSurchargeDataForPartList(
                    $position['article_id'],
                    $position['pos_id'],
                    $calcDate,
                    $docType,
                    $position['amount']
                );

                $totalAmount += $partListData['amount'];
                $price += $partListData['price'];
                $copperBase = $partListData['copper_base'];
            }
        }

        $newPosId = 0;
        if ($price > 0) {
            $newPosId = $this->addCopperSurchargePosition(
                $docType,
                $docId,
                $price,
                $totalAmount,
                $copperBase,
                $currency,
                $calcDate
            );
        }

        return $newPosId;
    }

    /**
     * @param string            $docType
     * @param int               $docId
     * @param array             $copperPositionsInPartsList
     * @param DateTimeInterface $calcDate
     */
    private function createGroupPositions(
        string $docType,
        int $docId,
        array $copperPositionsInPartsList,
        DateTimeInterface $calcDate
    ): void {
        if ($this->config->getSurchargeMaintenanceType() === CopperSurchargeData::SURCHARGE_MAINTENANCE_TYPE_APP) {
            $positions = $this->rawMaterialGateway->findAllPositionsForGrouped(
                $docType,
                $docId,
                $this->config->getCopperSurchargeArticleId()
            );
        } else {
            $positions = $this->documentGateway->findAllPositionsForGrouped(
                $docType,
                $docId,
                $this->config->getCopperNumberOption()
            );
        }

        $price = 0.0;
        $totalAmount = 0.0;
        $currency = 'EUR';
        $prev = null;
        $copperBase = 0.0;
        $lastSort = 0;

        foreach ($positions as $key => $position) {
            if ((bool)$position['is_copper']) {
                $amount = $this->calcAmount($docType, (int)$position['pos_id'], (int)$position['article_id']);
                $copperBase = $this->getCopperBase($position['article_id']);
                $price += $this->calculateCopperSurchargePrice(
                    $copperBase,
                    $amount,
                    $calcDate
                );
                $currency = $position['currency'];
                $totalAmount += $amount;
            }

            if ($position['between_type'] === 'gruppe') {
                if (!empty($copperPositionsInPartsList)) {
                    $partListElements = $this->getElementsFromPartListBetweenSorts(
                        $copperPositionsInPartsList,
                        $lastSort,
                        $position['sort']
                    );

                    foreach ($partListElements as $partListElement) {
                        $partListKey = $partListElement['part_list_key'];
                        $partListData = $this->evaluateSurchargeDataForPartList(
                            $partListElement['article_id'],
                            $position['pos_id'],
                            $calcDate,
                            $docType,
                            $partListElement['amount']
                        );
                        $totalAmount += $partListData['amount'];
                        $price += $partListData['price'];
                        $copperBase = $partListData['copper_base'];
                        unset($copperPositionsInPartsList[$partListKey]);
                    }
                }
                if ($price > 0.0) {
                    $newPosId = $this->addCopperSurchargePosition(
                        $docType,
                        $docId,
                        $price,
                        $totalAmount,
                        $copperBase,
                        $currency,
                        $calcDate
                    );
                    if (!empty($prev)) {
                        $this->documentService->updatePositionSort($docType, $docId, (int)$prev['pos_id'], $newPosId);
                    }
                    $price = 0.0;
                    $totalAmount = 0.0;
                    $lastSort = $position['sort'];
                }
            }

            $prev = $position;
        }

        if (!empty($copperPositionsInPartsList)) {
            foreach ($copperPositionsInPartsList as $partListElement) {
                $partListData = $this->evaluateSurchargeDataForPartList(
                    $partListElement['article_id'],
                    $partListElement['pos_id'],
                    $calcDate,
                    $docType,
                    $partListElement['amount']
                );
                $totalAmount += $partListData['amount'];
                $price += $partListData['price'];
                $copperBase = $partListData['copper_base'];
            }
        }
        if ($price > 0.0) {
            $this->addCopperSurchargePosition(
                $docType,
                $docId,
                $price,
                $totalAmount,
                $copperBase,
                $currency,
                $calcDate
            );
        }
    }

    /**
     * @param float             $copperBase
     * @param float             $amount
     * @param DateTimeInterface $calcDate
     *
     * @return float
     */
    private function calculateCopperSurchargePrice(
        float $copperBase,
        float $amount,
        DateTimeInterface $calcDate
    ): float {
        $delPrice = $this->purchasePriceGateway->getDelCopperPriceByDate(
            $calcDate,
            $this->config->getCopperSurchargeArticleId()
        );
        $perCent = $this->config->getSurchargeDeliveryCosts() / 100;

        return (($delPrice + ($delPrice * $perCent)) - $copperBase) * $amount / 100;
    }

    /**
     * @param string $docType
     * @param int    $positionId
     * @param int    $positionArticleId
     *
     * @return float
     */
    private function calcAmount(string $docType, int $positionId, int $positionArticleId): float
    {
        if ($this->config->getSurchargeMaintenanceType() === CopperSurchargeData::SURCHARGE_MAINTENANCE_TYPE_APP) {
            $amount = $this->rawMaterialGateway->getRawMaterialAmount(
                $positionArticleId,
                $this->config->getCopperSurchargeArticleId()
            );
        } else {
            $articleId = $this->documentGateway->getArticleIdByPositionId($docType, $positionId);
            $amount = $this->documentGateway->getArticleCopperNumber(
                $articleId,
                $this->config->getCopperNumberOption()
            );
        }

        $documentAmount = $this->documentGateway->getPositionAmount($docType, $positionId);

        $amount *= $documentAmount;

        return (float)$amount;
    }

    /**
     * @param int $positionArticleId
     *
     * @return float
     */
    private function getCopperBase(int $positionArticleId): float
    {
        $copperBase = $this->config->getSurchargeCopperBaseStandard();

        $articleCopperBaseField = $this->config->getSurchargeCopperBase();
        if (!empty($articleCopperBaseField)) {
            $copperBaseTemp = $this->documentGateway->getArticleCopperBase($positionArticleId, $articleCopperBaseField);
            if (!empty($copperBaseTemp)) {
                $copperBase = $copperBaseTemp;
            }
        }

        return $copperBase;
    }

    /**
     * @param string $doctype
     * @param int    $docId
     *
     * @throws InvalidDateFormatException
     * @return DateTimeInterface
     */
    private function evaluateCalcDate(string $doctype, int $docId): DateTimeInterface
    {
        $orderOfferId = 0;
        if ($doctype === 'auftrag') {
            $orderOfferId = $this->documentGateway->findOrderOfferId($docId);
        }

        if (
            $doctype === 'rechnung' &&
            $this->config->getSurchargeInvoice() === CopperSurchargeData::INVOICE_CREATE_POS_BY_DELIVERY_DATE
        ) {
            $calcDate = $this->documentGateway->findDeliveryDate($docId);
            if (empty($calcDate)) {
                $calcDate = new DateTimeImmutable();
            }
        } elseif (
            $doctype === 'rechnung' &&
            $this->config->getSurchargeInvoice() === CopperSurchargeData::INVOICE_CREATE_POS_BY_ORDER_DATE
        ) {
            $orderId = $this->documentGateway->findInvoiceOrderId($docId);
            if (empty($orderId)) {
                $calcDate = new DateTimeImmutable();
            } else {
                $calcDate = $this->documentGateway->getCalcDate('auftrag', $orderId);
            }
        } elseif ($doctype === 'rechnung' &&
            $this->config->getSurchargeInvoice() === CopperSurchargeData::INVOICE_CREATE_POS_BY_INVOICE_DATE) {
            $calcDate = $this->documentGateway->getCalcDate($doctype, $docId);
        } elseif ($doctype === 'rechnung' &&
            $this->config->getSurchargeInvoice() === CopperSurchargeData::INVOICE_CREATE_POS_BY_OFFER_DATE) {
            $offerId = $this->documentGateway->findInvoiceOfferId($docId);
            if (!empty($offerId)) {
                $calcDate = $this->documentGateway->getCalcDate('angebot', $offerId);
            } else {
                $calcDate = new DateTimeImmutable();
            }
        } elseif (
            $doctype === 'auftrag' &&
            $orderOfferId !== 0 &&
            $this->config->getSurchargeDocumentConversion() === CopperSurchargeData::DOCUMENT_CONVERSION_FROM_OFFER
        ) {
            $calcDate = $this->documentGateway->getCalcDate('angebot', $orderOfferId);
        } else {
            $calcDate = new DateTimeImmutable();
        }

        return $calcDate;
    }

    /**
     * @param int    $docId
     * @param string $docType
     * @param int    $copperSurchargeArticleId
     */
    private function resetBetweenPositions(int $docId, string $docType, int $copperSurchargeArticleId): void
    {
        if ($this->config->getSurchargeMaintenanceType() === CopperSurchargeData::SURCHARGE_MAINTENANCE_TYPE_APP) {
            $positions = $this->rawMaterialGateway->findAllPositionsForGrouped(
                $docType,
                $docId,
                $copperSurchargeArticleId
            );
        } else {
            $positions = $this->documentGateway->findAllPositionsForGrouped(
                $docType,
                $docId,
                $this->config->getCopperNumberOption()
            );
        }

        $offset = 0;
        foreach ($positions as $position) {
            if ((int)$position['article_id'] === $copperSurchargeArticleId) {
                $offset--;
            }

            if ((int)$position['pos_type'] === 2 && $offset < 0) {
                $this->documentService->updateBetweenSort(
                    (int)$position['between_id'],
                    (int)$position['sort'] + $offset
                );
            }
        }
    }

    /**
     * @param string $docType
     * @param int    $docId
     */
    public function deleteRemainingCopperSurchargeArticles(string $docType, int $docId): void
    {
        if ($this->config->getSurchargeMaintenanceType() === CopperSurchargeData::SURCHARGE_MAINTENANCE_TYPE_ARTICLE) {
            $hasCopperArticles =
                $this->documentGateway->hasCopperArticles(
                    $docType,
                    $docId,
                    $this->config->getCopperNumberOption()
                );
        } else {
            $hasCopperArticles =
                $this->rawMaterialGateway->hasCopperArticles(
                    $docType,
                    $docId,
                    $this->config->getCopperSurchargeArticleId()
                );
        }

        if (!$hasCopperArticles) {
            $this->documentService->deleteCopperSurchargePositions(
                $docType,
                $docId,
                $this->config->getCopperSurchargeArticleId()
            );
        }
    }

    /**
     * @param string $docType
     * @param int    $posId
     */
    public function updatePositionContributionMargin(string $docType, int $posId)
    {
        $this->documentService->updatePositionContributionMargin($docType, $posId, 0);
    }

    /**
     * @param string $docType
     * @param int    $posId
     */
    public function updatePositionPurchasePrice(string $docType, int $posId)
    {
        $this->documentService->updatePositionPurchasePrice($docType, $posId, 0.0);
    }

    /**
     * @param string $docType
     *
     * @param int    $docTypeId
     *
     * @return array|DocumentPositionData[]
     */
    public function findPositionsForMaintenanceApp(string $docType, int $docTypeId): array
    {
        $data = $this->rawMaterialGateway->findPositions(
            $docType,
            $docTypeId,
            $this->config->getCopperSurchargeArticleId()
        );

        return $this->transformToDocumentPositionData($data);
    }

    /**
     * @param string $docType
     *
     * @param int    $docTypeId
     *
     * @return array|DocumentPositionData[]
     */
    public function findPositionsForMaintenanceArticle(string $docType, int $docTypeId): array
    {
        $data = $this->documentGateway->findPositions(
            $docType,
            $docTypeId,
            $this->config->getCopperNumberOption()
        );

        return $this->transformToDocumentPositionData($data);
    }

    /**
     * @param array $copperPositionsRaw
     *
     * @return array|DocumentPositionData[]
     */
    private function transformToDocumentPositionData(array $copperPositionsRaw): array
    {
        $documentPositions = [];
        foreach ($copperPositionsRaw as $position) {
            $data = new DocumentPositionData(
                (int)$position['pos_id'],
                (int)$position['article_id'],
                (string)$position['currency']
            );
            $documentPositions[] = $data;
        }

        return $documentPositions;
    }

    /**
     * @param int    $doctypeId
     * @param string $doctype
     */
    public function updateCopperSurchargeArticles(int $doctypeId, string $doctype)
    {
        $copperSurchargeArticleId = $this->config->getCopperSurchargeArticleId();
        $surchargePositions = $this->documentGateway->findCopperSurchargeArticlePositionIds(
            $doctype,
            $doctypeId,
            $copperSurchargeArticleId
        );

        if (!empty($surchargePositions)) {
            foreach ($surchargePositions as $surchargePosition) {
                $this->updatePositionContributionMargin($doctype, (int)$surchargePosition['pos_id']);
                $this->updatePositionPurchasePrice($doctype, (int)$surchargePosition['pos_id']);
            }
        }
    }

    /**
     * @param array $copperPositionsInPartsList
     * @param int   $lastSort
     * @param int   $nextSort
     *
     * @return array
     */
    private function getElementsFromPartListBetweenSorts(
        array $copperPositionsInPartsList,
        int $lastSort,
        int $nextSort
    ): array {
        $result = [];
        foreach ($copperPositionsInPartsList as $partListKey => $position) {
            $currentSort = $position['sort'];
            if ($currentSort > $lastSort && $currentSort <= $nextSort) {
                $position['part_list_key'] = $partListKey;
                $result[] = $position;
            }
        }

        return $result;
    }

    /**
     * @param int    $partListHeadId
     * @param int    $surchargeMaintenanceType
     * @param string $copperNumberOption
     * @param int    $copperArticleId
     *
     * @return array
     */
    private function getCopperArticlesFromPartList(
        int $partListHeadId,
        int $surchargeMaintenanceType,
        string $copperNumberOption,
        int $copperArticleId
    ): array {
        $result = [];
        $childElements = $this->documentGateway->getAllPartListChildElements($partListHeadId);

        foreach ($childElements as $childElement) {
            if ($surchargeMaintenanceType === CopperSurchargeData::SURCHARGE_MAINTENANCE_TYPE_ARTICLE) {
                $possibleArticle = $this->documentGateway->findPossibleCopperArticle(
                    $childElement['id'],
                    $copperNumberOption
                );
            } else {
                $possibleArticle = $this->rawMaterialGateway->findPossibleCopperArticle(
                    $childElement['id'],
                    $copperArticleId
                );
            }

            if (empty($possibleArticle)) {
                continue;
            }

            $result[] = [
                'article_id' => $possibleArticle['article_id'],
                'amount'     => $possibleArticle['amount'] * $childElement['amount'],
            ];
        }

        return $result;
    }

    /**
     * @param string $docType
     * @param int    $docTypeId
     *
     * @return array
     */
    public function findPositionsForMaintenanceAppInPartsList(
        string $docType,
        int $docTypeId
    ): array {
        $result = [];

        $copperArticleId = $this->config->getCopperSurchargeArticleId();

        $headArticles = $this->documentGateway->findPartListHeadArticles($docType, $docTypeId);

        foreach ($headArticles as $headArticle) {
            $childElements = $this->documentGateway->getAllPartListChildElements($headArticle['id']);
            $hasCopper = false;
            foreach ($childElements as $childElement) {
                if (!$hasCopper) {
                    $hasCopper = !empty(
                    $this->rawMaterialGateway->findPossibleCopperArticle(
                        $childElement['id'],
                        $copperArticleId
                    )
                    );
                }
            }
            if ($hasCopper) {
                $result[] = [
                    'article_id' => $headArticle['id'],
                    'sort'       => $headArticle['sort'],
                    'pos_id'     => $headArticle['pos_id'],
                    'currency'   => $headArticle['currency'],
                    'amount'     => (float)$headArticle['amount'],
                ];
            }
        }

        return $result;
    }

    /**
     * @param string $docType
     * @param int    $docTypeId
     *
     * @return array
     */
    public function findPositionsForMaintenanceArticleInPartsList(
        string $docType,
        int $docTypeId
    ): array {
        $result = [];

        $copperNumberOption = $this->config->getCopperNumberOption();

        $headArticles = $this->documentGateway->findPartListHeadArticles($docType, $docTypeId);

        foreach ($headArticles as $headArticle) {
            $childElements = $this->documentGateway->getAllPartListChildElements((int)$headArticle['id']);
            $hasCopper = false;
            foreach ($childElements as $childElement) {
                if (!$hasCopper) {
                    $hasCopper = !empty(
                    $this->documentGateway->findPossibleCopperArticle(
                        $childElement['id'],
                        $copperNumberOption
                    )
                    );
                }
            }
            if ($hasCopper) {
                $result[] = [
                    'article_id' => $headArticle['id'],
                    'sort'       => $headArticle['sort'],
                    'pos_id'     => $headArticle['pos_id'],
                    'currency'   => $headArticle['currency'],
                    'amount'     => (float)$headArticle['amount'],
                ];
            }
        }

        return $result;
    }
}
