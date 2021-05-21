<?php

declare(strict_types=1);

namespace Xentral\Modules\Datanorm\Data;

final class DatanormDTypeData extends AbstractDatanormTypeData implements DatanormTypeDataInterface
{

    /** @var string $processingFlag */
    private $processingFlag = '';

    /** @var string $articleNumber */
    private $articleNumber = '';

    /** @var string $lineNumber1 */
    private $lineNumber1 = '';

    /** @var string $textIndicator1 */
    private $textIndicator1 = '';

    /** @var string $text1 */
    private $text1 = '';

    /** @var string $textblockNumber1 */
    private $textblockNumber1 = '';

    /** @var string $lineNumber2 */
    private $lineNumber2 = '';

    /** @var string $textIndicator2 */
    private $textIndicator2 = '';

    /** @var string $text2 */
    private $text2 = '';

    /** @var string $textblockNumber2 */
    private $textblockNumber2 = '';

    /**
     * @return string
     */
    public function getProcessingFlag(): string
    {
        return $this->processingFlag;
    }

    /**
     * @return string
     */
    public function getArticleNumber(): string
    {
        return $this->articleNumber;
    }

    /**
     * @return string
     */
    public function getLineNumber1(): string
    {
        return $this->lineNumber1;
    }

    /**
     * @return string
     */
    public function getTextIndicator1(): string
    {
        return $this->textIndicator1;
    }

    /**
     * @return string
     */
    public function getText1(): string
    {
        return $this->text1;
    }

    /**
     * @return string
     */
    public function getTextblockNumber1(): string
    {
        return $this->textblockNumber1;
    }

    /**
     * @return string
     */
    public function getLineNumber2(): string
    {
        return $this->lineNumber2;
    }

    /**
     * @return string
     */
    public function getTextIndicator2(): string
    {
        return $this->textIndicator2;
    }

    /**
     * @return string
     */
    public function getText2(): string
    {
        return $this->text2;
    }

    /**
     * @return string
     */
    public function getTextblockNumber2(): string
    {
        return $this->textblockNumber2;
    }

    /**
     * @param string $processingFlag
     */
    public function setProcessingFlag($processingFlag): void
    {
        $this->processingFlag = $processingFlag;
    }

    /**
     * @param string $articleNumber
     */
    public function setArticleNumber(string $articleNumber): void
    {
        $this->articleNumber = $articleNumber;
    }

    /**
     * @param string $lineNumber1
     */
    public function setLineNumber1(string $lineNumber1): void
    {
        $this->lineNumber1 = $lineNumber1;
    }

    /**
     * @param string $textIndicator1
     */
    public function setTextIndicator1(string $textIndicator1): void
    {
        $this->textIndicator1 = $textIndicator1;
    }

    /**
     * @param string $text1
     */
    public function setText1(string $text1): void
    {
        $this->text1 = $text1;
    }

    /**
     * @param string $textblockNumber1
     */
    public function setTextblockNumber1(string $textblockNumber1): void
    {
        $this->textblockNumber1 = $textblockNumber1;
    }

    /**
     * @param string $lineNumber2
     */
    public function setLineNumber2(string $lineNumber2): void
    {
        $this->lineNumber2 = $lineNumber2;
    }

    /**
     * @param string $textIndicator2
     */
    public function setTextIndicator2(string $textIndicator2): void
    {
        $this->textIndicator2 = $textIndicator2;
    }

    /**
     * @param string $text2
     */
    public function setText2(string $text2): void
    {
        $this->text2 = $text2;
    }

    /**
     * @param string $textblockNumber2
     */
    public function setTextblockNumber2(string $textblockNumber2): void
    {
        $this->textblockNumber2 = $textblockNumber2;
    }


    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'processingFlag'   => $this->processingFlag,
            'articleNumber'    => $this->articleNumber,
            'lineNumber1'      => $this->lineNumber1,
            'textIndicator1'   => $this->textIndicator1,
            'text1'            => $this->text1,
            'textblockNumber1' => $this->textblockNumber1,
            'lineNumber2'      => $this->lineNumber2,
            'textIndicator2'   => $this->textIndicator2,
            'text2'            => $this->text2,
            'textblockNumber2' => $this->textblockNumber2,
        ];
    }

    /**
     * @param string $data
     */
    public function fillByJson(string $data): void
    {
        $obj = json_decode($data);

        $this->processingFlag = $obj->processingFlag;
        $this->articleNumber = $obj->articleNumber;
        $this->lineNumber1 = $obj->lineNumber1;
        $this->textIndicator1 = $obj->textIndicator1;
        $this->text1 = $obj->text1;
        $this->textblockNumber1 = $obj->textblockNumber1;
        $this->lineNumber2 = $obj->lineNumber2;
        $this->textIndicator2 = $obj->textIndicator2;
        $this->text2 = $obj->text2;
        $this->textblockNumber2 = $obj->textblockNumber2;
    }
}
