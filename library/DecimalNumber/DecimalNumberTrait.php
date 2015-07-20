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
 * Class DecimalNumberTrait
 */
trait DecimalNumberTrait
{
    /**
     * @var int
     */
    protected $numeral = 0;

    /**
     * @var int
     */
    protected $fractionDigits = 0;

    /**
     * @return int
     */
    public function getFractionDigits()
    {
        return $this->fractionDigits;
    }

    /**
     * @param int $fractionDigits
     * @return $this
     */
    public function setFractionDigits($fractionDigits)
    {
        $this->fractionDigits = (int) $fractionDigits;
        return $this;
    }

    /**
     * @return int
     */
    public function getNumeral()
    {
        return $this->numeral;
    }

    /**
     * @param int $numeral
     * @return $this
     */
    public function setNumeral($numeral)
    {
        $this->numeral = (int) $numeral;
        return $this;
    }

    /**
     * @return float
     */
    public function toFloat()
    {
        return round($this->numeral / pow(10, $this->fractionDigits), $this->fractionDigits);
    }
}
