<?php

namespace Xentral\Modules\Api\Dashboard;

use Xentral\Modules\Api\Exception\InvalidArgumentException;

class WidgetData
{
    /** @var string WIDGET_TYPE_SIMPLE */
    const WIDGET_TYPE_SIMPLE = 'simple';
    /** @var string WIDGET_TYPE_SIMPLE_BIG */
    const WIDGET_TYPE_SIMPLE_BIG = 'simple_big';
    /** @var string WIDGET_TYPE_CONTRAST */
    const WIDGET_TYPE_CONTRAST = 'contrast';
    /** @var string WIDGET_CONTRAST_BIG */
    const WIDGET_TYPE_CONTRAST_BIG = 'contrast_big';
    /** @var string WIDGET_TYPE_BARCHART */
    const WIDGET_TYPE_BARCHART = 'barchart';

    /** @var string WIDGET_TREND_RISE */
    const WIDGET_TREND_RISE = 'rise';
    /** @var string WIDGET_TREND_FALL */
    const WIDGET_TREND_FALL = 'fall';
    /** @var string WIDGET_TREND_EQUAL */
    const WIDGET_TREND_EQUAL = 'equal';
    /** @var string WIDGET_TREND_NONE */
    const WIDGET_TREND_NONE = 'none';

    /** @var string FORMAT_TEXT */
    const FORMAT_TEXT = 'text';
    /** @var string FORMAT_CURRENCY */
    const FORMAT_CURRENCY = 'currency';
    /** @var string FORMAT_DECIMAL */
    const FORMAT_DECIMAL = 'decimal';
    /** @var string FORMAT_HOURS */
    const FORMAT_HOURS = 'hours';

    /** @var array $formats */
    private static $formats = [self::FORMAT_TEXT, self::FORMAT_CURRENCY, self::FORMAT_DECIMAL, self::FORMAT_HOURS];
    /** @var string $name */
    protected $name;
    /** @var string $type */
    protected $type;
    /** @var string $label */
    protected $label;
    /** @var array $value */
    protected $value;
    /** @var string $context */
    protected $context;
    /** @var array $format */
    protected $format;
    /**@var string $valueUnit */
    private $valueUnit;

    /**
     * WidgetData constructor.
     *
     * @param string $name
     * @param string $type
     * @param string $label
     * @param array  $value
     * @param string $context
     * @param string $valueUnit
     * @param string $format
     */
    public function __construct($name, $type, $label, $value, $context, $valueUnit = '', $format = self::FORMAT_TEXT)
    {
        $this->name = $name;
        $this->type = $type;
        $this->label = $label;
        $this->value = $value;
        $this->context = $context;
        $this->valueUnit = $valueUnit;
        $this->setFormat($format);
    }

    /**
     * @param string $name
     *
     * @return WidgetData
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param string $label
     *
     * @return WidgetData
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @param string $format
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public function setFormat($format)
    {
        if (!in_array($format, self::$formats, true)) {
            throw new InvalidArgumentException(sprintf('Unknown format "%s".', $format));
        }
        $this->format = $format;
    }

    /**
     * @return array formatted Value(s)
     */
    public function getFormattedValue()
    {
        if (empty($this->value)) {
            return [];
        }

        $result = $this->value;
        switch ($this->format) {

            case self::FORMAT_TEXT:
                foreach ($result as $key => &$val) {
                    if(is_array($val)) {
                        $val = implode(',', $val);
                    } else {
                        $val = (string)$val;
                    }
                }
                unset($val);
                break;

            case self::FORMAT_CURRENCY:
                foreach ($result as $key => &$val) {
                    if (is_numeric($val)) {
                        $val = number_format($val, 2, ',', '.');
                    } else {
                        $val = (string)$val;
                    }
                }
                unset($val);
                break;

            case self::FORMAT_DECIMAL:
                foreach ($result as $key => &$val) {
                    if (is_numeric($val)) {
                        $val = number_format($val, 2, ',', '');
                    } else {
                        $val = (string)$val;
                    }
                }
                unset($val);
                break;

            case self::FORMAT_HOURS:
                foreach ($result as $key => &$val) {
                    if (is_numeric($val)) {
                        $min = $val * 60;
                        $hours = floor($min / 60);
                        $min %= 60;
                        $val = sprintf('%02dh %02dm', $hours, $min);
                    } else {
                        $val = (string)$val;
                    }
                }
                unset($val);
                break;

            default:
                $result = [];
        }

        return $result;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        if ($this->type === self::WIDGET_TYPE_CONTRAST || $this->type === self::WIDGET_TYPE_CONTRAST_BIG) {
            $trend = $this->getContrastTrend();
            $this->value['trend'] = $trend;
        }

        return [
            'name'           => $this->name,
            'type'           => $this->type,
            'label'          => $this->label,
            'value'          => $this->value,
            'formattedValue' => $this->getFormattedValue(),
            'valueUnit'      => $this->valueUnit,
            'format'         => $this->format,
            'context'        => $this->context,
        ];
    }

    /**
     * @return string
     */
    private function getContrastTrend()
    {
        if (!isset($this->value['current'], $this->value['previous'])) {
            return self::WIDGET_TREND_NONE;
        }
        if ($this->value['current'] > $this->value['previous']) {
            return self::WIDGET_TREND_RISE;
        }
        if ($this->value['current'] < $this->value['previous']) {
            return self::WIDGET_TREND_FALL;
        }

        return self::WIDGET_TREND_EQUAL;
    }
}
