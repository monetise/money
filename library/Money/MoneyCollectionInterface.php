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
use Traversable;

/**
 * Interface MoneyCollectionInterface
 */
interface MoneyCollectionInterface extends Traversable
{
    /**
     * Get a copy of this collection as array
     *
     * @return MoneyInterface[]
     */
    public function getArrayCopy();

    /**
     * Set the absolute monetary value for all money objects within the collection
     *
     * @return $this
     */
    public function abs();

    /**
     * Set the negated monetary value for all money objects within the collection
     *
     * @return $this
     */
    public function negate();

    /**
     * Append a copy of elements from another collection into this collection then
     * reduce the collection by summing per currency all money objects
     *
     * @param MoneyCollectionInterface $collection
     * @return $this
     */
    public function add(MoneyCollectionInterface $collection);

    /**
     * Append a copy of negated elements from another collection into this collection
     * then reduce the collection by summing per currency all money objects
     *
     * @param MoneyCollectionInterface $collection
     * @return $this
     */
    public function subtract(MoneyCollectionInterface $collection);

    /**
     * Multiply all money object within the collection by a given factor
     *
     * @param  DecimalNumberInterface|float $factor
     * @param  int $roundingMode
     * @return $this
     */
    public function multiply($factor, $roundingMode = PHP_ROUND_HALF_UP);

    /**
     * Reduce the collection by summing per currency all money objects within the collection
     *
     * @return $this
     */
    public function reduce();

    /**
     * Append a copy of elements from another collection into this collection
     *
     * @param MoneyCollectionInterface $collection
     * @return $this
     */
    public function merge(MoneyCollectionInterface $collection);

    /**
     * Get a copy of this money collection
     *
     * @return MoneyInterface
     */
    public function copy();
}
