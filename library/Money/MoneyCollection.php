<?php
/**
 * Monetise
 *
 * @link        https://github.com/monetise/money
 * @copyright   Copyright (c) 2015, Ripa Club
 * @license     https://github.com/monetise/money/blob/develop/LICENSE
 */
namespace Monetise\Money\Money;

use Monetise\Money\Exception;
use Zend\Stdlib\ArrayObject;
use Zend\Stdlib\Hydrator\ArraySerializable;
use Zend\Stdlib\Hydrator\HydratorAwareInterface;
use Zend\Stdlib\Hydrator\HydratorAwareTrait;
use Zend\Stdlib\Hydrator\HydratorInterface;

/**
 * Class MoneyCollection
 */
class MoneyCollection extends ArrayObject implements MoneyCollectionInterface, HydratorAwareInterface
{

    use HydratorAwareTrait;

    /**
     * @var MoneyInterface[]
     */
    protected $storage;

    /**
     * Constructor
     *
     * @param array $input
     * @param int $flags
     * @param string $iteratorClass
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($input = [], $flags = self::STD_PROP_LIST, $iteratorClass = 'ArrayIterator')
    {
        $this->validateData($input);
        parent::__construct($input, $flags, $iteratorClass);
    }

    /**
     * @return HydratorInterface
     */
    public function getHydrator()
    {
        if (!$this->hydrator) {
            $this->hydrator = new ArraySerializable;
        }
        return $this->hydrator;
    }

    /**
     * Validate the value
     *
     * Checks that the value passed is allowed within the collection
     *
     * @param mixed $value
     * @throws Exception\InvalidArgumentException
     */
    public function validateValue($value)
    {
        if (!$value instanceof MoneyInterface) {
            throw new Exception\InvalidArgumentException(
                sprintf(
                    'Value of type "%s" is invalid for %s; must implement "%s"',
                    is_object($value) ? get_class($value) : gettype($value),
                    get_class($this),
                    MoneyInterface::class
                )
            );
        }
    }

    /**
     * Validate an array of values
     *
     * Checks that values passed are allowed within the collection
     *
     * @param array $data
     * @throws Exception\InvalidArgumentException
     */
    public function validateData(array $data)
    {
        foreach ($data as $value) {
            $this->validateValue($value);
        }
    }

    /**
     * {@inheritdoc}
     * @throws Exception\InvalidArgumentException
     */
    public function offsetSet($key, $value)
    {
        $this->validateValue($value);
        return parent::offsetSet($key, $value);
    }

    /**
     * {@inheritdoc}
     * @throws Exception\InvalidArgumentException
     */
    public function append($value)
    {
        $this->validateValue($value);
        return parent::append($value);
    }

    /**
     * {@inheritdoc}
     */
    public function exchangeArray($data)
    {
        $oldData = parent::exchangeArray($data);
        try {
            $this->validateData($this->storage);
        } catch (\Exception $e) {
            $this->storage = $oldData;
            throw $e;
        }
        return $oldData;
    }

    /**
     * {@inheritdoc}
     */
    public function abs()
    {
        foreach ($this->storage as $money) {
            $money->abs();
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function negate()
    {
        foreach ($this->storage as $money) {
            $money->negate();
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function add(MoneyCollectionInterface $collection)
    {
        return $this->merge($collection)->reduce();
    }

    /**
     * {@inheritdoc}
     */
    public function subtract(MoneyCollectionInterface $collection)
    {
        /** @var $money MoneyInterface */
        foreach ($collection as $money) {
            $money = clone $money;
            $this->append($money->negate());
        }
        return $this->reduce();
    }

    /**
     * {@inheritdoc}
     */
    public function multiply($factor, $roundingMode = PHP_ROUND_HALF_UP)
    {
        foreach ($this->storage as $money) {
            $money->multiply($factor, $roundingMode);
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function reduce()
    {
        /** @var $moneyByCurrency MoneyInterface[] */
        $moneyByCurrency = [];

        /** @var $money MoneyInterface */
        foreach ($this->storage as $money) {
            $currency = $money->getCurrency();
            if (!isset($moneyByCurrency[$currency])) {
                $moneyByCurrency[$currency] = (new MoneyObject)->setCurrency($currency);
            }
            $moneyByCurrency[$currency]->add($money);
        }

        $this->storage = $moneyByCurrency;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function merge(MoneyCollectionInterface $collection)
    {
        foreach ($collection as $money) {
            $this->append(clone $money);
        }
        return $this;
    }

    /**
     * @return MoneyCollectionInterface
     */
    public function copy()
    {
        return clone $this;
    }

    /**
     * Clone recursively all objects within the collection
     */
    public function __clone()
    {
        $cloned = [];
        foreach ($this->storage as $k => $v) {
            $cloned[$k] = clone $v;
        }
        $this->storage = $cloned;
    }
}
