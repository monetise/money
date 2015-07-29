<?php
/**
 * Monetise
 *
 * @link        https://github.com/monetise/money
 * @copyright   Copyright (c) 2015, Ripa Club
 * @license     https://github.com/monetise/money/blob/develop/LICENSE
 */
namespace Monetise\Money\Money;

use Monetise\Money\DecimalNumber\DecimalNumberInterface;
use Monetise\Money\Exception\InvalidArgumentException;
use Monetise\Money\Exception\OverflowException;

/**
 * Trait MoneyTrait
 */
trait MoneyTrait
{
    /**
     * @var int
     */
    protected $amount = 0;

    /**
     * @var string
     */
    protected $currency;

    /**
     * Get the monetary value represented by this object
     *
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set the monetary value represented by this object
     *
     * @param int $amount
     * @return $this
     */
    public function setAmount($amount)
    {
        if ($amount === null) {
            $amount = 0; // TODO: initialization workaround
        } elseif (!is_int($amount)) {
            throw new InvalidArgumentException(sprintf(
                'Amount must be an integer, "%s" given',
                is_object($amount) ? get_class($amount) : gettype($amount)
            ));
        }
        $this->amount = $amount;
        return $this;
    }

    /**
     * @return int
     */
    public function getSubUnit()
    {
        return Currency::getSubUnit($this->currency);
    }

    /**
     * @return int
     */
    public function getFractionDigits()
    {
        return Currency::getDefaultFractionDigits($this->currency);
    }

    /**
     * Get the currency of the monetary value represented by this
     * object
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set the currency of the monetary value represented by this
     * object
     *
     * @param string $currency
     * @return $this
     */
    public function setCurrency($currency)
    {
        if ($currency) {
            $currency = strtoupper($currency);
            Currency::getNumericCode($currency); // ensure currency support
            $this->currency = $currency;
        } else {
            $this->currency = null;
        }

        return $this;
    }

    /**
     * @param MoneyInterface $money
     * @throws InvalidArgumentException
     * @return bool
     */
    public function equalTo(MoneyInterface $money)
    {
        return $this->compareTo($money) === 0;
    }

    /**
     * Compares this object with another
     *
     * Returns an integer less than, equal to, or greater than zero
     * if the value of this MoneyInterface object is considered to be respectively
     * less than, equal to, or greater than the other MoneyInterface object.
     *
     * @param MoneyInterface $money
     * @throws InvalidArgumentException
     * @return int
     */
    public function compareTo(MoneyInterface $money)
    {
        if ($this->getCurrency() !== $money->getCurrency()) {
            throw new InvalidArgumentException('Operations between different currencies are not supported yet.');
        }

        if ($this->getAmount() == $money->getAmount()) {
            return 0;
        }

        return $this->getAmount() < $money->getAmount() ? -1 : 1;
    }

    /**
     * Set the absolute monetary value represented by this object
     *
     * @return $this
     */
    public function abs()
    {
        if ($this->getAmount() < 0) {
            $this->negate();
        }
        return $this;
    }

    /**
     * Set the negated monetary value represented by this object
     *
     * @return $this
     */
    public function negate()
    {
        $this->setAmount(-$this->getAmount());
        return $this;
    }

    /**
     * Add the monetary value of another MoneyInterface object to this object
     *
     * @param MoneyInterface $money
     * @return $this
     */
    public function add(MoneyInterface $money)
    {
        if ($this->getCurrency() !== $money->getCurrency()) {
            throw new InvalidArgumentException('Operations between different currencies are not supported yet.');
        }

        $this->setAmount($this->getAmount() + $money->getAmount());
        return $this;
    }

    /**
     * Subtract the monetary value of another MoneyInterface object from this object
     *
     * @param MoneyInterface $money
     * @return $this
     */
    public function subtract(MoneyInterface $money)
    {
        $negatedMoney = clone $money;
        $negatedMoney->negate();
        $this->add($negatedMoney);
        return $this;
    }

    /**
     * Multiply the monetary value of this object by a given factor
     *
     * @param  float|DecimalNumberInterface   $factor
     * @param  int $roundingMode
     * @return $this
     */
    public function multiply($factor, $roundingMode = PHP_ROUND_HALF_UP)
    {
        if ($factor instanceof DecimalNumberInterface) {
            $factor = $factor->toFloat();
        }

        $this->setAmount($this->toInt(
            round($factor * $this->getAmount(), 0, $roundingMode)
        ));
        return $this;
    }

    /**
     * Convert the current amount to float
     *
     * @return float
     */
    public function toFloat()
    {
        return round($this->getAmount() / $this->getSubUnit(), $this->getFractionDigits());
    }

    /**
     * Populate current amount from a given float value
     *
     * @param float $amount
     * @param string $currency
     * @return $this
     */
    public function fromFloat($amount, $currency = null)
    {
        if ($currency) {
            $this->setCurrency($currency);
        }

        if (!is_float($amount)) {
            throw new InvalidArgumentException(sprintf(
                'Amount must be a float, %s given',
                gettype($amount)
            ));
        }

        $fractionDigits = $this->getFractionDigits();
        $subUnit = $this->getSubUnit();

        $this->setAmount(
            $this->toInt(
                round(
                    $subUnit * round($amount, $fractionDigits, PHP_ROUND_HALF_UP),
                    0,
                    PHP_ROUND_HALF_UP
                )
            )
        );

        return $this;
    }


    /**
     * Raises an exception if the amount is outside of the integer bounds
     *
     * @param  number $amount
     * @return number
     * @throws OverflowException
     */
    protected function assertInsideIntegerBounds($amount)
    {
        if (abs($amount) > PHP_INT_MAX) {
            throw new OverflowException;
        }
    }

    /**
     * Cast an amount to an integer but ensure that the operation won't hide overflow
     *
     * @param number $amount // FIXME: i think number type do not exist
     * @return int
     * @throws OverflowException
     */
    protected function toInt($amount)
    {
        $this->assertInsideIntegerBounds($amount);
        return intval($amount);
    }
}
