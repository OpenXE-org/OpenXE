<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Xentral\Components\I18n\Formatter;

use Vtiful\Kernel\Format;
use Xentral\Components\I18n\Formatter\Exception\TypeErrorException;

/**
 * AbstractFormatter.
 *
 * @author   Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
abstract class AbstractFormatter implements FormatterInterface
{
    private string $locale;
    private FormatterMode $strictness;
    private mixed $parsedValue;
    
    
    
    public function __construct(string $locale, FormatterMode $strictness = FormatterMode::MODE_STRICT)
    {
        $this->locale = $locale;
        $this->strictness = $strictness;
        $this->init();
    }
    
    
    
    /**
     * Initialize the formatter. Overload this instead of the constructor.
     *
     * @return void
     */
    protected function init(): void
    {
    }
    
    
    
    /**
     * Set the native PHP value in the formatter.
     * The value must ALWAYS be of the requested type or an Exception is thrown.
     *
     * @param mixed $input
     *
     * @return self
     */
    protected function setParsedValue($value): self
    {
        $this->parsedValue = $value;
        return $this;
    }
    
    
    
    /**
     * Set the native PHP value in the formatter.
     * The value must ALWAYS be of the requested type (or NULL or '' depending on the strictness).
     *
     * @param mixed $input
     *
     * @return self
     */
    public function setPhpVal(mixed $input): self
    {
        if ($this->isStrictValidPhpVal($input)
            || $this->isEmptyValidPhpValue($input)
            || $this->isNullValidPhpValue($input)) {
            $this->parsedValue = $input;
            return $this;
        } else {
            throw new TypeErrorException(
                "Value " . var_export($input, true) . " is not a valid type for " . get_class(
                    $this
                ) . " with strictness {$this->getStrictness()->name}"
            );
        }
    }
    
    
    
    /**
     * Get the native PHP value from the formatter.
     * The value must ALWAYS be of the requested type or an Exception is thrown.
     *
     * @return mixed
     */
    public function getPhpVal(): mixed
    {
        return $this->parsedValue;
    }
    
    
    
    /**
     * Return the locale used for output formatting.
     *
     * @return string
     */
    protected function getLocale(): string
    {
        return $this->locale;
    }
    
    
    
    /**
     * Return the current strictness mode.
     *
     * @return FormatterMode
     * @see FormatterMode
     *
     */
    public function getStrictness(): FormatterMode
    {
        return $this->strictness;
    }
    
    
    
    /**
     * Check if $input conforms to the MODE_NULL strictness AND strictness is set.
     *
     * @param mixed $input
     *
     * @return bool
     */
    protected function isNullValidPhpValue(mixed $input): bool
    {
        return ($this->getStrictness() == FormatterMode::MODE_NULL) && ($input === null);
    }
    
    
    
    /**
     * Check if $input conforms to the MODE_EMPTY strictness AND strictness is set.
     *
     * @param mixed $input
     *
     * @return bool
     */
    protected function isEmptyValidPhpValue(mixed $input): bool
    {
        return ($this->getStrictness() == FormatterMode::MODE_EMPTY) && is_string($input) && (trim($input) === '');
    }
    
    
    
    /**
     * Clean up input string.
     *
     * @param string $string
     *
     * @return string
     */
    protected function sanitizeInputString(string $string): string
    {
        return trim($string);
    }
    
}