<?php
/**
 * Monetise
 *
 * @link        https://github.com/monetise/money
 * @copyright   Copyright (c) 2015, Ripa Club
 * @license     https://github.com/monetise/money/blob/develop/LICENSE
 */
namespace Monetise\Money\Money;

use Monetise\Money\Exception\InvalidArgumentException;

/**
 * Class Currency
 *
 * Holds currency data used in the model layer.
 * Data is not localized, for I18N purpose you should use ICU.
 */
abstract class Currency
{
    /**
     * @var array
     */
    protected static $currencies = [
        'EUR' => [
            'currency_name' => 'Euro',
            'numeric_code' => 978,
            'default_fraction_digits' => 2,
            'sub_unit' => 100,
        ],
        'USD' => [
            'display_name' => 'US Dollar',
            'numeric_code' => 840,
            'default_fraction_digits' => 2,
            'sub_unit' => 100,
        ],
    ];

    /**
     * @param string $currencyCode
     * @throws RuntimeException
     * @return array
     */
    protected static function getCurrencyData($currencyCode)
    {
        if (!isset(static::$currencies[$currencyCode])) {
            throw new InvalidArgumentException(sprintf(
                '"%s" currency is not supported',
                $currencyCode
            ));
        }

        return static::$currencies[$currencyCode];
    }

    /**
     * Returns the ISO 4217 currency name
     *
     * @param string $currencyCode The ISO 4217 currency name
     * @return string
     */
    public static function getCurrencyName($currencyCode)
    {
        return static::getCurrencyData($currencyCode)['currency_name'];
    }

    /**
     * Returns the ISO 4217 numeric code
     *
     * @param string $currencyCode The ISO 4217 alphabetic code
     * @return int
     */
    public static function getNumericCode($currencyCode)
    {
        return static::getCurrencyData($currencyCode)['numeric_code'];
    }

    /**
     * Returns the default number of fraction digits used
     *
     * @param string $currencyCode The ISO 4217 alphabetic code
     * @return integer
     */
    public static function getDefaultFractionDigits($currencyCode)
    {
        return static::getCurrencyData($currencyCode)['default_fraction_digits'];
    }

    /**
     * Returns the sub unit used
     *
     * @param string $currencyCode The ISO 4217 alphabetic code
     * @return integer
     */
    public static function getSubUnit($currencyCode)
    {
        return static::getCurrencyData($currencyCode)['sub_unit'];
    }
}
