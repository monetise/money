<?php
/**
 * Monetise
 *
 * @link        https://github.com/monetise/money
 * @copyright   Copyright (c) 2015, Ripa Club
 * @license     https://github.com/monetise/money/blob/develop/LICENSE
 */
namespace Monetise\Money\DecimalNumber;

/**
 * Class DecimalNumberInterface
 */
interface DecimalNumberInterface
{
    /**
     * @return int
     */
    public function getNumeral();

    /**
     * @param int $numeral
     * @return $this
     */
    public function setNumeral($numeral);

    /**
     * @return int
     */
    public function getFractionDigits();

    /**
     * @param int $fractionDigits
     * @return $this
     */
    public function setFractionDigits($fractionDigits);

    /**
     * @return float
     */
    public function toFloat();
}
