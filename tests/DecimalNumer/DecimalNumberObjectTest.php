<?php
/**
 * Monetise Tests
 *
 * @link        https://github.com/monetise/money
 * @copyright   Copyright (c) 2015, Ripa Club
 * @license     https://github.com/monetise/money/blob/develop/LICENSE
 */
namespace Monetise\MoneyTest\DecimalNumber;

use Monetise\Money\DecimalNumber\DecimalNumberObject;
use Monetise\Money\DecimalNumber\DecimalNumberInterface;

/**
 * Class DecimalNumberObjectTest
 *
 * @group decimalnumber
 */
class DecimalNumberObjectTest extends \PHPUnit_Framework_TestCase
{

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

        $decimalNumber->setNumeral("11");
        $this->assertSame(11, $decimalNumber->getNumeral());
    }

    public function testSetGetFractionDigits()
    {
        $decimalNumber = new DecimalNumberObject();

        // Default
        $this->assertSame(0, $decimalNumber->getFractionDigits());

        $this->assertSame($decimalNumber, $decimalNumber->setFractionDigits(33));
        $this->assertSame(33, $decimalNumber->getFractionDigits());
        $this->assertInternalType('int', $decimalNumber->getFractionDigits());

        $decimalNumber->setFractionDigits("11");
        $this->assertSame(11, $decimalNumber->getFractionDigits());
    }

    public function testToFloat()
    {
        $decimalNumber = new DecimalNumberObject();

        $decimalNumber->setNumeral(1234);
        $decimalNumber->setFractionDigits(2);
        $this->assertSame(12.34, $decimalNumber->toFloat());
    }
}
