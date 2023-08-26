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
     * @param string $type
     *
     * @return FormatterInterface
     */
    public function factory(string $type): FormatterInterface
    {
        return new $type($this->locale);
    }
    
    
    
    /**
     * Shortcut function for creating a FloatFormatter and parsing a user input.
     *
     * @param string $input
     *
     * @return FloatFormatter
     */
    public function floatFromUserInput(string $input): FloatFormatter
    {
        $formatter = new FloatFormatter($this->locale);
        $formatter->parseUserInput($input);
        return $formatter;
    }
    
    
    
    /**
     * Shortcut function for creating a FloatFormatter and setting a PHP value.
     *
     * @param float $input
     *
     * @return FloatFormatter
     */
    public function floatFromPhpVal(float $input): FloatFormatter
    {
        $formatter = $this->factory(FloatFormatter::class);
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
    
}