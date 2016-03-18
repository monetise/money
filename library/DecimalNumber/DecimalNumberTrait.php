<?php
/**
 * Monetise
 *
 * @link        https://github.com/monetise/money
 * @copyright   Copyright (c) 2015, Ripa Club
 * @license     https://github.com/monetise/money/blob/develop/LICENSE
 */
namespace Monetise\Money\DecimalNumber;

use Monetise\Money\Exception\InvalidArgumentException;

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
        if ($fractionDigits === null) {
            $fractionDigits = 0;
        } elseif (!is_int($fractionDigits) || $fractionDigits < 0) {
            throw new InvalidArgumentException(
                sprintf(
                    'Fraction digits must be an integer greater than or equal to zero, "%s" given',
                    is_object($fractionDigits) ? get_class($fractionDigits) : gettype($fractionDigits)
                )
            );
        }

        $this->fractionDigits = $fractionDigits;
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
        if ($numeral === null) {
            $numeral = 0;
        } elseif (!is_int($numeral)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Numeral must be an integer, "%s" given',
                    is_object($numeral) ? get_class($numeral) : gettype($numeral)
                )
            );
        }

        $this->numeral = $numeral;
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
