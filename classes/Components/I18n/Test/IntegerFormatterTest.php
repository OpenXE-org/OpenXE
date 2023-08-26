<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Xentral\Components\I18n\Test;

require(__DIR__ . '/../../../bootstrap.php');

use Xentral\Components\I18n\Formatter\Exception\TypeErrorException;
use Xentral\Components\I18n\Formatter\FormatterMode;
use Xentral\Components\I18n\Formatter\IntegerFormatter;
use PHPUnit\Framework\TestCase;

class IntegerFormatterTest extends TestCase
{
    public function testStrictModeWithValue()
    {
        $floatFormatter = new IntegerFormatter('de_DE', FormatterMode::MODE_STRICT);
        $output = $floatFormatter->setPhpVal(floatval(1234.56))->formatForUser();
        var_dump($output);
        $this->assertIsString($output);
        $this->assertEquals('1.234', $output);
        
        $output = $floatFormatter->setPhpVal(intval(1234))->formatForUser();
        var_dump($output);
        $this->assertIsString($output);
        $this->assertEquals('1.234', $output);
        
    }
    
    public function testStrictModeWithValueInput()
    {
        $floatFormatter = new IntegerFormatter('de_DE', FormatterMode::MODE_STRICT);
        $output = $floatFormatter->parseUserInput('1234,56')->getPhpVal();
        var_dump($output);
        $this->assertIsInt($output);
        $this->assertEquals(1234, $output);
        
        $output = $floatFormatter->parseUserInput('1234')->getPhpVal();
        var_dump($output);
        $this->assertIsInt($output);
        $this->assertEquals(1234, $output);
        
    }
    
    public function testStrictModeWithNull()
    {
        $floatFormatter = new IntegerFormatter('de_DE', FormatterMode::MODE_STRICT);
        $this->expectException(TypeErrorException::class);
        $output = $floatFormatter->setPhpVal(null)->formatForUser();
    }
    public function testStrictModeWithNullInput()
    {
        $floatFormatter = new IntegerFormatter('de_DE', FormatterMode::MODE_STRICT);
        $this->expectException(TypeErrorException::class);
        $output = $floatFormatter->parseUserInput('')->getPhpVal();
    }
    public function testStrictModeWithEmpty()
    {
        $floatFormatter = new IntegerFormatter('de_DE', FormatterMode::MODE_STRICT);
        $this->expectException(TypeErrorException::class);
        $output = $floatFormatter->setPhpVal('')->formatForUser();
    }
    public function testStrictModeWithEmptyInput()
    {
        $floatFormatter = new IntegerFormatter('de_DE', FormatterMode::MODE_STRICT);
        $this->expectException(TypeErrorException::class);
        $output = $floatFormatter->parseUserInput('')->getPhpVal();
    }
    public function testNullModeWithValue()
    {
        $floatFormatter = new IntegerFormatter('de_DE', FormatterMode::MODE_NULL);
        $output = $floatFormatter->setPhpVal(floatval(1234.56))->formatForUser();
        var_dump($output);
        $this->assertIsString($output);
        $this->assertEquals('1.234', $output);
        
        $output = $floatFormatter->setPhpVal(intval(1234))->formatForUser();
        var_dump($output);
        $this->assertIsString($output);
        $this->assertEquals('1.234', $output);
    }
    
    public function testNullModeWithValueInput()
    {
        $floatFormatter = new IntegerFormatter('de_DE', FormatterMode::MODE_NULL);
        $output = $floatFormatter->parseUserInput('1234,56')->getPhpVal();
        var_dump($output);
        $this->assertIsInt($output);
        $this->assertEquals(1234, $output);
        
        $output = $floatFormatter->parseUserInput('1234')->getPhpVal();
        var_dump($output);
        $this->assertIsInt($output);
        $this->assertEquals(1234, $output);
    }
    
    public function testNullModeWithNull()
    {
        $floatFormatter = new IntegerFormatter('de_DE', FormatterMode::MODE_NULL);
        $output = $floatFormatter->setPhpVal(null)->formatForUser();
        var_dump($output);
        $this->assertIsString($output);
        $this->assertEquals('', $output);
    }
    public function testNullModeWithNullInput()
    {
        $floatFormatter = new IntegerFormatter('de_DE', FormatterMode::MODE_NULL);
        $output = $floatFormatter->parseUserInput('')->getPhpVal();
        var_dump($output);
        $this->assertNull($output);
    }
    public function testNullModeWithEmpty()
    {
        $floatFormatter = new IntegerFormatter('de_DE', FormatterMode::MODE_NULL);
        $this->expectException(TypeErrorException::class);
        $output = $floatFormatter->setPhpVal('')->formatForUser();
    }
    public function testNullModeWithEmptyInput()
    {
        $floatFormatter = new IntegerFormatter('de_DE', FormatterMode::MODE_NULL);
        $output = $floatFormatter->parseUserInput('')->getPhpVal();
        var_dump($output);
        $this->assertNull($output);
    }
    public function testEmptyModeWithValue()
    {
        $floatFormatter = new IntegerFormatter('de_DE', FormatterMode::MODE_EMPTY);
        $output = $floatFormatter->setPhpVal(1234.56)->formatForUser();
        var_dump($output);
        $this->assertIsString($output);
        $this->assertEquals('1.234', $output);
        
        $output = $floatFormatter->setPhpVal(1234)->formatForUser();
        var_dump($output);
        $this->assertIsString($output);
        $this->assertEquals('1.234', $output);
    }
    public function testEmptyModeWithValueInput()
    {
        $floatFormatter = new IntegerFormatter('de_DE', FormatterMode::MODE_EMPTY);
        $output = $floatFormatter->parseUserInput('1234,56')->getPhpVal();
        var_dump($output);
        $this->assertIsInt($output);
        $this->assertEquals(1234, $output);
        
        $output = $floatFormatter->parseUserInput('1234')->getPhpVal();
        var_dump($output);
        $this->assertIsInt($output);
        $this->assertEquals(1234, $output);
    }
    public function testEmptyModeWithNull()
    {
        $floatFormatter = new IntegerFormatter('de_DE', FormatterMode::MODE_EMPTY);
        $this->expectException(TypeErrorException::class);
        $output = $floatFormatter->setPhpVal(null)->formatForUser();
    }
    public function testEmptyModeWithNullInput()
    {
        $floatFormatter = new IntegerFormatter('de_DE', FormatterMode::MODE_EMPTY);
        $output = $floatFormatter->parseUserInput('')->getPhpVal();
        var_dump($output);
        $this->assertIsString($output);
        $this->assertEquals('', $output);
    }
    public function testEmptyModeWithEmpty()
    {
        $floatFormatter = new IntegerFormatter('de_DE', FormatterMode::MODE_EMPTY);
        $output = $floatFormatter->setPhpVal('')->formatForUser();
        var_dump($output);
        $this->assertIsString($output);
        $this->assertEquals('', $output);
    }
    public function testEmptyModeWithEmptyInput()
    {
        $floatFormatter = new IntegerFormatter('de_DE', FormatterMode::MODE_EMPTY);
        $output = $floatFormatter->parseUserInput('')->getPhpVal();
        var_dump($output);
        $this->assertIsString($output);
        $this->assertEquals('', $output);
    }
    public function testCanParseOwnOutput()
    {
        $value = 1234567.89;
        $locale = 'de_DE';
        $floatFormatter1 = new IntegerFormatter($locale);
        $output = $floatFormatter1->setPhpVal($value)->formatForUser();
        var_dump($output);
        $floatFormatter2 = new IntegerFormatter($locale);
        $input = $floatFormatter2->parseUserInput($output)->getPhpVal();
        var_dump($input);
        $this->assertEquals(intval($value), $input);
        
        $locale = 'de_CH';
        $floatFormatter1 = new IntegerFormatter($locale);
        $output = $floatFormatter1->setPhpVal($value)->formatForUser();
        var_dump($output);
        $floatFormatter2 = new IntegerFormatter($locale);
        $input = $floatFormatter2->parseUserInput($output)->getPhpVal();
        var_dump($input);
        $this->assertEquals(intval($value), $input);
        
        $locale = 'de_AT';
        $floatFormatter1 = new IntegerFormatter($locale);
        $output = $floatFormatter1->setPhpVal($value)->formatForUser();
        var_dump($output);
        $floatFormatter2 = new IntegerFormatter($locale);
        $input = $floatFormatter2->parseUserInput($output)->getPhpVal();
        var_dump($input);
        $this->assertEquals(intval($value), $input);
    }
    
    public function testCanSetDigits()
    {
        $value = 1234567.8901234;
        $locale = 'de_DE';
        $floatFormatter1 = new IntegerFormatter($locale);
        $output = $floatFormatter1->setPhpVal($value)->setMinIntDigits(10)->setMaxDigits(4)->formatForUser();
        var_dump($output);
        $this->assertEquals('0.001.234.567', $output);
        
        $floatFormatter1 = new IntegerFormatter($locale);
        $output = $floatFormatter1->setPhpVal($value)->setMaxIntDigits(4)->formatForUser();
        var_dump($output);
        $this->assertEquals('4.567', $output);
        
        $floatFormatter1 = new IntegerFormatter($locale);
        $output = $floatFormatter1->setPhpVal($value)->setMaxDigits(3)->setMinDigits(3)->formatForUser();
        var_dump($output);
        $this->assertEquals('1.234.567', $output);
        
        $floatFormatter1 = new IntegerFormatter($locale);
        $output = $floatFormatter1->setPhpVal($value)->setMaxSignificantDigits(3)->formatForUser();
        var_dump($output);
        $this->assertEquals('1.230.000', $output);
        
        $floatFormatter1 = new IntegerFormatter($locale);
        $output = $floatFormatter1->setPhpVal($value)->setMinSignificantDigits(3)->formatForUser();
        var_dump($output);
        $this->assertEquals('1.234.567', $output);
    }
    
}
