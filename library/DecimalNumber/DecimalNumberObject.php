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
 * Class DecimalNumberObject
 */
class DecimalNumberObject implements DecimalNumberInterface
{
    use DecimalNumberTrait;
    
    public function __construct($numeral = null, $fractionDigits = null)
    {
        if (null !== $numeral) {
            $this->setNumeral($numeral);
        }
        
        if (null !== $fractionDigits) {
            $this->setFractionDigits($fractionDigits);
        }
    }
}
