<?php
/**
 * Monetise Tests
 *
 * @link        https://github.com/monetise/money
 * @copyright   Copyright (c) 2015, Ripa Club
 * @license     https://github.com/monetise/money/blob/develop/LICENSE
 */
namespace Monetise\MoneyTest\Money;

use Monetise\Money\DecimalNumber\DecimalNumberObject;
use Monetise\Money\Exception\InvalidArgumentException;
use Monetise\Money\Exception\OverflowException;
use Monetise\Money\Money\MoneyInterface;
use Monetise\Money\Money\MoneyObject;
use Monetise\Money\Money\Zero;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\Stdlib\Hydrator\HydratorAwareInterface;
use Zend\Stdlib\Hydrator\ObjectProperty;

/**
 * Class MoneyObjectTest
 *
 * @group money
 */
class MoneyObjectTest extends \PHPUnit_Framework_TestCase
{
    public function testImplementsInterface()
    {
        $this->assertInstanceOf(MoneyInterface::class, new MoneyObject);
    }
    
    public function testCtor()
    {
        $money = new MoneyObject();
        $this->assertSame(0, $money->getAmount());
        $this->assertNull($money->getCurrency());
        
        $money = new MoneyObject(111);
        $this->assertSame(111, $money->getAmount());
        $this->assertNull($money->getCurrency());
        
        $money = new MoneyObject(1000, 'USD');
        $this->assertSame(1000, $money->getAmount());
        $this->assertSame('USD', $money->getCurrency());
    }

    public function testGetHydrator()
    {
        $money = new MoneyObject;
        $this->assertInstanceOf(HydratorAwareInterface::class, $money);

        // Test default
        /** @var $defaultHydrator ClassMethods */
        $defaultHydrator = $money->getHydrator();
        $this->assertInstanceOf(ClassMethods::class, $defaultHydrator);
        $this->assertTrue($defaultHydrator->getUnderscoreSeparatedKeys(), 'Asserting underscore separated keys');

        $anotherHydrator = new ObjectProperty();
        $money->setHydrator($anotherHydrator);
        $this->assertSame($anotherHydrator, $money->getHydrator());
    }

    public function testSetGetAmount()
    {
        $money = new MoneyObject;

        // Default
        $this->assertSame(0, $money->getAmount());

        $this->assertSame($money, $money->setAmount(33));
        $this->assertSame(33, $money->getAmount());
        $this->assertInternalType('int', $money->getAmount());

        $this->assertSame($money, $money->setAmount(null));
        $this->assertSame(0, $money->getAmount());
        $this->assertInternalType('int', $money->getAmount());
    }

    public function testSetNotIntegerAmountShouldThrowException()
    {
        $money = new MoneyObject;

        $this->setExpectedException(InvalidArgumentException::class);
        $money->setAmount('11');
    }

    public function testSetGetCurrency()
    {
        $money = new MoneyObject;

        // Default
        $this->assertNull($money->getCurrency());

        $this->assertSame($money, $money->setCurrency('EUR'));
        $this->assertSame('EUR', $money->getCurrency());

        $this->assertSame($money, $money->setCurrency(null));
        $this->assertNull($money->getCurrency());
    }

    public function testEqualTo()
    {
        $a = (new MoneyObject())->setAmount(100)->setCurrency('EUR');
        $b = (new MoneyObject())->setAmount(100)->setCurrency('EUR');
        $c = (new MoneyObject())->setAmount(50)->setCurrency('EUR');

        $this->assertTrue($a->equalTo($b));
        $this->assertTrue($b->equalTo($a));
        $this->assertFalse($a->equalTo($c));
        $this->assertFalse($c->equalTo($a));
        $this->assertFalse($b->equalTo($c));
        $this->assertFalse($c->equalTo($b));

        $d = (new MoneyObject())->setAmount(50)->setCurrency('USD');
        $this->setExpectedException(InvalidArgumentException::class);
        $d->equalTo($a);
    }

    public function testCompareTo()
    {
        $a = (new MoneyObject())->setAmount(100)->setCurrency('EUR');
        $b = (new MoneyObject())->setAmount(200)->setCurrency('EUR');

        $this->assertEquals(-1, $a->compareTo($b));
        $this->assertEquals(1, $b->compareTo($a));
        $this->assertEquals(0, $a->compareTo($a));

        $c = (new MoneyObject())->setAmount(50)->setCurrency('USD');
        $this->setExpectedException(InvalidArgumentException::class);
        $c->equalTo($a);
    }


    public function testToFloat()
    {
        $money = new MoneyObject;

        $money->setAmount(1234);
        $money->setCurrency('EUR');
        $this->assertSame(12.34, $money->toFloat());
    }

    public function testAbs()
    {
        $money = new MoneyObject;
        $money->setCurrency('EUR');

        $testValue = -10;
        $money->setAmount($testValue);
        $this->assertSame($money, $money->setAmount($testValue)->abs());
        $this->assertSame(abs($testValue), $money->getAmount());

        $testValue = 10;
        $money->setAmount($testValue);
        $this->assertSame($money, $money->setAmount($testValue)->abs());
        $this->assertSame(abs($testValue), $money->getAmount());
    }

    public function testNegate()
    {
        $money = new MoneyObject;
        $money->setCurrency('EUR');

        $testValue = 100;
        $money->setAmount($testValue);
        $this->assertSame($money, $money->setAmount($testValue)->negate());
        $this->assertSame(-$testValue, $money->getAmount());

        $testValue = -10;
        $money->setAmount($testValue);
        $this->assertSame($money, $money->setAmount($testValue)->negate());
        $this->assertSame(-$testValue, $money->getAmount());
    }

    public function testAdd()
    {
        $a = (new MoneyObject)->setAmount(1)->setCurrency('EUR');
        $b = (new MoneyObject)->setAmount(2)->setCurrency('EUR');

        $this->assertSame($a, $a->add($b));

        $this->assertEquals(2, $b->getAmount());
        $this->assertEquals(3, $a->getAmount());

        $c = (new MoneyObject)->setAmount(50)->setCurrency('USD');
        $this->setExpectedException(InvalidArgumentException::class);
        $a->add($c);
    }

    public function testSubtract()
    {
        $a = (new MoneyObject)->setAmount(3)->setCurrency('EUR');
        $b = (new MoneyObject)->setAmount(2)->setCurrency('EUR');

        $this->assertSame($a, $a->subtract($b));

        $this->assertEquals(2, $b->getAmount());
        $this->assertEquals(1, $a->getAmount());

        $c = (new MoneyObject)->setAmount(50)->setCurrency('USD');
        $this->setExpectedException(InvalidArgumentException::class);
        $a->add($c);
    }

    public function testMultiplyFloat()
    {
        $a = (new MoneyObject)->setAmount(3)->setCurrency('EUR');

        $this->assertSame($a, $a->multiply(2));
        $this->assertSame(6, $a->getAmount());
    }

    public function testMultiplyDecimalNumber()
    {
        $a = (new MoneyObject)->setAmount(100)->setCurrency('EUR');
        $dm = (new DecimalNumberObject())->setNumeral(5)->setFractionDigits(1);

        $this->assertSame($a, $a->multiply($dm));
        $this->assertSame(50, $a->getAmount());
    }

    public function testFromFloat()
    {
        $money = (new MoneyObject)->setAmount(1234)->setCurrency('EUR');
        $monetFromFloat = (new MoneyObject())->fromFloat(12.34, 'EUR');
        $this->assertEquals($money, $monetFromFloat);

        $monetFromFloat = (new MoneyObject)->setCurrency('EUR')->fromFloat(12.34);
        $this->assertEquals($money, $monetFromFloat);

        $this->setExpectedException(InvalidArgumentException::class);
        (new MoneyObject())->fromFloat('12.34');
    }

    public function testMultiplyShouldThrowExceptionForIntegerOverflow()
    {
        $a = (new MoneyObject)->setAmount(PHP_INT_MAX)->setCurrency('USD');

        $this->setExpectedException(OverflowException::class);
        $a->multiply(2);
    }

    public function testFromFloatShouldThrowExceptionForIntegerOverflow()
    {
        $this->setExpectedException(OverflowException::class);
        $a = (new MoneyObject)->fromFloat((float) PHP_INT_MAX + 1, 'USD');
    }
}
