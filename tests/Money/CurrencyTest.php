<?php
/**
 * Monetise Tests
 *
 * @link        https://github.com/monetise/money
 * @copyright   Copyright (c) 2015, Ripa Club
 * @license     https://github.com/monetise/money/blob/develop/LICENSE
 */
namespace Monetise\MoneyTest\Money;

use Monetise\Money\Exception\InvalidArgumentException;
use Monetise\Money\Exception\UnexpectedValueException;
use Monetise\Money\Money\Currency;

/**
 * Class CurrencyTest
 *
 * @group money
 */
class CurrencyTest extends \PHPUnit_Framework_TestCase
{
    public function testGetCurrencyData()
    {
        $refl = new \ReflectionClass(Currency::class);
        $method = $refl->getMethod('getCurrencyData');
        $method->setAccessible(true);
        $property = $refl->getProperty('currencies');
        $property->setAccessible(true);
        $currencies = $property->getValue();

        $this->assertSame($currencies['EUR'], $method->invoke(null, 'EUR'));

        $property->setAccessible(false);
        $method->setAccessible(false);
    }


    public function testGetCurrencyDataShouldThrowInvalidArgumentExceptionForNotSupportedCurrencies()
    {
        $refl = new \ReflectionClass(Currency::class);
        $method = $refl->getMethod('getCurrencyData');
        $method->setAccessible(true);
        $currencyCode = 'ABCDEFG';

        try {
            $method->invoke(null, $currencyCode);
        } catch (InvalidArgumentException $exc) {
            $this->assertInstanceOf(InvalidArgumentException::class, $exc);
            $this->assertSame(sprintf('"%s" currency is not supported', $currencyCode), $exc->getMessage());
        }
        $method->setAccessible(false);
    }

    /**
     * Currency data provider
     *
     * @return array
     */
    public function getCurrencies()
    {
        $refl = new \ReflectionClass(Currency::class);
        $property = $refl->getProperty('currencies');
        $property->setAccessible(true);
        $currencies = $property->getValue();
        $property->setAccessible(false);

        $data = [];
        foreach ($currencies as $iso4127alpha3 => $currency) {
            $row = [$iso4127alpha3, $currency];
            $data[] = $row;
        }
        return $data;
    }

    /**
     * @dataProvider getCurrencies
     * @param $code
     * @param $expected
     */
    public function testAllGetters($code, $expected)
    {
        $map = [
            'display_name' => 'getCurrencyName',
            'default_fraction_digits' => 'getDefaultFractionDigits',
            'numeric_code' => 'getNumericCode',
            'sub_unit' => 'getSubUnit',
        ];
        foreach ($map as $key => $method) {
            $this->assertSame(
                $expected[$key],
                Currency::$method($code)
            );
        }
    }

    public function testNotIntegerDefaultFractionDigitsShouldThrowUnexpectedValueException()
    {
        $refl = new \ReflectionClass(Currency::class);
        $property = $refl->getProperty('currencies');
        $property->setAccessible(true);
        $originalCurrencies = $property->getValue();
        $newCurrencies = [
            'ABC' => [
                'display_name' => 'ABC fake currency',
                'numeric_code' => 123,
                'default_fraction_digits' => 'dfd',
                'sub_unit' => 2,
            ],
        ];
        $property->setValue($newCurrencies);

        try {
            Currency::getDefaultFractionDigits('ABC');
            $this->fail(UnexpectedValueException::class . ' not thrown');
        } catch (UnexpectedValueException $exc) {
            $this->assertInstanceOf(UnexpectedValueException::class, $exc);
            $this->assertSame(
                'The currency default fraction digits value must be an integer; "string" given',
                $exc->getMessage()
            );
        }
        $property->setValue($originalCurrencies);
        $property->setAccessible(false);
    }

    public function testDefaultFractionDigitsLessThanZeroShouldThrowUnexpectedValueException()
    {
        $refl = new \ReflectionClass(Currency::class);
        $property = $refl->getProperty('currencies');
        $property->setAccessible(true);
        $originalCurrencies = $property->getValue();
        $newCurrencies = [
            'ABC' => [
                'display_name' => 'ABC fake currency',
                'numeric_code' => 123,
                'default_fraction_digits' => -1,
                'sub_unit' => 100,
            ],
        ];
        $property->setValue($newCurrencies);

        try {
            Currency::getDefaultFractionDigits('ABC');
            $this->fail(UnexpectedValueException::class . ' not thrown');
        } catch (UnexpectedValueException $exc) {
            $this->assertInstanceOf(UnexpectedValueException::class, $exc);
            $this->assertSame(
                sprintf(
                    'The currency default fraction digits value must be greater than 0; "%s" given',
                    $newCurrencies['ABC']['default_fraction_digits']
                ),
                $exc->getMessage()
            );
        }
        $property->setValue($originalCurrencies);
        $property->setAccessible(false);
    }

    public function testNotIntegerSubUnitsShouldThrowUnexpectedValueException()
    {
        $refl = new \ReflectionClass(Currency::class);
        $property = $refl->getProperty('currencies');
        $property->setAccessible(true);
        $originalCurrencies = $property->getValue();
        $newCurrencies = [
            'ABC' => [
                'display_name' => 'ABC fake currency',
                'numeric_code' => 123,
                'default_fraction_digits' => 2,
                'sub_unit' => 'abc',
            ],
        ];
        $property->setValue($newCurrencies);

        try {
            Currency::getSubUnit('ABC');
            $this->fail(UnexpectedValueException::class . ' not thrown');
        } catch (UnexpectedValueException $exc) {
            $this->assertInstanceOf(UnexpectedValueException::class, $exc);
            $this->assertSame('The currency sub-units value must be an integer; "string" given', $exc->getMessage());
        }
        $property->setValue($originalCurrencies);
        $property->setAccessible(false);
    }

    public function testSubUnitsLessThanOneShouldThrowUnexpectedValueException()
    {
        $refl = new \ReflectionClass(Currency::class);
        $property = $refl->getProperty('currencies');
        $property->setAccessible(true);
        $originalCurrencies = $property->getValue();
        $newCurrencies = [
            'ABC' => [
                'display_name' => 'ABC fake currency',
                'numeric_code' => 123,
                'default_fraction_digits' => 2,
                'sub_unit' => -1,
            ],
        ];
        $property->setValue($newCurrencies);

        try {
            Currency::getSubUnit('ABC');
            $this->fail(UnexpectedValueException::class . ' not thrown');
        } catch (UnexpectedValueException $exc) {
            $this->assertInstanceOf(UnexpectedValueException::class, $exc);
            $this->assertSame(
                sprintf(
                    'The currency sub-units value must be greater than 1; "%s" given',
                    $newCurrencies['ABC']['sub_unit']
                ),
                $exc->getMessage()
            );
        }
        $property->setValue($originalCurrencies);
        $property->setAccessible(false);
    }
}
