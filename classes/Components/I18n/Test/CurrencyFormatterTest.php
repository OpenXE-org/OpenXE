<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Xentral\Components\I18n\Test;

require(__DIR__ . '/../../../bootstrap.php');

use Xentral\Components\I18n\Formatter\CurrencyFormatter;
use PHPUnit\Framework\TestCase;
use Xentral\Components\I18n\Formatter\FormatterMode;

class CurrencyFormatterTest extends TestCase
{
    public function testStrictModeWithValue()
    {
        $floatFormatter = new CurrencyFormatter('de_DE', FormatterMode::MODE_STRICT);
        $output = $floatFormatter->setPhpVal(floatval(1234.56))->formatForUser();
        var_dump($output);
        $this->assertIsString($output);
        $this->assertEquals('1.234,56 EUR', $output);
        
        $floatFormatter = new CurrencyFormatter('de_DE', FormatterMode::MODE_STRICT);
        $output = $floatFormatter->setPhpVal(intval(1234))->formatForUser();
        var_dump($output);
        $this->assertIsString($output);
        $this->assertEquals('1.234 EUR', $output);
        
        $floatFormatter = new CurrencyFormatter('de_CH', FormatterMode::MODE_STRICT);
        $output = $floatFormatter->setPhpVal(floatval(1234.56))->formatForUser();
        var_dump($output);
        $this->assertIsString($output);
        $this->assertEquals('CHF 1’234.56', $output);
    }
    
    
    
    public function testStrictModeWithValueWithoutCCY()
    {
        $floatFormatter = new CurrencyFormatter('de_CH', FormatterMode::MODE_STRICT);
        $floatFormatter->hideCurrency();
        $output = $floatFormatter->setPhpVal(floatval(1234.56))->formatForUser();
        var_dump($output);
        $this->assertIsString($output);
        $this->assertEquals('1’234.56', $output);
    }
    
    
    
    public function testStrictModeWithValueInput()
    {
        $floatFormatter = new CurrencyFormatter('de_DE', FormatterMode::MODE_STRICT);
        $output = $floatFormatter->parseUserInput('1234,56 EUR')->getPhpVal();
        var_dump($output);
        $this->assertIsFloat($output);
        $this->assertEquals(1234.56, $output);
        
        $output = $floatFormatter->parseUserInput('EUR 1234')->getPhpVal();
        var_dump($output);
        $this->assertIsFloat($output);
        $this->assertEquals(1234, $output);
    }
    
    
    
    public function testStrictModeWithValueInputWithoutCCY()
    {
        $floatFormatter = (new CurrencyFormatter('de_DE', FormatterMode::MODE_STRICT))->hideCurrency();
        $output = $floatFormatter->parseUserInput('1234,56')->getPhpVal();
        var_dump($output);
        $this->assertIsFloat($output);
        $this->assertEquals(1234.56, $output);
        
        $floatFormatter = (new CurrencyFormatter('de_DE', FormatterMode::MODE_STRICT))->hideCurrency();
        $output = $floatFormatter->parseUserInput('1234')->getPhpVal();
        var_dump($output);
        $this->assertIsFloat($output);
        $this->assertEquals(1234, $output);
    }
    
    
    
    public function testCanParseOwnOutput()
    {
        $value = 1234567.89;
        $locale = 'de_DE';
        $floatFormatter1 = new CurrencyFormatter($locale);
        $output = $floatFormatter1->setPhpVal($value)->formatForUser();
        var_dump($output);
        $floatFormatter2 = new CurrencyFormatter($locale);
        $input = $floatFormatter2->parseUserInput($output)->getPhpVal();
        var_dump($input);
        $this->assertEquals($value, $input);
        
        $locale = 'de_CH';
        $floatFormatter1 = new CurrencyFormatter($locale);
        $output = $floatFormatter1->setPhpVal($value)->formatForUser();
        var_dump($output);
        $floatFormatter2 = new CurrencyFormatter($locale);
        $input = $floatFormatter2->parseUserInput($output)->getPhpVal();
        var_dump($input);
        $this->assertEquals($value, $input);
        
        $locale = 'de_AT';
        $floatFormatter1 = new CurrencyFormatter($locale);
        $output = $floatFormatter1->setPhpVal($value)->formatForUser();
        var_dump($output);
        $floatFormatter2 = new CurrencyFormatter($locale);
        $input = $floatFormatter2->parseUserInput($output)->getPhpVal();
        var_dump($input);
        $this->assertEquals($value, $input);
    }
    
    
    
    public function testCanParseOwnOutputWithoutCCY()
    {
        $value = 1234567.89;
        $locale = 'de_DE';
        $floatFormatter1 = new CurrencyFormatter($locale);
        $floatFormatter1->hideCurrency();
        $output = $floatFormatter1->setPhpVal($value)->formatForUser();
        var_dump($output);
        $floatFormatter2 = new CurrencyFormatter($locale);
        $floatFormatter2->hideCurrency();
        $input = $floatFormatter2->parseUserInput($output)->getPhpVal();
        var_dump($input);
        $this->assertEquals($value, $input);
        
        $locale = 'de_CH';
        $floatFormatter1 = (new CurrencyFormatter($locale))->hideCurrency();
        $output = $floatFormatter1->setPhpVal($value)->formatForUser();
        var_dump($output);
        $floatFormatter2 = new CurrencyFormatter($locale);
        $floatFormatter2->hideCurrency();
        $input = $floatFormatter2->parseUserInput($output)->getPhpVal();
        var_dump($input);
        $this->assertEquals($value, $input);
        
        $locale = 'de_AT';
        $floatFormatter1 = (new CurrencyFormatter($locale))->hideCurrency();
        $output = $floatFormatter1->setPhpVal($value)->formatForUser();
        $floatFormatter1->hideCurrency();
        var_dump($output);
        $floatFormatter2 = (new CurrencyFormatter($locale))->hideCurrency();
        $input = $floatFormatter2->parseUserInput($output)->getPhpVal();
        var_dump($input);
        $this->assertEquals($value, $input);
    }
    
}
