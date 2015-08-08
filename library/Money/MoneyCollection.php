<?php
/**
 * Monetise
 *
 * @link        https://github.com/monetise/money
 * @copyright   Copyright (c) 2015, Ripa Club
 * @license     https://github.com/monetise/money/blob/develop/LICENSE
 */
namespace Monetise\Money\Money;

use Zend\Stdlib\ArrayObject;
use Monetise\Money\Exception;
use Zend\Stdlib\Hydrator\HydratorAwareTrait;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\Stdlib\Hydrator\ArraySerializable;
use Zend\Stdlib\Hydrator\HydratorAwareInterface;

/**
 * Class MoneyCollection
 */
class MoneyCollection extends ArrayObject implements MoneyCollectionInterface, HydratorAwareInterface
{

    use HydratorAwareTrait;
    
    /**
     * Constructor
     *
     * @param array  $input
     * @param int    $flags
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
            throw new Exception\InvalidArgumentException(sprintf(
                'Value of type "%s" is invalid for %s; must implement "%s"',
                is_object($value) ? get_class($value) : gettype($value),
                get_class($this),
                MoneyInterface::class
            ));
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
    
   
}