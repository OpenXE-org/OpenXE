<?php

declare(strict_types=1);

namespace Xentral\Modules\Datanorm\Handler;

use Xentral\Modules\Datanorm\Data\DatanormATypeData;
use Xentral\Modules\Datanorm\Data\DatanormBTypeData;
use Xentral\Modules\Datanorm\Data\DatanormDTypeData;
use Xentral\Modules\Datanorm\Data\DatanormPTypeData;
use Xentral\Modules\Datanorm\Data\DatanormTTypeData;
use Xentral\Modules\Datanorm\Data\DatanormVTypeData;
use Xentral\Modules\Datanorm\Exception\InvalidLineException;
use Xentral\Modules\Datanorm\Exception\WrongDiscountFormatException;
use Xentral\Modules\Datanorm\Exception\WrongPriceFormatException;

final class DatanormReaderVersionFourHandler extends AbstractDatanormReaderHandler
    implements DatanormReaderHandlerInterface
{

    /**
     * @param string $line
     *
     * @throws WrongPriceFormatException
     *
     * @return DatanormATypeData
     */
    public function transformToTypeA(string $line): DatanormATypeData
    {
        $split = explode(';', $line);

        if (count($split) < 12) {
            throw new InvalidLineException('The A-record has not the right amount of columns.');
        }

        $atype = new DatanormATypeData();

        $atype->setWorkflowState($split[1]);
        $atype->setArticleNumber($split[2]);
        $atype->setTextkey($split[3]);
        $atype->setShortdescription1($split[4]);
        $atype->setShortDescription2($split[5]);
        $atype->setPriceMark($split[6]);
        $atype->setPriceAmount($this->transformPriceAmountCode($split[7]));
        $atype->setPackingUnit($split[8]);
        $atype->setPrice($this->convertPrice($split[9]) / $this->transformPriceAmountCode($split[7]));
        $atype->setDiscountGroup($split[10]);
        $atype->setMainProductGroup($split[11]);
        $atype->setLongDecriptionKey($split[12]);

        return $atype;
    }

    /**
     * @param string $line
     *
     * @throws WrongPriceFormatException
     * @throws WrongDiscountFormatException
     *
     * @return DatanormPTypeData
     */
    public function transformToTypeP(string $line): DatanormPTypeData
    {
        $split = explode(';', $line);

        if (count($split) < 29) {
            throw new InvalidLineException('The P-record has not the right amount of columns.');
        }

        $pType = new DatanormPTypeData();

        $pType->setArticleNumber1($split[2]);
        $pType->setPriceMark1($split[3]);
        $pType->setPriceAmount1(1);
        $pType->setPrice1($this->convertPrice($split[4]));

        $pType->setDiscountKey1a($split[5]);
        if (!empty($split[5]) && $split[5] !== '0') {
            $pType->setDiscount1a($this->convertDiscount($split[5], $split[6]));
        }

        $pType->setDiscountKey1b($split[7]);
        if (!empty($split[7]) && $split[7] !== '0') {
            $pType->setDiscount1b($this->convertDiscount($split[7], $split[8]));
        }

        $pType->setDiscountKey1c($split[9]);
        if (!empty($split[9]) && $split[9] !== '0') {
            $pType->setDiscount1c($this->convertDiscount($split[9], $split[10]));
        }

        $pType->setArticleNumber2($split[11]);
        $pType->setPriceMark2($split[12]);
        $pType->setPriceAmount2(1);
        $pType->setPrice2($this->convertPrice($split[13]));

        $pType->setDiscountKey2a($split[14]);
        if (!empty($split[14]) && $split[14] !== '0') {
            $pType->setDiscount2a($this->convertDiscount($split[14], $split[15]));
        }

        $pType->setDiscountKey2b($split[16]);
        if (!empty($split[16]) && $split[16] !== '0') {
            $pType->setDiscount2b($this->convertDiscount($split[16], $split[17]));
        }

        $pType->setDiscountKey2c($split[18]);
        if (!empty($split[18]) && $split[18] !== '0') {
            $pType->setDiscount2c($this->convertDiscount($split[18], $split[19]));
        }

        $pType->setArticleNumber3($split[20]);
        $pType->setPriceMark3($split[21]);
        $pType->setPriceAmount3(1);
        $pType->setPrice3($this->convertPrice($split[22]));

        $pType->setDiscountKey3a($split[23]);
        if (!empty($split[23]) && $split[23] !== '0') {
            $pType->setDiscount3a($this->convertDiscount($split[23], $split[24]));
        }

        $pType->setDiscountKey3b($split[25]);
        if (!empty($split[25]) && $split[25] !== '0') {
            $pType->setDiscount3b($this->convertDiscount($split[25], $split[26]));
        }

        $pType->setDiscountKey3c($split[27]);
        if (!empty($split[27]) && $split[27] !== '0') {
            $pType->setDiscount3c($this->convertDiscount($split[27], $split[28]));
        }

        return $pType;
    }

    /**
     * @param string $line
     *
     * @param InvalidLineException
     *
     * @return DatanormVTypeData
     */
    public function transformToTypeV(string $line): DatanormVTypeData
    {
        if (strlen($line) > 130) {
            throw new InvalidLineException('The V-record has the wrong length.');
        }

        $creationDate = trim(substr($line, 2, 6));
        $info1 = trim(substr($line, 8, 40));
        $info2 = trim(substr($line, 48, 40));
        $info3 = trim(substr($line, 88, 35));
        $currency = trim(substr($line, 125, 3));

        $creationDateFormat = '';
        if (!empty($creationDate)) {
            $creationDateFormat =
                '20' . substr($creationDate, 4, 2) .
                substr($creationDate, 2, 2) .
                substr($creationDate, 0, 2);
        }

        $vType = new DatanormVTypeData();

        $vType->setAdress2($info2);
        $vType->setDate($creationDateFormat);
        $vType->setCurrency($currency);
        $vType->setDescription(trim($info1 . ' ' . $info2 . ' ' . $info3));

        return $vType;
    }

    /**
     * @param string $line
     *
     * @throws InvalidLineException
     *
     * @return DatanormBTypeData
     */
    public function transformToTypeB(string $line): DatanormBTypeData
    {
        $split = explode(';', $line);

        if (count($split) < 16) {
            throw new InvalidLineException('The B-record has not the right amount of columns.');
        }

        $bType = new DatanormBTypeData();

        $bType->setProcessingFlag($split[1]);
        $bType->setArticleNumber($split[2]);
        $bType->setMatchcode($split[3]);
        $bType->setAltArticleNumber($split[4]);
        $bType->setCopperWeightIndicator($split[6]);
        $bType->setCopperRawPrice($split[7]);
        $bType->setCopperWeight($split[8]);
        $bType->setEan($split[9]);
        $bType->setGraphicNumber($split[10]);
        $bType->setProductGroup($split[11]);
        $bType->setCostIndicator($split[12]);
        $bType->setOrderAmount($split[13]);
        $bType->setCreatorReferenceNumber($split[14]);
        $bType->setReferenceNumber($split[15]);

        return $bType;
    }

    /**
     * @param string $priceAmountCode
     *
     * @return int
     */
    private function transformPriceAmountCode(string $priceAmountCode): int
    {
        $priceAmount = 1;

        if ($priceAmountCode === '1') {
            $priceAmount = 10;
        } elseif ($priceAmountCode === '2') {
            $priceAmount = 100;
        } elseif ($priceAmountCode === '3') {
            $priceAmount = 1000;
        }

        return $priceAmount;
    }

    /**
     * @return int
     */
    public function getVersion(): int
    {
        return 4;
    }

    /**
     * @param string $line
     *
     * @return DatanormTTypeData
     */
    public function transformToTypeT(string $line): DatanormTTypeData
    {
        $split = explode(';', $line);

        $tType = new DatanormTTypeData();

        if (isset($split[1])) {
            $tType->setProcessingFlag($split[1]);
        }
        if (isset($split[2])) {
            $tType->setTextnumber($split[2]);
        }
        if (isset($split[4])) {
            $tType->setLinenumber1($split[4]);
        }
        if (isset($split[6])) {
            $tType->setText1($split[6]);
        }
        if (isset($split[7])) {
            $tType->setLinenumber2($split[7]);
        }
        if (isset($split[9])) {
            $tType->setText2($split[9]);
        }

        return $tType;
    }

    /**
     * @param string $line
     *
     * @return DatanormDTypeData
     */
    public function transformToTypeD(string $line): DatanormDTypeData
    {
        $split = explode(';', $line);

        $dType = new DatanormDTypeData();

        if (isset($split[1])) {
            $dType->setProcessingFlag($split[1]);
        }
        if (isset($split[2])) {
            $dType->setArticleNumber($split[2]);
        }
        if (isset($split[3])) {
            $dType->setLineNumber1($split[3]);
        }
        if (isset($split[4])) {
            $dType->setTextIndicator1($split[4]);
        }
        if (isset($split[5])) {
            $dType->setTextblockNumber1($split[5]);
        }
        if (isset($split[6])) {
            $dType->setText1($split[6]);
        }
        if (isset($split[8])) {
            $dType->setTextIndicator2($split[8]);
        }
        if (isset($split[9])) {
            $dType->setTextblockNumber2($split[9]);
        }
        if (isset($split[10])) {
            $dType->setText2($split[10]);
        }

        return $dType;
    }
}
