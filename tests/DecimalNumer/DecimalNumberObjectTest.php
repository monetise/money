<?php
/**
 * Monetise Tests
 *
 * @link        https://github.com/monetise/money
 * @copyright   Copyright (c) 2015, Ripa Club
 * @license     https://github.com/monetise/money/blob/develop/LICENSE
 */
namespace Monetise\MoneyTest\DecimalNumber;

use Monetise\Money\DecimalNumber\DecimalNumberInterface;
use Monetise\Money\DecimalNumber\DecimalNumberObject;
use Monetise\Money\Exception\InvalidArgumentException;

/**
 * Class DecimalNumberObjectTest
 *
 * @group decimalnumber
 */
class DecimalNumberObjectTest extends \PHPUnit_Framework_TestCase
{

    public function testCtor()
    {
        $decimalNumber = new DecimalNumberObject();
        $this->assertSame(0, $decimalNumber->getNumeral());
        $this->assertSame(0, $decimalNumber->getFractionDigits());
    
        $decimalNumber = new DecimalNumberObject(111);
        $this->assertSame(111, $decimalNumber->getNumeral());
        $this->assertSame(0, $decimalNumber->getFractionDigits());
    
        $decimalNumber = new DecimalNumberObject(1000, 2);
        $this->assertSame(1000, $decimalNumber->getNumeral());
        $this->assertSame(2, $decimalNumber->getFractionDigits());
    }
    
    public function testImplementsInterface()
    {
        $this->assertInstanceOf(DecimalNumberInterface::class, new DecimalNumberObject());
    }

    public function testSetGetNumeral()
    {
        $decimalNumber = new DecimalNumberObject();

        // Default
        $this->assertSame(0, $decimalNumber->getNumeral());

        $this->assertSame($decimalNumber, $decimalNumber->setNumeral(33));
        $this->assertSame(33, $decimalNumber->getNumeral());
        $this->assertInternalType('int', $decimalNumber->getNumeral());
        
        $this->assertSame($decimalNumber, $decimalNumber->setNumeral(null));
        $this->assertSame(0, $decimalNumber->getNumeral());
        $this->assertInternalType('int', $decimalNumber->getNumeral());

        $this->setExpectedException(InvalidArgumentException::class);
        $decimalNumber->setNumeral("11");
    }

    public function testSetGetFractionDigits()
    {
        $decimalNumber = new DecimalNumberObject();

        // Default
        $this->assertSame(0, $decimalNumber->getFractionDigits());

        $this->assertSame($decimalNumber, $decimalNumber->setFractionDigits(33));
        $this->assertSame(33, $decimalNumber->getFractionDigits());
        $this->assertInternalType('int', $decimalNumber->getFractionDigits());
        
        $this->assertSame($decimalNumber, $decimalNumber->setFractionDigits(null));
        $this->assertSame(0, $decimalNumber->getFractionDigits());
        $this->assertInternalType('int', $decimalNumber->getFractionDigits());

        $this->setExpectedException(InvalidArgumentException::class);
        $decimalNumber->setFractionDigits("11");
    }

    public function testToFloat()
    {
        $decimalNumber = new DecimalNumberObject();

        $decimalNumber->setNumeral(1234);
        $decimalNumber->setFractionDigits(2);
        $this->assertSame(12.34, $decimalNumber->toFloat());
    }
}
