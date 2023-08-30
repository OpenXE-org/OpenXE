<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Xentral\Components\I18n\Formatter;

use Xentral\Components\I18n\Bootstrap;
use Xentral\Components\I18n\Formatter\FloatFormatter;

class CurrencyFormatter extends FloatFormatter
{
    private string $ccy;
    private bool $showCcy = true;
    
    
    
    protected function init(): void
    {
        $this->parseType = \NumberFormatter::TYPE_CURRENCY;
        $this->formatterStyle = \NumberFormatter::CURRENCY;
        parent::init();
        
        $parsedLocale = \Locale::parseLocale($this->getLocale());
        $this->setCcy(
            Bootstrap::findRegion($parsedLocale['region'])[\Xentral\Components\I18n\Iso3166\Key::CURRENCY_CODE] ?? ''
        );
        
        // Set text representation for currency, not the symbol
        $this->getNumberFormatter()->setSymbol(\NumberFormatter::CURRENCY_SYMBOL, $this->getCcy());
    }
    
    
    
    /**
     * Return the currency.
     *
     * @return string
     */
    public function getCcy(): string
    {
        return $this->ccy;
    }
    
    
    
    /**
     * Set the currency. Currency is also automatically set by locale, if class is
     * instantiated.
     *
     * @param string $ccy
     *
     * @return $this
     */
    public function setCcy(string $ccy): self
    {
        $this->ccy = $ccy;
        return $this;
    }
    
    
    
    /**
     * Don't show currency in formatted output and don't expect currency in string to parse.
     *
     * @return $this
     */
    public function hideCurrency(): self
    {
        $this->showCcy = false;
        $this->parseType = \NumberFormatter::TYPE_DOUBLE;
        return $this;
    }
    
    
    
    /**
     * Show currency in formatted output and expect it in string to parse.
     *
     * @return $this
     */
    public function showCurrency(): self
    {
        $this->showCcy = true;
        $this->parseType = \NumberFormatter::TYPE_CURRENCY;
        return $this;
    }
    
    
    
    protected function sanitizeInputString(string $string): string
    {
        return $this->showCcy
            ? trim($string)
            : parent::sanitizeInputString($string);
    }
    
    
    
    /**
     * Return a string that can be used in an SQL query to format the value for presentation to a User.
     * Should return the same string as if it was formatted by FormatterInterface::formatForUser(), but directly from
     * the database.
     * This function does not need a native PHP value, but a table column is needed.
     *
     * @TODO This function is not complete. It does not add a currency symbol.
     *
     * @param string $col
     *
     * @return string
     * @deprecated
     *
     */
    public function formatForUserWithSqlStatement(string $col): string
    {
        return parent::formatForUserWithSqlStatement($col);
    }
    
    
    
    protected function parse(string $input, ?\NumberFormatter $numberFormatter = null): false|float|int
    {
        if (!$this->showCcy) {
            // If currency is not shown, create a new \NumberFormatter with style DECIMAL
            $numberFormatter = new \NumberFormatter(
                $this->getNumberFormatter()->getLocale(),
                \NumberFormatter::DECIMAL,
                $this->getNumberFormatter()->getPattern()
            );
            return parent::parse($input, $numberFormatter);
        }
        
        if ($numberFormatter === null) {
            $numberFormatter = $this->getNumberFormatter();
        }
        return $numberFormatter->parseCurrency($input, $this->ccy);
    }
    
    
    
    protected function format(float|int $phpVal, ?\NumberFormatter $numberFormatter = null): false|string
    {
        if (!$this->showCcy) {
            // If currency is not shown, create a new \NumberFormatter with style DECIMAL
            $numberFormatter = new \NumberFormatter(
                $this->getNumberFormatter()->getLocale(),
                \NumberFormatter::DECIMAL,
                $this->getNumberFormatter()->getPattern()
            );
            return parent::format($phpVal, $numberFormatter);
        }
        
        if ($numberFormatter === null) {
            $numberFormatter = $this->getNumberFormatter();
        }
        return $numberFormatter->formatCurrency($phpVal, $this->ccy);
    }
    
    
}