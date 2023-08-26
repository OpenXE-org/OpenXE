<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Xentral\Components\I18n\Formatter;

use Xentral\Components\I18n\Formatter\FloatFormatter;

class IntegerFormatter extends FloatFormatter
{
    
    
    protected function init(): void
    {
        $this->parseType = \NumberFormatter::TYPE_INT64;
        parent::init();
        parent::setMaxDigits(0);
    }
    
    
    
    /**
     * For IntegerFormatter, strip decimals from $input if a number is given.
     *
     * @param mixed $input
     *
     * @return AbstractFormatter
     */
    public function setPhpVal(mixed $input): AbstractFormatter
    {
        return parent::setPhpVal(is_numeric($input) ? intval($input) : $input);
    }
    
    
    
    /**
     * Check if $input is an integer.
     *
     * @param $input
     *
     * @return bool
     */
    public function isStrictValidPhpVal($input): bool
    {
        return is_integer($input);
    }
    
    
    
    /**
     * Don't allow setting of min digits.
     *
     * @param int $digits
     *
     * @return \Xentral\Components\I18n\Formatter\FloatFormatter
     */
    public function setMinDigits(int $digits): \Xentral\Components\I18n\Formatter\FloatFormatter
    {
        return $this;
    }
    
    
    
    /**
     * Don't allow setting of max digits.
     *
     * @param int $digits
     *
     * @return \Xentral\Components\I18n\Formatter\FloatFormatter
     */
    
    public function setMaxDigits(int $digits): \Xentral\Components\I18n\Formatter\FloatFormatter
    {
        return $this;
    }
    
    
}