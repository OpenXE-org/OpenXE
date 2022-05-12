<?php

namespace Xentral\Widgets\DataTable\Column;

use Closure;
use DateTime;
use Exception;

class ColumnFormatter
{
    /**
     * @param mixed $ifEmpty
     *
     * @return Closure
     */
    public static function ifEmpty($ifEmpty)
    {
        return static function ($value) use ($ifEmpty) {
            if (empty($value)) {
                return $ifEmpty;
            }

            return $value;
        };
    }

    /**
     * @example Format::sprintf('row_id_%s') %s will be replaced with the current value
     *
     * @param mixed $sprintf
     *
     * @return Closure
     */
    public static function sprintf($sprintf)
    {
        return static function ($value) use ($sprintf) {
            return sprintf($sprintf, $value);
        };
    }

    /**
     * @param string $template
     *
     * @return Closure
     */
    public static function template($template)
    {
        return static function ($value, $rowAssoc) use ($template) {
            $templateVars = [];
            foreach ($rowAssoc as $assocKey => $assocValue) {
                $templateVar = '{' . strtoupper($assocKey) . '}';
                $templateVar = str_replace('-', '_', $templateVar);
                $templateVars[$templateVar] = $assocValue;
            }

            return strtr($template, $templateVars);
        };
    }

    /**
     * @param int    $decimals
     * @param string $decimalSeperator
     * @param string $thousandsSeperator
     *
     * @return Closure
     */
    public static function number($decimals = 2, $decimalSeperator = ',', $thousandsSeperator = '.')
    {
        return static function ($value) use ($decimals, $decimalSeperator, $thousandsSeperator) {
            return number_format($value, $decimals, $decimalSeperator, $thousandsSeperator);
        };
    }

    /**
     * @param int    $decimals
     * @param string $decimalSeperator
     * @param string $thousandsSeperator
     *
     * @return Closure
     */
    public static function bytes($decimals = 1, $decimalSeperator = ',', $thousandsSeperator = '.')
    {
        return static function ($bytes) use ($decimals, $decimalSeperator, $thousandsSeperator) {
            $bytes = (float)$bytes;
            $base = log($bytes, 1024);
            $suffixes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
            $suffixIndex = (int)floor($base);
            $suffix = $suffixes[$suffixIndex];
            $number = pow(1024, $base - floor($base));

            return number_format($number, $decimals, $decimalSeperator, $thousandsSeperator) . ' ' . $suffix;
        };
    }

    /**
     * @param string $dateFormat https://www.php.net/manual/de/function.date.php
     *
     * @return Closure
     */
    public static function date($dateFormat)
    {
        return static function ($dateString) use ($dateFormat) {
            try {
                $date = new DateTime($dateString);

                return $date->format($dateFormat);
            } catch (Exception $exception) {
                return $exception->getMessage();
            }
        };
    }

    /**
     * @return Closure
     */
    public static function htmlEscape()
    {
        return static function ($value) {
            return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        };
    }

    /**
     * @todo Fixen
     *
     * @return Closure
     */
    public static function dump()
    {
        return static function ($value, $row) {
            $data = [
                'value' => $value,
                'row'   => $row,
            ];

            return sprintf(
                '<pre class="dump">%s</pre>',
                htmlspecialchars(var_export($data, true))
            );
        };
    }
}
