<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Xentral\Components\I18n\Test;

require(__DIR__ . '/../../../bootstrap.php');


use Xentral\Components\I18n\Formatter\FloatFormatter;
use Xentral\Components\I18n\FormatterService;
use PHPUnit\Framework\TestCase;

class FormatterServiceTest extends TestCase
{
    public function testCanCreateFormatterService()
    {
        $formatterService = new FormatterService('de_DE');
        $this->assertInstanceOf(FormatterService::class, $formatterService);
    }
    
    
    
    public function testFloatFromUserInput()
    {
        $formatterService = new FormatterService('de_DE');
        /** @var FloatFormatter $floatFormatter */
        $floatFormatter = $formatterService->floatFromUserInput("1.234,56");
        $phpVal = $floatFormatter->getPhpVal();
        $this->assertIsFloat($phpVal);
        $this->assertEquals(1234.56, $phpVal);
        
        $formatterService = new FormatterService('de_DE');
        /** @var FloatFormatter $floatFormatter */
        $floatFormatter = $formatterService->floatFromUserInput("1234,56");
        $phpVal = $floatFormatter->getPhpVal();
        $this->assertIsFloat($phpVal);
        $this->assertEquals(1234.56, $phpVal);
        
        $formatterService = new FormatterService('de_DE');
        /** @var FloatFormatter $floatFormatter */
        $floatFormatter = $formatterService->floatFromUserInput("1.234.567,89");
        $phpVal = $floatFormatter->getPhpVal();
        $this->assertIsFloat($phpVal);
        $this->assertEquals(1234567.89, $phpVal);
        
        $formatterService = new FormatterService('de_DE');
        /** @var FloatFormatter $floatFormatter */
        $floatFormatter = $formatterService->floatFromUserInput("1234567,89");
        $phpVal = $floatFormatter->getPhpVal();
        $this->assertIsFloat($phpVal);
        $this->assertEquals(1234567.89, $phpVal);
        
        
        $formatterService = new FormatterService('de_CH');
        /** @var FloatFormatter $floatFormatter */
        $floatFormatter = $formatterService->floatFromUserInput("1'234.56");
        $phpVal = $floatFormatter->getPhpVal();
        $this->assertIsFloat($phpVal);
        $this->assertEquals(1234.56, $phpVal);
        
        $formatterService = new FormatterService('de_CH');
        /** @var FloatFormatter $floatFormatter */
        $floatFormatter = $formatterService->floatFromUserInput("1234.56");
        $phpVal = $floatFormatter->getPhpVal();
        $this->assertIsFloat($phpVal);
        $this->assertEquals(1234.56, $phpVal);
        
        $formatterService = new FormatterService('de_CH');
        /** @var FloatFormatter $floatFormatter */
        $floatFormatter = $formatterService->floatFromUserInput("1'234'567.89");
        $phpVal = $floatFormatter->getPhpVal();
        $this->assertIsFloat($phpVal);
        $this->assertEquals(1234567.89, $phpVal);
        
        $formatterService = new FormatterService('de_CH');
        /** @var FloatFormatter $floatFormatter */
        $floatFormatter = $formatterService->floatFromUserInput("1234567.89");
        $phpVal = $floatFormatter->getPhpVal();
        $this->assertIsFloat($phpVal);
        $this->assertEquals(1234567.89, $phpVal);
        
        
        $formatterService = new FormatterService('de_AT');
        /** @var FloatFormatter $floatFormatter */
        $floatFormatter = $formatterService->floatFromUserInput("1 234,56");
        $phpVal = $floatFormatter->getPhpVal();
        $this->assertIsFloat($phpVal);
        $this->assertEquals(1234.56, $phpVal);
        
        $formatterService = new FormatterService('de_AT');
        /** @var FloatFormatter $floatFormatter */
        $floatFormatter = $formatterService->floatFromUserInput("1234,56");
        $phpVal = $floatFormatter->getPhpVal();
        $this->assertIsFloat($phpVal);
        $this->assertEquals(1234.56, $phpVal);
        
        $formatterService = new FormatterService('de_AT');
        /** @var FloatFormatter $floatFormatter */
        $floatFormatter = $formatterService->floatFromUserInput("1 234 567,89");
        $phpVal = $floatFormatter->getPhpVal();
        $this->assertIsFloat($phpVal);
        $this->assertEquals(1234567.89, $phpVal);
        
        $formatterService = new FormatterService('de_AT');
        /** @var FloatFormatter $floatFormatter */
        $floatFormatter = $formatterService->floatFromUserInput("1234567,89");
        $phpVal = $floatFormatter->getPhpVal();
        $this->assertIsFloat($phpVal);
        $this->assertEquals(1234567.89, $phpVal);
    }
    
    
    
    public function testFloatFromPhpVal()
    {
        $formatterService = new FormatterService('de_DE');
        /** @var FloatFormatter $floatFormatter */
        $floatFormatter = $formatterService->floatFromPhpVal(1234567.89);
        $formattedString = $floatFormatter->formatForUser();
        $this->assertIsString($formattedString);
        $this->assertEquals("1.234.567,89", $formattedString);
        
        
        $formatterService = new FormatterService('de_CH');
        /** @var FloatFormatter $floatFormatter */
        $floatFormatter = $formatterService->floatFromPhpVal(1234567.89);
        $formattedString = $floatFormatter->formatForUser();
        $this->assertIsString($formattedString);
        $this->assertEquals("1’234’567.89", $formattedString);
        
        
        $formatterService = new FormatterService('de_AT');
        /** @var FloatFormatter $floatFormatter */
        $floatFormatter = $formatterService->floatFromPhpVal(1234567.89);
        $formattedString = $floatFormatter->formatForUser();
        $this->assertIsString($formattedString);
        $this->assertEquals("1 234 567,89", $formattedString);
    }
}
