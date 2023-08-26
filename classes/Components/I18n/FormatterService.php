<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Xentral\Components\I18n;

use Xentral\Components\I18n\Formatter\FloatFormatter;
use Xentral\Components\I18n\Formatter\FormatterInterface;

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
    
    
}