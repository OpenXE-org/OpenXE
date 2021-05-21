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

final class DatanormReaderVersionFiveHandler extends AbstractDatanormReaderHandler
    implements DatanormReaderHandlerInterface
{

    /**
     * @param string $line
     *
     * @throws WrongPriceFormatException
     * @throws InvalidLineException
     *
     * @return DatanormATypeData
     */
    public function transformToTypeA(string $line): DatanormATypeData
    {
        $split = explode(';', $line);

        if (count($split) < 29) {
            throw new InvalidLineException('The A-record has not the right amount of columns.');
        }

        /** @var  DatanormATypeData */
        $atype = new DatanormATypeData();

        $atype->setWorkflowState($split[1]);
        $atype->setArticleNumber($split[2]);
        $atype->setShortdescription1($split[3]);
        $atype->setShortDescription2($split[4]);
        $atype->setPackingUnit($split[5]);
        $atype->setPriceMark($split[6]);
        $atype->setPriceAmount((int)$split[7]);
        $atype->setPrice($this->convertPrice($split[8]) / (int)$split[7]);
        $atype->setDiscountGroup($split[9]);
        $atype->setMainProductGroup($split[10]);
        $atype->setProductGroup($split[11]);
        $atype->setMatchcode($split[12]);
        $atype->setProducerToken1($split[13]);
        $atype->setAltArticleNumber($split[14]);
        $atype->setProducerToken2($split[15]);
        $atype->setProducerNumber($split[16]);
        $atype->setProducerModel($split[17]);
        $atype->setEan($split[18]);
        $atype->setConnectionNumber($split[19]);
        $atype->setMinimumPackageAmount($split[20]);
        $atype->setCataloguePage($split[21]);
        $atype->setTextkey($split[22]);
        $atype->setLongDecriptionKey($split[23]);
        $atype->setCostType($split[24]);
        $atype->setArticleType($split[25]);
        $atype->setProducerToken3($split[26]);
        $atype->setReferenceNumber($split[27]);
        $atype->setMwstType((int)$split[28]);

        return $atype;
    }

    /**
     * @param string $line
     *
     * @throws WrongPriceFormatException
     * @throws InvalidLineException
     * @throws WrongDiscountFormatException
     *
     * @return DatanormPTypeData
     */
    public function transformToTypeP(string $line): DatanormPTypeData
    {
        $split = explode(';', $line);

        if (count($split) < 6) {
            throw new InvalidLineException('The P-record has not the right amount of columns.');
        }

        /** @var  DatanormPTypeData */
        $pType = new DatanormPTypeData();

        $pType->setArticleNumber1($split[1]);
        $pType->setPriceMark1($split[2]);
        $pType->setPriceAmount1((int)$split[3]);
        $pType->setPrice1($this->convertPrice($split[4]) / (int)$split[3]);

        $pType->setDiscountGroup($split[5]);

        if (isset($split[6])) {
            $pType->setDiscountKey1a($split[6]);
        }

        if (isset($split[7])) {
            $pType->setDiscount1a($this->convertDiscount($split[6], $split[7]));
        }

        if (isset($split[8])) {
            $pType->setDiscountKey1b($split[8]);
        }

        if (isset($split[9])) {
            $pType->setDiscount1b($this->convertDiscount($split[8], $split[9]));
        }

        if (isset($split[10])) {
            $pType->setDiscountKey1c($split[10]);
        }

        if (isset($split[11])) {
            $pType->setDiscount1c($this->convertDiscount($split[10], $split[11]));
        }

        if (isset($split[12])) {
            $pType->setValidFromDate($split[12]);
        }

        return $pType;
    }

    /**
     * @param string $line
     *
     * @throws InvalidLineException
     *
     * @return DatanormVTypeData
     */
    public function transformToTypeV(string $line): DatanormVTypeData
    {
        $split = explode(';', $line);

        if (count($split) < 15) {
            throw new InvalidLineException('The V-record has not the right amount of columns.');
        }

        /** @var  DatanormVTypeData */
        $vType = new DatanormVTypeData();

        $vType->setDataMark($split[2]);
        $vType->setDate($split[3]);
        $vType->setCurrency($split[4]);
        $vType->setDescription($split[5]);
        $vType->setProducerToken($split[7]);
        $vType->setAdress1($split[8]);
        $vType->setAdress2($split[9]);
        $vType->setAdress3($split[10]);
        $vType->setStreet($split[11]);
        $vType->setCountryId($split[12]);
        $vType->setZip($split[13]);
        $vType->setCity($split[14]);

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

        if (count($split) < 3) {
            throw new InvalidLineException('The B-record has not the right amount of columns.');
        }

        $bType = new DatanormBTypeData();

        $bType->setProcessingFlag($split[1]);
        $bType->setArticleNumber($split[2]);

        if (isset($split[3])) {
            $bType->setArticleNumberNew($split[3]);
        }

        if (isset($split[4])) {
            $bType->setExpirationDate($split[4]);
        }

        return $bType;
    }

    /**
     * @return int
     */
    public function getVersion(): int
    {
        return 5;
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
        if (isset($split[3])) {
            $tType->setIndicator($split[3]);
        }
        if (isset($split[4])) {
            $tType->setLinenumber1($split[4]);
        }
        if (isset($split[5])) {
            $tType->setText1($split[5]);
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
            $dType->setText1($split[5]);
        }
        if (isset($split[6])) {
            $dType->setTextblockNumber1($split[6]);
        }

        return $dType;
    }
}
