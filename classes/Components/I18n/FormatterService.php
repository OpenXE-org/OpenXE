<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Xentral\Components\I18n;

use Xentral\Components\I18n\Formatter\CurrencyFormatter;
use Xentral\Components\I18n\Formatter\FloatFormatter;
use Xentral\Components\I18n\Formatter\FormatterInterface;
use Xentral\Components\I18n\Formatter\FormatterMode;
use Xentral\Components\I18n\Formatter\IntegerFormatter;

/**
 * This service creates formatters for localized input and output.
 *
 * @author   Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
class FormatterService
{
    private string $locale;
    
    
    
    /**
     * Construct a FormatterService object.
     *
     * @param string $locale
     */
    public function __construct(string $locale)
    {
        $this->locale = $locale;
    }
    
    
    
    /**
     * Factory for FormatterInterface objects. There will be a FormatterInterface object for every data type
     * necessary.
     *
     * @param string        $type
     * @param FormatterMode $strictness
     *
     * @return FormatterInterface
     */
    public function factory(string $type, FormatterMode $strictness = FormatterMode::MODE_STRICT): FormatterInterface
    {
        return new $type($this->locale, $strictness);
    }
    
    
    
    /**
     * Shortcut function for creating a FloatFormatter and parsing a user input.
     *
     * @param string        $input
     * @param FormatterMode $strictness
     *
     * @return FloatFormatter
     */
    public function floatFromUserInput(string $input, FormatterMode $strictness=FormatterMode::MODE_NULL): FloatFormatter
    {
        $formatter = new FloatFormatter($this->locale, $strictness);
        $formatter->parseUserInput($input);
        return $formatter;
    }
    
    
    
    /**
     * Shortcut function for creating a FloatFormatter and setting a PHP value.
     *
     * @param string|float|null $input
     * @param FormatterMode     $strictness
     *
     * @return FloatFormatter
     */
    public function floatFromPhpVal(string|null|float $input, FormatterMode $strictness=FormatterMode::MODE_NULL): FloatFormatter
    {
        $formatter = $this->factory(FloatFormatter::class, $strictness);
        $formatter->setPhpVal($input);
        return $formatter;
    }
    
    
    
    /**
     * Replace callback function for \FormActionHandler.
     * Parses and Formats an integer and allows an empty string.
     *
     * @param int   $toDatabase
     * @param mixed $value
     * @param       $fromForm
     *
     * @return mixed|string
     * @see \FormActionHandler
     */
    public function replaceIntegerOrEmpty(int $toDatabase, mixed $value, $fromForm)
    {
        $formatter = new IntegerFormatter($this->locale, FormatterMode::MODE_EMPTY);
        
        
        if (!is_numeric($value) || $fromForm) {
            $formatter->parseUserInput(strval($value));
        } else {
            $formatter->setPhpVal($value);
        }
        
        
        if ($toDatabase) {
            return $formatter->getPhpVal();
        } else {
            return $formatter->formatForUser();
        }
    }
    
    
    
    /**
     * Replace callback function for \FormActionHandler.
     * Parses and Formats a float and allows an empty string.
     * Output shows decimals, if present.
     *
     * @param int   $toDatabase
     * @param mixed $value
     * @param       $fromForm
     *
     * @return mixed|string
     * @see \FormActionHandler
     */
    public function replaceDecimalOrEmpty(int $toDatabase, mixed $value, $fromForm)
    {
        $formatter = new FloatFormatter($this->locale, FormatterMode::MODE_EMPTY);
        
        
        if (!is_numeric($value) || $fromForm) {
            $formatter->parseUserInput(strval($value));
        } else {
            $formatter->setPhpVal(floatval($value));
        }
        
        
        if ($toDatabase) {
            return $formatter->getPhpVal();
        } else {
            return $formatter->formatForUser();
        }
    }
    
    
    
    /**
     * Replace callback function for \FormActionHandler.
     * Parses and Formats a float and allows an empty string.
     * Output always shows at least 7 decimal.
     *
     * @param int   $toDatabase
     * @param mixed $value
     * @param       $fromForm
     *
     * @return mixed|string
     * @see \FormActionHandler
     */
    public function replaceDecimalGeoOrEmpty(int $toDatabase, mixed $value, $fromForm)
    {
        $formatter = new FloatFormatter($this->locale, FormatterMode::MODE_EMPTY);
        $formatter->setMinDigits(7);
        
        
        if (!is_numeric($value) || $fromForm) {
            $formatter->parseUserInput(strval($value));
        } else {
            $formatter->setPhpVal(floatval($value));
        }
        
        
        if ($toDatabase) {
            return $formatter->getPhpVal();
        } else {
            return $formatter->formatForUser();
        }
    }
    
    
    
    /**
     * Replace callback function for \FormActionHandler.
     * Parses and Formats a float and allows an empty string.
     * Output always shows at least 1 decimal.
     *
     * @param int   $toDatabase
     * @param mixed $value
     * @param       $fromForm
     *
     * @return mixed|string
     * @see \FormActionHandler
     */
    public function replaceDecimalPercentOrEmpty(int $toDatabase, mixed $value, $fromForm)
    {
        $formatter = new FloatFormatter($this->locale, FormatterMode::MODE_EMPTY);
        $formatter->setMinDigits(1);
        
        
        if (!is_numeric($value) || $fromForm) {
            $formatter->parseUserInput(strval($value));
        } else {
            $formatter->setPhpVal(floatval($value));
        }
        
        
        if ($toDatabase) {
            return $formatter->getPhpVal();
        } else {
            return $formatter->formatForUser();
        }
    }
    
    
    
    /**
     * Replace callback function for \FormActionHandler.
     * Parses and Formats a float and allows an empty string.
     *
     * @param int   $toDatabase
     * @param mixed $value
     * @param       $fromForm
     *
     * @return mixed|string
     * @see \FormActionHandler
     */
    public function replaceCurrencyOrEmpty(int $toDatabase, mixed $value, $fromForm)
    {
        $formatter = (new FloatFormatter($this->locale, FormatterMode::MODE_EMPTY));
        $formatter->setMinDigits(2);
        
        
        if (!is_numeric($value) || $fromForm) {
            $formatter->parseUserInput(strval($value));
        } else {
            $formatter->setPhpVal(floatval($value));
        }
        
        
        if ($toDatabase) {
            return $formatter->getPhpVal();
        } else {
            return $formatter->formatForUser();
        }
    }
    
    
    
    /**
     * Format a quantity value for output.
     *
     * @param mixed $menge
     *
     * @return string
     */
    public function formatMenge(mixed $menge): string
    {
        $formatter = new FloatFormatter($this->locale, FormatterMode::MODE_EMPTY);
        $formatter->setPhpVal(floatval($menge));
        return $formatter->formatForUser();
    }
    
    
    
    /**
     * Parse a quantity from a form and parse for database input.
     *
     * @param mixed $string
     *
     * @return string|float|null
     */
    public function parseMenge(mixed $string): string|null|float
    {
        $formatter = new FloatFormatter($this->locale, FormatterMode::MODE_EMPTY);
        $formatter->parseUserInput(strval($string));
        return $formatter->getPhpVal();
    }
    
    
    /**
     * Format a price value for output.
     *
     * @param mixed $menge
     *
     * @return string
     */
    public function formatPreis(mixed $menge): string
    {
        $formatter = new FloatFormatter($this->locale, FormatterMode::MODE_EMPTY);
        $formatter->setMinDigits(2);
        $formatter->setPhpVal(floatval($menge));
        return $formatter->formatForUser();
    }
    
    
    
    /**
     * Parse a price from a form and parse for database input.
     *
     * @param mixed $string
     *
     * @return string|float|null
     */
    public function parsePreis(mixed $string): string|null|float
    {
        $formatter = new FloatFormatter($this->locale, FormatterMode::MODE_EMPTY);
        $formatter->setMinDigits(2);
        $formatter->parseUserInput(strval($string));
        return $formatter->getPhpVal();
    }
    
}