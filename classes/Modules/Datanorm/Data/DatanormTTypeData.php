<?php

declare(strict_types=1);

namespace Xentral\Modules\Datanorm\Data;

final class DatanormTTypeData extends AbstractDatanormTypeData implements DatanormTypeDataInterface
{
    /** @var string $processingFlag */
    private $processingFlag = '';

    /** @var string $textnumber */
    private $textnumber = '';

    /** @var string $indicator */
    private $indicator = '';

    /** @var string $linenumber1 */
    private $linenumber1 = '';

    /** @var string $text1 */
    private $text1 = '';

    /** @var string $linenumber2 */
    private $linenumber2 = '';

    /** @var string $text2 */
    private $text2 = '';

    /**
     * @return string
     */
    public function getProcessingFlag()
    {
        return $this->processingFlag;
    }

    /**
     * @return string
     */
    public function getTextnumber()
    {
        return $this->textnumber;
    }

    /**
     * @return string
     */
    public function getIndicator()
    {
        return $this->indicator;
    }

    /**
     * @return string
     */
    public function getLinenumber1()
    {
        return $this->linenumber1;
    }

    /**
     * @return string
     */
    public function getText1()
    {
        return $this->text1;
    }

    /**
     * @return string
     */
    public function getLinenumber2()
    {
        return $this->linenumber2;
    }

    /**
     * @return string
     */
    public function getText2()
    {
        return $this->text2;
    }

    /**
     * @param string $processingFlag
     */
    public function setProcessingFlag(string $processingFlag): void
    {
        $this->processingFlag = $processingFlag;
    }

    /**
     * @param string $textnumber
     */
    public function setTextnumber(string $textnumber): void
    {
        $this->textnumber = $textnumber;
    }

    /**
     * @param string $indicator
     */
    public function setIndicator(string $indicator): void
    {
        $this->indicator = $indicator;
    }

    /**
     * @param string $linenumber1
     */
    public function setLinenumber1(string $linenumber1): void
    {
        $this->linenumber1 = $linenumber1;
    }

    /**
     * @param string $text1
     */
    public function setText1(string $text1): void
    {
        $this->text1 = $text1;
    }

    /**
     * @param string $linenumber2
     */
    public function setLinenumber2(string $linenumber2): void
    {
        $this->linenumber2 = $linenumber2;
    }

    /**
     * @param string $text2
     */
    public function setText2(string $text2): void
    {
        $this->text2 = $text2;
    }


    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'processingFlag' => $this->processingFlag,
            'textnumber'     => $this->textnumber,
            'indicator'      => $this->indicator,
            'linenumber1'    => $this->linenumber1,
            'text1'          => $this->text1,
            'linenumber2'    => $this->linenumber2,
            'text2'          => $this->text2,
        ];
    }

    /**
     * @param string $data
     */
    public function fillByJson(string $data): void
    {
        $obj = json_decode($data);

        $this->processingFlag = $obj->processingFlag;
        $this->textnumber = $obj->textnumber;
        $this->indicator = $obj->indicator;
        $this->linenumber1 = $obj->linenumber1;
        $this->text1 = $obj->text1;
        $this->linenumber2 = $obj->linenumber2;
        $this->text2 = $obj->text2;
    }
}
