<?php

namespace Xentral\Modules\Report\Data;

use JsonSerializable;

class ReportColumn implements JsonSerializable
{
    /** @var string ALIGN_LEFT */
    public const ALIGN_LEFT = 'left';

    /** @var string ALIGN_RIGHT */
    public const ALIGN_RIGHT = 'right';

    /** @var string ALIGN_CENTER */
    public const ALIGN_CENTER = 'center';

    /** @var string ALIGN_JUSTIFY */
    public const ALIGN_JUSTIFY = 'justify';

    /** @var string SORT_NUMERIC */
    public const SORT_NUMERIC = 'numeric';

    /** @var string SORT_ALPHABETIC */
    public const SORT_ALPHABETIC = 'alphabetic';

    /** @var string FORMAT_SUM_MONEY_DE */
    public const FORMAT_SUM_MONEY_DE = 'sum_money_de';

    /** @var string FORMAT_SUM_MONEY_EN */
    public const FORMAT_SUM_MONEY_EN = 'sum_money_en';

    /** @var string FORMAT_DATE_DMY */
    public const FORMAT_DATE_DMY = 'date_dmy';

    /** @var string FORMAT_DATE_YMD */
    public const FORMAT_DATE_YMD = 'date_ymd';

    /** @var string FORMAT_DATE_DMYHIS */
    public const FORMAT_DATE_DMYHIS = 'date_dmyhis';

    /** @var string FORMAT_DATE_YMDHIS */
    public const FORMAT_DATE_YMDHIS = 'date_ymdhis';

    /** @var string FORMAT_CUSTOM */
    public const FORMAT_CUSTOM = 'custom';

    /** @var string $key */
    private $key;

    /** @var string $title */
    private $title;

    /** @var string size */
    private $width;

    /** @var string $alignment */
    private $alignment;

    /** @var bool $isSumColumn */
    private $isSumColumn;

    /** @var int $id */
    private $id;

    /** @var int $sequence */
    private $sequence;

    /** @var string $sorting */
    private $sorting;

    /** @var string|null $formatType */
    private $formatType;

    /** @var string|null $formatStatement */
    private $formatStatement;

    /**
     * @param string      $key
     * @param string      $title
     * @param string      $width
     * @param string      $alignment
     * @param bool        $isSumColumn
     * @param int         $id
     * @param int         $sequence
     * @param string      $sorting
     * @param string|null $formatType
     * @param null        $formatStatement
     */
    public function __construct(
        $key,
        $title,
        $width = '30',
        $alignment = self::ALIGN_LEFT,
        $isSumColumn = false,
        $id = 0,
        $sequence = 0,
        $sorting = 'numeric',
        $formatType = null,
        $formatStatement = null
    ) {
        $this->key = $key;
        $this->title = $title;
        $this->width = $width;
        $this->alignment = $alignment;
        $this->isSumColumn = $isSumColumn;
        $this->id = $id;
        $this->sequence = $sequence;
        $this->sorting = $sorting;
        $this->formatType = $formatType;
        $this->formatStatement = $formatStatement;
    }

    /**
     * @param $data
     *
     * @return ReportColumn|null
     */
    public static function fromDbState($data)
    {
        if (!isset($data['key_name'])) {
            return null;
        }
        if (!isset($data['title'])) {
            return null;
        }
        $key = (string)$data['key_name'];
        $title = (string)$data['title'];
        $width = '';
        if (isset($data['width'])) {
            $width = $data['width'];
        }
        $alignment = '';
        if (isset($data['alignment'])) {
            $alignment = $data['alignment'];
        }
        $sorting = 'numeric';
        if (isset($data['sorting'])) {
            $sorting = $data['sorting'];
        }
        $sequence = 0;
        if (isset($data['sequence'])) {
            $sequence = $data['sequence'];
        }
        $format = null;
        if (array_key_exists('format_type', $data)) {
            $format = $data['format_type'];
        }
        $formatStatement = null;
        if (array_key_exists('format_statement', $data)) {
            $formatStatement = $data['format_statement'];
        }
        $sum = false;
        if (isset($data['sum']) && $data['sum'] === 1) {
            $sum = true;
        }
        $id = 0;
        if (isset($data['id'])) {
            $id = $data['id'];
        }

        return new self(
            $key,
            $title,
            $width,
            $alignment,
            $sum,
            $id,
            $sequence,
            $sorting,
            $format,
            $formatStatement
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'key_name'  => $this->getKey(),
            'title'     => $this->getTitle(),
            'width'     => $this->getWidth(),
            'alignment' => $this->getAlignment(),
            'sorting'   => $this->getSorting(),
            'sum'       => $this->isSumColumn(),
            'id'        => $this->getId(),
            'sequence'  => $this->getSequence(),
            'format_type' => $this->getFormatType(),
            'format_statement' => $this->getFormatStatement(),
        ];
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @return mixed data which can be serialized by <b>json_encode</b>,
     */
    public function jsonSerialize()
    {
        $data = $this->toArray();
        unset($data['id'], $data['sequence']);

        return $data;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @return string
     */
    public function getAlignment()
    {
        return $this->alignment;
    }

    /**
     * @return bool
     */
    public function isSumColumn()
    {
        return $this->isSumColumn;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getSequence()
    {
        return $this->sequence;
    }

    /**
     * @return string
     */
    public function getSorting()
    {
        return $this->sorting;
    }

    /**
     * Changes sequence number and returns old sequence number.
     *
     * @param int $sequence
     *
     * @return int previous sequence number
     */
    public function changeSequence($sequence)
    {
        $oldSequence = $this->getSequence();
        $this->sequence = $sequence;

        return $oldSequence;
    }

    /**
     * @return string|null
     */
    public function getFormatType(): ?string
    {
        return $this->formatType;
    }

    /**
     * @return string|null
     */
    public function getFormatStatement(): ?string
    {
        return $this->formatStatement;
    }
}
