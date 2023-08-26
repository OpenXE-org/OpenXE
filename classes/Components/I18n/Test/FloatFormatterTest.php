<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Xentral\Components\I18n\Test;

require(__DIR__ . '/../../../bootstrap.php');

use Xentral\Components\I18n\Formatter\Exception\TypeErrorException;
use Xentral\Components\I18n\Formatter\FloatFormatter;
use PHPUnit\Framework\TestCase;
use Xentral\Components\I18n\Formatter\FormatterMode;

class FloatFormatterTest extends TestCase
{
    public function testStrictModeWithValue()
    {
        $floatFormatter = new FloatFormatter('de_DE', FormatterMode::MODE_STRICT);
        $output = $floatFormatter->setPhpVal(floatval(1234.56))->formatForUser();
        var_dump($output);
        $this->assertIsString($output);
        $this->assertEquals('1.234,56', $output);
        
        $output = $floatFormatter->setPhpVal(intval(1234))->formatForUser();
        var_dump($output);
        $this->assertIsString($output);
        $this->assertEquals('1.234', $output);
    }
    
    
    
    public function testStrictModeWithValueInput()
    {
        $floatFormatter = new FloatFormatter('de_DE', FormatterMode::MODE_STRICT);
        $output = $floatFormatter->parseUserInput('1234,56')->getPhpVal();
        var_dump($output);
        $this->assertIsFloat($output);
        $this->assertEquals(1234.56, $output);
        
        $output = $floatFormatter->parseUserInput('1234')->getPhpVal();
        var_dump($output);
        $this->assertIsFloat($output);
        $this->assertEquals(1234, $output);
    }
    
    
    
    public function testStrictModeWithValueInputNoDecimals()
    {
        $floatFormatter = new FloatFormatter('de_DE', FormatterMode::MODE_STRICT);
        $output = $floatFormatter->parseUserInput('1234')->getPhpVal();
        var_dump($output);
        $this->assertIsFloat($output);
    }
    
    
    
    public function testStrictModeWithNull()
    {
        $floatFormatter = new FloatFormatter('de_DE', FormatterMode::MODE_STRICT);
        $this->expectException(TypeErrorException::class);
        $output = $floatFormatter->setPhpVal(null)->formatForUser();
    }
    
    
    
    public function testStrictModeWithNullInput()
    {
        $floatFormatter = new FloatFormatter('de_DE', FormatterMode::MODE_STRICT);
        $this->expectException(TypeErrorException::class);
        $output = $floatFormatter->parseUserInput('')->getPhpVal();
    }
    
    
    
    public function testStrictModeWithEmpty()
    {
        $floatFormatter = new FloatFormatter('de_DE', FormatterMode::MODE_STRICT);
        $this->expectException(TypeErrorException::class);
        $output = $floatFormatter->setPhpVal('')->formatForUser();
    }
    
    
    
    public function testStrictModeWithEmptyInput()
    {
        $floatFormatter = new FloatFormatter('de_DE', FormatterMode::MODE_STRICT);
        $this->expectException(TypeErrorException::class);
        $output = $floatFormatter->parseUserInput('')->getPhpVal();
    }
    
    
    
    public function testNullModeWithValue()
    {
        $floatFormatter = new FloatFormatter('de_DE', FormatterMode::MODE_NULL);
        $output = $floatFormatter->setPhpVal(floatval(1234.56))->formatForUser();
        var_dump($output);
        $this->assertIsString($output);
        $this->assertEquals('1.234,56', $output);
        
        $output = $floatFormatter->setPhpVal(intval(1234))->formatForUser();
        var_dump($output);
        $this->assertIsString($output);
        $this->assertEquals('1.234', $output);
    }
    
    
    
    public function testNullModeWithValueInput()
    {
        $floatFormatter = new FloatFormatter('de_DE', FormatterMode::MODE_NULL);
        $output = $floatFormatter->parseUserInput('1234,56')->getPhpVal();
        var_dump($output);
        $this->assertIsFloat($output);
        $this->assertEquals(1234.56, $output);
        
        $output = $floatFormatter->parseUserInput('1234')->getPhpVal();
        var_dump($output);
        $this->assertIsFloat($output);
        $this->assertEquals(1234, $output);
    }
    
    
    
    public function testNullModeWithNull()
    {
        $floatFormatter = new FloatFormatter('de_DE', FormatterMode::MODE_NULL);
        $output = $floatFormatter->setPhpVal(null)->formatForUser();
        var_dump($output);
        $this->assertIsString($output);
        $this->assertEquals('', $output);
    }
    
    
    
    public function testNullModeWithNullInput()
    {
        $floatFormatter = new FloatFormatter('de_DE', FormatterMode::MODE_NULL);
        $output = $floatFormatter->parseUserInput('')->getPhpVal();
        var_dump($output);
        $this->assertNull($output);
    }
    
    
    
    public function testNullModeWithEmpty()
    {
        $floatFormatter = new FloatFormatter('de_DE', FormatterMode::MODE_NULL);
        $this->expectException(TypeErrorException::class);
        $output = $floatFormatter->setPhpVal('')->formatForUser();
    }
    
    
    
    public function testNullModeWithEmptyInput()
    {
        $floatFormatter = new FloatFormatter('de_DE', FormatterMode::MODE_NULL);
        $output = $floatFormatter->parseUserInput('')->getPhpVal();
        var_dump($output);
        $this->assertNull($output);
    }
    
    
    
    public function testEmptyModeWithValue()
    {
        $floatFormatter = new FloatFormatter('de_DE', FormatterMode::MODE_EMPTY);
        $output = $floatFormatter->setPhpVal(1234.56)->formatForUser();
        var_dump($output);
        $this->assertIsString($output);
        $this->assertEquals('1.234,56', $output);
        
        $output = $floatFormatter->setPhpVal(1234)->formatForUser();
        var_dump($output);
        $this->assertIsString($output);
        $this->assertEquals('1.234', $output);
    }
    
    
    
    public function testEmptyModeWithValueInput()
    {
        $floatFormatter = new FloatFormatter('de_DE', FormatterMode::MODE_EMPTY);
        $output = $floatFormatter->parseUserInput('1234,56')->getPhpVal();
        var_dump($output);
        $this->assertIsFloat($output);
        $this->assertEquals(1234.56, $output);
        
        $output = $floatFormatter->parseUserInput('1234')->getPhpVal();
        var_dump($output);
        $this->assertIsFloat($output);
        $this->assertEquals(1234, $output);
    }
    
    
    
    public function testEmptyModeWithNull()
    {
        $floatFormatter = new FloatFormatter('de_DE', FormatterMode::MODE_EMPTY);
        $this->expectException(TypeErrorException::class);
        $output = $floatFormatter->setPhpVal(null)->formatForUser();
    }
    
    
    
    public function testEmptyModeWithNullInput()
    {
        $floatFormatter = new FloatFormatter('de_DE', FormatterMode::MODE_EMPTY);
        $output = $floatFormatter->parseUserInput('')->getPhpVal();
        var_dump($output);
        $this->assertIsString($output);
        $this->assertEquals('', $output);
    }
    
    
    
    public function testEmptyModeWithEmpty()
    {
        $floatFormatter = new FloatFormatter('de_DE', FormatterMode::MODE_EMPTY);
        $output = $floatFormatter->setPhpVal('')->formatForUser();
        var_dump($output);
        $this->assertIsString($output);
        $this->assertEquals('', $output);
    }
    
    
    
    public function testEmptyModeWithEmptyInput()
    {
        $floatFormatter = new FloatFormatter('de_DE', FormatterMode::MODE_EMPTY);
        $output = $floatFormatter->parseUserInput('')->getPhpVal();
        var_dump($output);
        $this->assertIsString($output);
        $this->assertEquals('', $output);
    }
    
    
    
    public function testCanParseOwnOutput()
    {
        $value = 1234567.89;
        $locale = 'de_DE';
        $floatFormatter1 = new FloatFormatter($locale);
        $output = $floatFormatter1->setPhpVal($value)->formatForUser();
        var_dump($output);
        $floatFormatter2 = new FloatFormatter($locale);
        $input = $floatFormatter2->parseUserInput($output)->getPhpVal();
        var_dump($input);
        $this->assertEquals($value, $input);
        
        $locale = 'de_CH';
        $floatFormatter1 = new FloatFormatter($locale);
        $output = $floatFormatter1->setPhpVal($value)->formatForUser();
        var_dump($output);
        $floatFormatter2 = new FloatFormatter($locale);
        $input = $floatFormatter2->parseUserInput($output)->getPhpVal();
        var_dump($input);
        $this->assertEquals($value, $input);
        
        $locale = 'de_AT';
        $floatFormatter1 = new FloatFormatter($locale);
        $output = $floatFormatter1->setPhpVal($value)->formatForUser();
        var_dump($output);
        $floatFormatter2 = new FloatFormatter($locale);
        $input = $floatFormatter2->parseUserInput($output)->getPhpVal();
        var_dump($input);
        $this->assertEquals($value, $input);
    }
    
    
    
    public function testCanSetDigits()
    {
        $value = 1234567.8901234;
        $locale = 'de_DE';
        $floatFormatter1 = new FloatFormatter($locale);
        $output = $floatFormatter1->setPhpVal($value)->setMaxDigits(4)->formatForUser();
        var_dump($output);
        $this->assertEquals('1.234.567,8901', $output);
        
        $floatFormatter1 = new FloatFormatter($locale);
        $output = $floatFormatter1->setPhpVal($value)->setMaxDigits(3)->formatForUser();
        var_dump($output);
        $this->assertEquals('1.234.567,89', $output);
        
        $floatFormatter1 = new FloatFormatter($locale);
        $output = $floatFormatter1->setPhpVal($value)->setMaxDigits(3)->setMinDigits(3)->formatForUser();
        var_dump($output);
        $this->assertEquals('1.234.567,890', $output);
        
        $floatFormatter1 = new FloatFormatter($locale);
        $output = $floatFormatter1->setPhpVal($value)->setMinDigits(10)->formatForUser();
        var_dump($output);
        $this->assertEquals('1.234.567,8901234000', $output);
        
        $floatFormatter1 = new FloatFormatter($locale);
        $output = $floatFormatter1->setPhpVal($value)->setMaxDigits(0)->formatForUser();
        var_dump($output);
        $this->assertEquals('1.234.568', $output);
    }
    
}
