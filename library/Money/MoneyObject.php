<?php
/**
 * Monetise
 *
 * @link        https://github.com/monetise/money
 * @copyright   Copyright (c) 2015, Ripa Club
 * @license     https://github.com/monetise/money/blob/develop/LICENSE
 */
namespace Monetise\Money\Money;

use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\Stdlib\Hydrator\Filter\FilterComposite;
use Zend\Stdlib\Hydrator\Filter\MethodMatchFilter;
use Zend\Stdlib\Hydrator\HydratorAwareInterface;
use Zend\Stdlib\Hydrator\HydratorAwareTrait;

/**
 * MoneyObject
 */
class MoneyObject implements MoneyInterface, HydratorAwareInterface
{
    use MoneyTrait;
    use HydratorAwareTrait;
    
    /**
     * @param int|null $amount
     * @param string|null $currency
     */
    public function __construct($amount = null, $currency = null)
    {
        if (null !== $amount) {
            $this->setAmount($amount);
        }
        
        if (null !== $currency) {
            $this->setCurrency($currency);
        }
    }

    /**
     * Retrieve hydrator
     *
     * @return ClassMethods
     */
    public function getHydrator()
    {
        if (!$this->hydrator) {
            $this->hydrator = new ClassMethods(true);
            $this->hydrator->addFilter(
                'hydrator',
                new MethodMatchFilter(
                    'getHydrator',
                    true // exclude
                ),
                FilterComposite::CONDITION_AND
            );
            $this->hydrator->addFilter(
                'sub_unit',
                new MethodMatchFilter(
                    'getSubUnit',
                    true // exclude
                ),
                FilterComposite::CONDITION_AND
            );
            $this->hydrator->addFilter(
                'fraction_digits',
                new MethodMatchFilter(
                    'getFractionDigits',
                    true // exclude
                ),
                FilterComposite::CONDITION_AND
            );
        }
        return $this->hydrator;
    }
}
