<?php

declare(strict_types=1);

namespace Xentral\Modules\Datanorm;

use Xentral\Components\Database\Exception\QueryFailureException;
use Xentral\Modules\Article\Service\PurchasePriceService;
use Xentral\Modules\Article\Service\SellingPriceService;
use Xentral\Modules\Datanorm\Data\DatanormATypeData;
use Xentral\Modules\Datanorm\Data\DatanormBTypeData;
use Xentral\Modules\Datanorm\Data\DatanormPTypeData;
use Xentral\Modules\Datanorm\Data\DatanormVTypeData;
use Xentral\Modules\Datanorm\Exception\ArticleNotFoundException;
use Xentral\Modules\Datanorm\Exception\InvalidArgumentException;
use Xentral\Modules\Datanorm\Service\ArticleService;
use Xentral\Modules\Datanorm\Service\DatanormEnricher;
use Xentral\Modules\Datanorm\Service\DatanormIntermediateGateway;
use Xentral\Modules\Datanorm\Service\DatanormIntermediateService;
use Xentral\Modules\Datanorm\Service\DatanormConverter;
use Xentral\Modules\Datanorm\Wrapper\AddressWrapper;


final class DatanormImporter
{
    /** @var DatanormIntermediateService $intermediateService */
    private $intermediateService;

    /** @var DatanormIntermediateGateway $intermediateGateway */
    private $intermediateGateway;

    /** @var DatanormConverter $datanormConverter */
    private $datanormConverter;

    /** @var ArticleService $articleService */
    private $articleService;

    /** @var SellingPriceService $sellingPriceService */
    private $sellingPriceService;

    /** @var PurchasePriceService $purchasePriceService */
    private $purchasePriceService;

    /** @var AddressWrapper $addressWrapper */
    private $addressWrapper;

    /** @var DatanormEnricher $enricher */
    private $enricher;

    /**
     * @param DatanormIntermediateService $intermediateService
     * @param DatanormIntermediateGateway $intermediateGateway
     * @param DatanormConverter           $datanormTransformer
     * @param ArticleService              $articleService
     * @param SellingPriceService         $sellingPriceService
     * @param PurchasePriceService        $purchasePriceService
     * @param AddressWrapper              $addressWrapper
     * @param DatanormEnricher            $enrichService
     */
    public function __construct(
        DatanormIntermediateService $intermediateService,
        DatanormIntermediateGateway $intermediateGateway,
        DatanormConverter $datanormTransformer,
        ArticleService $articleService,
        SellingPriceService $sellingPriceService,
        PurchasePriceService $purchasePriceService,
        AddressWrapper $addressWrapper,
        DatanormEnricher $enrichService
    ) {
        $this->intermediateService = $intermediateService;
        $this->intermediateGateway = $intermediateGateway;
        $this->datanormConverter = $datanormTransformer;
        $this->articleService = $articleService;
        $this->sellingPriceService = $sellingPriceService;
        $this->purchasePriceService = $purchasePriceService;
        $this->addressWrapper = $addressWrapper;
        $this->enricher = $enrichService;
    }

    /**
     * @return bool
     */
    public function hasLines(): bool
    {
        return !empty($this->intermediateGateway->getLines(1));
    }

    /**
     * @param int $limit
     *
     * @throws ArticleNotFoundException
     * @throws QueryFailureException
     * @throws InvalidArgumentException
     */
    public function handleLines(int $limit): void
    {
        $sellingPrices = [];
        $purchasePrices = [];
        $lastFileName = '';
        $doneIds = [];

        $headData = null;
        $head = null;
        $supplierId = null;

        $intermediateDatas = $this->intermediateGateway->getLines($limit);

        foreach ($intermediateDatas as $intermediateData) {

            $type = $intermediateData['type'];
            $content = $intermediateData['content'];
            $fileName = $intermediateData['fileName'];

            if ($lastFileName !== $fileName) {
                $head = $this->getHeadLine(
                    $this->intermediateGateway->getVType($fileName)
                );
                $supplierId = $head->getSupplierAddressId();
                if(empty($supplierId)){
                    $supplierId = $this->addressWrapper->insertSupplierIfNotExists($head);
                }
                $lastFileName = $fileName;
            }

            if ($type === 'A') {
                $aType = new DatanormATypeData();
                $aType->fillByJson($content);
                $articleArray = $this->datanormConverter->transformATypeToArticleArray($aType);

                $articleId = $this->articleService->InsertUpdateArticle($articleArray);

                if (!empty($aType->getPrice())) {
                    $prices = $this->datanormConverter->transformToPriceArray(
                        $articleId,
                        $aType->getPriceMark(),
                        $head->getCurrency(),
                        $aType->getPriceAmount(),
                        $aType->getPrice(),
                        $supplierId,
                        '',
                        0,
                        '',
                        0,
                        '',
                        0
                    );
                    if (!empty($prices['sellingPrice'])) {
                        $sellingPrices[] = $prices['sellingPrice'];
                    }
                    if (!empty($prices['purchasePrices'])) {
                        foreach ($prices['purchasePrices'] as $purchasePrice) {
                            $purchasePrices[] = $purchasePrice;
                        }
                    }
                }
            } elseif ($type === 'P') {
                $pType = new DatanormPTypeData();
                $pType->fillByJson($content);

                $articleId = $this->articleService->findArticleIdByNumber($pType->getArticleNumber1());

                if (!empty($articleId)) {
                    $prices = $this->datanormConverter->transformToPriceArray(
                        $articleId,
                        $pType->getPriceMark1(),
                        $head->getCurrency(),
                        $pType->getPriceAmount1(),
                        $pType->getPrice1(),
                        $supplierId,
                        $pType->getDiscountKey1a(),
                        $pType->getDiscount1a(),
                        $pType->getDiscountKey1b(),
                        $pType->getDiscount1b(),
                        $pType->getDiscountKey1c(),
                        $pType->getDiscount1c()
                    );
                    if (!empty($prices['sellingPrice'])) {
                        $sellingPrices[] = $prices['sellingPrice'];
                    }
                    if (!empty($prices['purchasePrices'])) {
                        foreach ($prices['purchasePrices'] as $purchasePrice) {
                            $purchasePrices[] = $purchasePrice;
                        }
                    }
                } else {
                    throw new ArticleNotFoundException(
                        'The article with the number ' . $pType->getArticleNumber1() . ' was not found.'
                    );
                }

                if (!empty($pType->getArticleNumber2())) {
                    $articleId = $this->articleService->findArticleIdByNumber($pType->getArticleNumber2());

                    if (!empty($articleId)) {
                        $prices = $this->datanormConverter->transformToPriceArray(
                            $articleId,
                            $pType->getPriceMark2(),
                            $head->getCurrency(),
                            $pType->getPriceAmount2(),
                            $pType->getPrice2(),
                            $supplierId,
                            $pType->getDiscountKey2a(),
                            $pType->getDiscount2a(),
                            $pType->getDiscountKey2b(),
                            $pType->getDiscount2b(),
                            $pType->getDiscountKey2c(),
                            $pType->getDiscount2c()
                        );
                        if (!empty($prices['sellingPrice'])) {
                            $sellingPrices[] = $prices['sellingPrice'];
                        }
                        if (!empty($prices['purchasePrices'])) {
                            foreach ($prices['purchasePrices'] as $purchasePrice) {
                                $purchasePrices[] = $purchasePrice;
                            }
                        }
                    } else {
                        throw new ArticleNotFoundException(
                            'The article with the number ' . $pType->getArticleNumber2() . ' was not found.'
                        );
                    }
                }

                if (!empty($pType->getArticleNumber3())) {
                    $articleId = $this->articleService->findArticleIdByNumber($pType->getArticleNumber3());

                    if (!empty($articleId)) {
                        $prices = $this->datanormConverter->transformToPriceArray(
                            $articleId,
                            $pType->getPriceMark3(),
                            $head->getCurrency(),
                            $pType->getPriceAmount3(),
                            $pType->getPrice3(),
                            $supplierId,
                            $pType->getDiscountKey3a(),
                            $pType->getDiscount3a(),
                            $pType->getDiscountKey3b(),
                            $pType->getDiscount3b(),
                            $pType->getDiscountKey3c(),
                            $pType->getDiscount3c()
                        );
                        if (!empty($prices['sellingPrice'])) {
                            $sellingPrices[] = $prices['sellingPrice'];
                        }
                        if (!empty($prices['purchasePrices'])) {
                            foreach ($prices['purchasePrices'] as $purchasePrice) {
                                $purchasePrices[] = $purchasePrice;
                            }
                        }
                    } else {
                        throw new ArticleNotFoundException(
                            'The article with the number ' . $pType->getArticleNumber3() . ' was not found.'
                        );
                    }
                }
            } elseif ($type === 'B') {
                $bType = new DatanormBTypeData();
                $bType->fillByJson($content);

                $articleArray = $this->datanormConverter->transformBTypeToArticleArray($bType);
                if (!empty($articleArray)) {
                    $this->articleService->InsertUpdateArticle($articleArray);
                }
            }

            $doneIds[] = $intermediateData['id'];
        }

        if (!empty($doneIds)) {
            $this->intermediateService->setMultipleDone($doneIds);
        }

        if (!empty($sellingPrices)) {
            $this->sellingPriceService->setStandardPriceByArray($sellingPrices);
        }

        if (!empty($purchasePrices)) {
            $this->purchasePriceService->setPurchasePriceByArray($purchasePrices);
        }
    }

    /**
     * @param array $headdata
     *
     * @return DatanormVTypeData
     */
    private function getHeadLine(array $headdata): DatanormVTypeData
    {
        $head = new DatanormVTypeData();
        $head->fillByJson($headdata['content']);
        $head->setUserAddressId($headdata['user_address_id']);
        $head->setSupplierAddressId($headdata['supplier_address_id']);

        return $head;
    }


    /**
     * @return bool
     */
    public function checkFilesEnrichState(): bool
    {
        $lines = $this->intermediateGateway->getLinesToEnrich(1);
        if (count($lines) > 0) {
            return true;
        }

        return false;
    }


    /**
     * @param int $limit
     */
    public function enrich(int $limit): void
    {
        $lines = $this->intermediateGateway->getLinesToEnrich($limit);
        $enrichData = [];

        if (!empty($lines)) {
            foreach ($lines as $line) {
                $type = $line['type'];
                $content = $line['content'];
                $id = $line['id'];

                if ($type === 'A') {
                    $aType = new DatanormATypeData();
                    $aType->fillByJson($content);

                    $aTypeMod = $this->enricher->enrichArticle($aType);
                    $content = json_encode($aTypeMod);
                } elseif ($type === 'P') {
                    $pType = new DatanormPTypeData();
                    $pType->fillByJson($content);

                    $pTypeMod = $this->enricher->enrichPrice($pType);
                    $content = json_encode($pTypeMod);
                }

                if (!empty($content)) {
                    $enrichData[$id] = [
                        'content' => $content,
                        'hash'    => md5($content),
                        'enrich'  => false,
                    ];
                }
            }
        }

        if (!empty($enrichData)) {
            $this->intermediateService->saveEnrichData($enrichData);
        }
    }

    public function setTAndDTypeDone(): void
    {
        $this->intermediateService->setTAndDTypeDone();
    }

    /**
     * @param int    $vId
     * @param string $supplierNumber
     */
    public function saveSupplierToVType(int $vId, string $supplierNumber)
    {
        $this->intermediateService->saveSupplierToVType($vId,$supplierNumber);
    }

    /**
     * @param int $vId
     *
     * @return string
     */
    public function findSupplierNumberByVid(int $vId)
    {
        return $this->intermediateGateway->findSupplierNumberByVid($vId);
    }
}
