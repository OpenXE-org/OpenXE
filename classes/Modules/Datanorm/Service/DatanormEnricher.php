<?php

declare(strict_types=1);

namespace Xentral\Modules\Datanorm\Service;

use Xentral\Modules\Datanorm\Data\DatanormATypeData;
use Xentral\Modules\Datanorm\Data\DatanormDTypeData;
use Xentral\Modules\Datanorm\Data\DatanormPTypeData;
use Xentral\Modules\Datanorm\Data\DatanormTTypeData;

final class DatanormEnricher
{
    /** @var DatanormIntermediateGateway $intermediateGateway */
    private $intermediateGateway;

    /**
     * @param DatanormIntermediateGateway $intermediateGateway
     */
    public function __construct(DatanormIntermediateGateway $intermediateGateway)
    {
        $this->intermediateGateway = $intermediateGateway;
    }

    /**
     * @param DatanormPTypeData $pType
     *
     * @return DatanormPTypeData
     */
    public function enrichPrice(DatanormPTypeData $pType): DatanormPTypeData
    {
        $articleNumber = $pType->getArticleNumber1();
        $data = $this->intermediateGateway->findArticleLineByNumber($articleNumber);
        $amount = $this->getPriceAmount($data);
        $pType->setPriceAmount1($amount);
        $price = $pType->getPrice1();
        $pType->setPrice1($price / $amount);

        $articleNumber2 = $pType->getArticleNumber2();
        if (!empty($articleNumber2)) {
            $data = $this->intermediateGateway->findArticleLineByNumber($articleNumber2);
            if (isset($data['content'])) {
                $amount = $this->getPriceAmount($data);
                $pType->setPriceAmount2($amount);
                $price = $pType->getPrice2();
                $pType->setPrice2($price / $amount);
            }
        }

        $articleNumber3 = $pType->getArticleNumber3();
        if (!empty($articleNumber3)) {
            $data = $this->intermediateGateway->findArticleLineByNumber($articleNumber3);
            if (isset($data['content'])) {
                $amount = $this->getPriceAmount($data);
                $pType->setPriceAmount3($amount);
                $price = $pType->getPrice3();
                $pType->setPrice3($price / $amount);
            }
        }

        return $pType;
    }

    /**
     * @param array $data
     *
     * @return int
     */
    private function getPriceAmount(array $data): int
    {
        $aType = new DatanormATypeData();
        $aType->fillByJson($data['content']);

        return (int)$aType->getPriceAmount();
    }

    /**
     * @param DatanormATypeData $aType
     *
     * @return DatanormATypeData
     */
    public function enrichArticle(DatanormATypeData $aType): DatanormATypeData
    {
        $longTextBlockNumber = $aType->getLongDecriptionKey();
        $textFlag = substr($aType->getTextkey(), 0, 1);

        $text = '';
        $longtText = '';
        $dimensionText = '';

        if (!empty($longTextBlockNumber)) {
            $longtextData = $this->intermediateGateway->findTTypeContentByBlocknumber($longTextBlockNumber);
            if (!empty($longtextData)) {
                $longtText = $this->createLongText($longtextData);
            }
        }

        $dimensionTextData = $this->intermediateGateway->findDTypeContentByArticleNumer($aType->getArticleNumber());
        if (!empty($dimensionTextData)) {
            $dimensionText = $this->getDimensionText($dimensionTextData);
        }

        if ($textFlag === '0') { //KT1 + KT2
            $text .= $aType->getShortDescription1() . PHP_EOL;
            $text .= $aType->getShortDescription2();
        } elseif ($textFlag === '1') { //LT + KT2
            $text .= $longtText . PHP_EOL;
            $text .= $aType->getShortDescription2();
        } elseif ($textFlag === '2') { //KT1 + DT
            $text .= $aType->getShortDescription1() . PHP_EOL;
            $text .= $dimensionText;
        } elseif ($textFlag === '3') { //LT + DT
            $text .= $longtText . PHP_EOL;
            $text .= $dimensionText;
        } elseif ($textFlag === '4') { //KT1 + KT2 + LT
            $text .= $aType->getShortDescription1() . PHP_EOL;
            $text .= $aType->getShortDescription2() . PHP_EOL;
            $text .= $longtText;
        } elseif ($textFlag === '5') { //KT1 + KT2 + DT
            $text .= $aType->getShortDescription1() . PHP_EOL;
            $text .= $aType->getShortDescription2() . PHP_EOL;
            $text .= $dimensionText;
        } elseif ($textFlag === '6') { //KT1 + KT2 + LT + DT
            $text .= $aType->getShortDescription1() . PHP_EOL;
            $text .= $aType->getShortDescription2() . PHP_EOL;
            $text .= $longtText . PHP_EOL;
            $text .= $dimensionText;
        }

        if (!empty($text)) {
            $aType->setShortDescription2(trim($text));
        }

        return $aType;
    }

    /**
     * @param array $longTextData
     *
     * @return string
     */
    private function createLongText(array $longTextData): string
    {
        $longtext = '';
        foreach ($longTextData as $d) {
            $tType = new DatanormTTypeData();
            $tType->fillByJson($d['content']);

            if (!empty($tType->getText1())) {
                $longtext .= $tType->getText1() . PHP_EOL;
            }

            if (!empty($tType->getText2())) {
                $longtext .= $tType->getText2() . PHP_EOL;
            }
        }

        return trim($longtext);
    }

    /**
     * @param array $dimensionTextData
     *
     * @return string
     */
    private function getDimensionText(array $dimensionTextData): string
    {
        $dimensionText = '';

        foreach ($dimensionTextData as $d) {
            $dType = new DatanormDTypeData();
            $dType->fillByJson($d['content']);

            $textIndicator1 = $dType->getTextIndicator1();
            $textIndicator2 = $dType->getTextIndicator2();

            if (!empty($textIndicator1)) {
                $txt = $this->createDimensionText(
                    $textIndicator1,
                    $dType->getText1(),
                    $dType->getTextblockNumber1()
                );
                if (!empty($txt)) {
                    $dimensionText .= $txt . PHP_EOL;
                }
            }

            if (!empty($textIndicator2)) {
                $txt = $this->createDimensionText(
                    $textIndicator2,
                    $dType->getText2(),
                    $dType->getTextblockNumber2()
                );
                if (!empty($txt)) {
                    $dimensionText .= $txt . PHP_EOL;
                }
            }
        }

        return trim($dimensionText);
    }

    /**
     * @param string $textIndicator
     * @param string $text
     * @param string $textBlockNumber
     *
     * @return string
     */
    private function createDimensionText(string $textIndicator, string $text, string $textBlockNumber): string
    {
        $dimensionText = '';

        if ($textIndicator === 'F') {
            $dimensionText = $text;
        } else {
            $longTextData = $this->intermediateGateway->findTTypeContentByBlocknumber(
                $textBlockNumber
            );

            if ($textIndicator === 'T') {
                $dimensionText .= $this->createLongText($longTextData);
            } elseif ($textIndicator === 'E') {
                $dimensionText .= $this->createInsertingText($text, $longTextData);
            }
        }

        return $dimensionText;
    }

    /**
     * @param string $fillementText
     * @param array  $longTextData
     *
     * @return string
     */
    private function createInsertingText(string $fillementText, array $longTextData): string
    {
        $tType = new DatanormTTypeData();
        $tType->fillByJson($longTextData[0]['content']);
        $pattern = $tType->getText1();

        $patternExp = explode('$$$', $pattern);
        $fillmentExp = explode('$', $fillementText);

        $text = '';
        for ($i = 0; $i < count($patternExp); $i++) {
            $text .= $patternExp[$i] . $fillmentExp[$i];
        }

        return $text;
    }
}
