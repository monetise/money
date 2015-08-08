<?php
/**
 * Monetise Tests
 *
 * @link        https://github.com/monetise/money
 * @copyright   Copyright (c) 2015, Ripa Club
 * @license     https://github.com/monetise/money/blob/develop/LICENSE
 */
namespace Monetise\MoneyTest\Money;

use Monetise\Money\Money\MoneyCollection;
use Monetise\Money\Money\MoneyCollectionInterface;
use Monetise\Money\Exception\InvalidArgumentException;
use Monetise\Money\Money\MoneyObject;
use Zend\Stdlib\Hydrator\ArraySerializable;
use Zend\Stdlib\Hydrator\HydratorAwareInterface;
/**
 * Class MoneyCollectionTest
 *
 * @group money
 */
class MoneyCollectionTest extends \PHPUnit_Framework_TestCase
{

    protected $collection; 
    
    public function setUp()
    {
        $this->collection = new MoneyCollection;
    }
    
    public function testImplementsInterface()
    {
        $this->assertInstanceOf(MoneyCollectionInterface::class, $this->collection);
        $this->assertInstanceOf(HydratorAwareInterface::class, $this->collection);
    }
    
    public function testCtor()
    {
        $dataSet = [new MoneyObject, new MoneyObject];
        $invalidDataset = ['foo', 'bar'];
        
        $this->assertSame($dataSet, (new MoneyCollection($dataSet))->getArrayCopy());
        
        $this->setExpectedException(InvalidArgumentException::class);
        new MoneyCollection($invalidDataset);
    }
    
    public function testGetHydrator()
    {
        $this->assertInstanceOf(ArraySerializable::class, $this->collection->getHydrator());
    }
    
    public function testValidateData()
    {
        $this->assertNull($this->collection->validateData([new MoneyObject, new MoneyObject]));
        $this->setExpectedException(InvalidArgumentException::class);
        $this->collection->validateData(['foo']);
    }
    
    public function testOffsetSet()
    {
        $valueObject = new MoneyObject;
        $this->collection->offsetSet(0, $valueObject);
        $this->assertSame($valueObject, $this->collection->offsetGet(0));
        
        $this->setExpectedException(InvalidArgumentException::class);
        $this->collection->offsetSet(0, new \stdClass);
    }
    
    public function testAppend()
    {
        $valueObject = new MoneyObject;
        $this->collection->append($valueObject);
        
        $this->setExpectedException(InvalidArgumentException::class);
        $this->collection->append(new \stdClass);
    }
    
    public function testExchangeArray()
    {
       
        $dataSet1 = [new MoneyObject];
        $dataSet2 = [new MoneyObject, new MoneyObject];
        
        $this->assertEmpty($this->collection->exchangeArray($dataSet1));
        $this->assertSame($dataSet1, $this->collection->exchangeArray($dataSet2));        
        
        $this->setExpectedException(InvalidArgumentException::class);       
        try {
            $this->collection->exchangeArray(['bar' => 'baz']);
        } catch (\Exception $e) {
            // Test data did not change
            $this->assertEquals($dataSet2, $this->collection->getArrayCopy());
            throw $e;
        }
    }
}
