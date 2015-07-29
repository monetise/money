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
 * Interface MoneyInterface
 *
 * @see http://martinfowler.com/eaaCatalog/money.html
 * @see https://github.com/sebastianbergmann/money
 */
interface MoneyInterface
{
    /**
     * Get the monetary value represented by this object
     *
     * @return int
     */
    public function getAmount();

    /**
     * Set the monetary value represented by this object
     *
     * @param int $amount
     * @return $this
     */
    public function setAmount($amount);

    /**
     * Retrieve the number of fraction digits
     *
     * This information has to be used only for float conversions.
     *
     * @return int
     */
    public function getFractionDigits();

    /**
     * Retrieve the number of sub-units
     *
     * @return int
     */
    public function getSubUnit();

    /**
     * Get the currency of the monetary value represented by this object
     *
     * @return string
     */
    public function getCurrency();

    /**
     * Set the currency of the monetary value represented by this object
     *
     * @param string $currency
     * @return $this
     */
    public function setCurrency($currency);

    /**
     * Check if this object is equal to another
     *
     * @param MoneyInterface $money
     * @return bool
     */
    public function equalTo(MoneyInterface $money);

    /**
     * Compare this object with another
     *
     * It returns an integer less than, equal to, or greater than zero
     * if the value of this MoneyInterface object is considered to be respectively
     * less than, equal to, or greater than the other MoneyInterface object.
     *
     * @param MoneyInterface $money
     * @throws InvalidArgumentException
     * @return int
     */
    public function compareTo(MoneyInterface $money);

    /**
     * Set the absolute monetary value represented by this object
     *
     * @return $this
     */
    public function abs();

    /**
     * Set the negated monetary value represented by this object
     *
     * @return $this
     */
    public function negate();

    /**
     * Add the monetary value of another MoneyInterface object to this object
     *
     * @param MoneyInterface $money
     * @return $this
     */
    public function add(MoneyInterface $money);

    /**
     * Subtract the monetary value of another MoneyInterface object from this object
     *
     * @param MoneyInterface $money
     * @return $this
     */
    public function subtract(MoneyInterface $money);

    /**
     * Multiply the monetary value of this object by a given factor
     *
     * @param  float   $factor
     * @param  int $roundingMode
     * @return $this
     */
    public function multiply($factor, $roundingMode = PHP_ROUND_HALF_UP);

    /**
     * Get current monetary value converted to float
     *
     * @return float
     */
    public function toFloat();

    /**
     * Set current monetary value from a float
     *
     * @param float $amount
     * @param string $currency
     * @return $this
     */
    public function fromFloat($amount, $currency = null);
}
