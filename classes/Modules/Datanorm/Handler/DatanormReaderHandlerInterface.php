<?php

declare(strict_types=1);

namespace Xentral\Modules\Datanorm\Handler;

use Xentral\Modules\Datanorm\Data\DatanormATypeData;
use Xentral\Modules\Datanorm\Data\DatanormBTypeData;
use Xentral\Modules\Datanorm\Data\DatanormDTypeData;
use Xentral\Modules\Datanorm\Data\DatanormPTypeData;
use Xentral\Modules\Datanorm\Data\DatanormTTypeData;
use Xentral\Modules\Datanorm\Data\DatanormVTypeData;
use Xentral\Modules\Datanorm\Exception\WrongPriceFormatException;

interface DatanormReaderHandlerInterface
{

    /**
     * @param string $line
     *
     * @throws WrongPriceFormatException
     *
     * @return DatanormATypeData
     */
    public function transformToTypeA(string $line): DatanormATypeData;

    /**
     * @param string $line
     *
     * @throws WrongPriceFormatException
     *
     * @return DatanormPTypeData
     */
    public function transformToTypeP(string $line): DatanormPTypeData;

    /**
     * @param string $line
     *
     * @return DatanormVTypeData
     */
    public function transformToTypeV(string $line): DatanormVTypeData;

    /**
     * @param string $line
     *
     * @return DatanormBTypeData
     */
    public function transformToTypeB(string $line): DatanormBTypeData;

    /**
     * @param string $line
     *
     * @return DatanormTTypeData
     */
    public function transformToTypeT(string $line): DatanormTTypeData;

    /**
     * @param string $line
     *
     * @return DatanormDTypeData
     */
    public function transformToTypeD(string $line): DatanormDTypeData;

    /**
     * @return int
     */
    public function getVersion(): int;
}
