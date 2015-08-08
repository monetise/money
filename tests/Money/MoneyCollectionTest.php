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
use Monetise\Money\DecimalNumber\DecimalNumberObject;
use Monetise\Money\DecimalNumber\DecimalNumberInterface;
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
        $this->assertInstanceOf(HydratorAwareInterface::class, $this->collection);
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
    
    public function collectionDataProvider()
    {
        $data = [
            [['amount' => 20, 'currency' => 'EUR']],
            [['amount' => -10, 'currency' => 'EUR']],
            [['amount' => -10, 'currency' => 'EUR'], ['amount' => -10, 'currency' => 'USD'], ['amount' => 100, 'currency' => 'EUR']],
            [['amount' => -11, 'currency' => 'EUR'], ['amount' => 150, 'currency' => 'GBP'], ['amount' => 100, 'currency' => 'EUR']],
        ];
        
        $return = [];
        
        foreach ($data as $rawData) {
            $moneyCollection = new MoneyCollection();
            foreach ($rawData as $moneyData) {
                $moneyCollection->append(new MoneyObject($moneyData['amount'], $moneyData['currency']));
            }
            $return[] = [$moneyCollection, $rawData];
        }
        
        return $return;
    }
    
    public function collectionData2Provider()
    {
        $data = [
            [['amount' => -10, 'currency' => 'EUR'], ['amount' => -10, 'currency' => 'USD'], ['amount' => 100, 'currency' => 'EUR']],
            [['amount' => 99, 'currency' => 'USD']],
            [['amount' => -44, 'currency' => 'CHF'], ['amount' => 43, 'currency' => 'GBP'], ['amount' => 100, 'currency' => 'USD']],
        ];
    
        $return = [];
    
        foreach ($data as $rawData) {
            $moneyCollection = new MoneyCollection();
            foreach ($rawData as $moneyData) {
                $moneyCollection->append(new MoneyObject($moneyData['amount'], $moneyData['currency']));
            }
            $return[] = [$moneyCollection, $rawData];
        }
    
        return $return;
    }
    
    public function reductionDataProvider()
    {
        $data = [
            [['amount' => -10, 'currency' => 'EUR'], ['amount' => -10, 'currency' => 'USD'], ['amount' => 100, 'currency' => 'EUR']],
            [['amount' => -11, 'currency' => 'EUR'], ['amount' => 150, 'currency' => 'GBP'], ['amount' => 100, 'currency' => 'EUR']],
            [['amount' => 1, 'currency' => 'GBP'], ['amount' => 1, 'currency' => 'GBP'], ['amount' => 1, 'currency' => 'GBP']],
            [['amount' => -1, 'currency' => 'GBP'], ['amount' => 0, 'currency' => 'GBP'], ['amount' => 1, 'currency' => 'GBP']],
        ];
        
        $reducedData = [
            ['EUR' => ['amount' => 90, 'currency' => 'EUR'], 'USD' => ['amount' => -10, 'currency' => 'USD']],
            ['EUR' => ['amount' => 89, 'currency' => 'EUR'], 'GBP' => ['amount' => 150, 'currency' => 'GBP']],
            ['GBP' => ['amount' => 3, 'currency' => 'GBP']],
            ['GBP' => ['amount' => 0, 'currency' => 'GBP']],
        ];
    
        $return = [];
    
        foreach ($data as $k => $rawData) {
            $moneyCollection = new MoneyCollection();
            foreach ($rawData as $moneyData) {
                $moneyCollection->append(new MoneyObject($moneyData['amount'], $moneyData['currency']));
            }
            $return[] = [$moneyCollection, $rawData, $reducedData[$k]];
        }
    
        return $return;
    }
    
    
    protected function buildMatrix(array $leftdata, \Closure $operator, array $rightData)
    {
        $leftdata  = $this->reductionDataProvider();
        $rightData = $this->reductionDataProvider();
        
        $return = [];
        
        foreach ($leftdata as $left) {
            foreach ($rightData as $right) {
                $reducedData = [];
                
                $currencies = array_unique(array_merge(array_keys($left[2]), array_keys($right[2])));
                foreach ($currencies as $currency) {     
                    $reducedData[$currency] = [
                        'amount'    => $operator(
                            isset($left[2][$currency]) ? $left[2][$currency]['amount'] : 0, 
                            isset($right[2][$currency]) ? $right[2][$currency]['amount'] : 0
                        ),
                        'currency'  => $currency
                    ];
                }
        
                $return[] = [clone $left[0], $left[1], clone $right[0], $right[1], $reducedData];
            }
        }
        
        return $return;
    }
    
    
    public function addDataProvider()
    {
        return $this->buildMatrix(
            $this->reductionDataProvider(),
            function ($left, $right) { return $left + $right; },
            $this->reductionDataProvider()
        );
    }
    
    public function subtractDataProvider()
    {
        return $this->buildMatrix(
            $this->reductionDataProvider(),
            function ($left, $right) { return $left - $right; },
            $this->reductionDataProvider()
        );
    }
    
    public function mergeDataProvider()
    {
        $dataSet1 = $this->collectionDataProvider();
        $dataSet2 = $this->collectionData2Provider();
        
        $return = [];
        
        foreach ($dataSet1 as $k => $data1) {
            
            if (isset($dataSet2[$k])) {
                $data2 = $dataSet2[$k];    
            } else {
                $data2 = [new MoneyCollection, []];
            }
            
            $expected = $data1[1];
            foreach ($data2[1] as $elem) {
                $expected[] = $elem;
            }
            
            $return[] = [
                $data1[0], $data1[1], $data2[0], $data2[1], $expected
            ];
        }
        
        return $return;
    }
    
    /**
     * @dataProvider collectionDataProvider
     * @param MoneyCollection $moneyCollection
     * @param array $rawData
     */
    public function testAbs(MoneyCollectionInterface $moneyCollection, array $rawData)
    {
        $this->assertSame($moneyCollection, $moneyCollection->abs());
        foreach ($moneyCollection as $k => $money) {
            $this->assertGreaterThan(0, $money->getAmount());
            $this->assertEquals(abs($rawData[$k]['amount']), $money->getAmount());
            $this->assertEquals($rawData[$k]['currency'], $money->getCurrency());
        }
    }
    
    /**
     * @dataProvider collectionDataProvider
     * @param MoneyCollection $moneyCollection
     * @param array $rawData
     */
    public function testNegate(MoneyCollectionInterface $moneyCollection, array $rawData)
    {
        $this->assertSame($moneyCollection, $moneyCollection->negate());
        foreach ($moneyCollection as $k => $money) {
            $this->assertEquals(-$rawData[$k]['amount'], $money->getAmount());
            $this->assertEquals($rawData[$k]['currency'], $money->getCurrency());
        }
    }
    
    
    /**
     * @dataProvider addDataProvider
     * @param MoneyCollectionInterface $moneyCollection
     * @param array $rawData
     * @param MoneyCollectionInterface $moneyCollection2
     * @param array $rawData2
     * @param array $reducedData
     */
    public function testAdd(
        MoneyCollectionInterface $moneyCollection, 
        array $rawData, 
        MoneyCollectionInterface $moneyCollection2, 
        array $rawData2, 
        array $reducedData
    ) {
        $this->assertSame($moneyCollection, $moneyCollection->add($moneyCollection2));
        foreach ($moneyCollection as $k => $money) {
            $this->assertEquals($reducedData[$k]['amount'], $money->getAmount());
            $this->assertEquals($reducedData[$k]['currency'], $money->getCurrency());
        }
    }
    
    /**
     * @dataProvider subtractDataProvider
     * @param MoneyCollectionInterface $moneyCollection
     * @param array $rawData
     * @param MoneyCollectionInterface $moneyCollection2
     * @param array $rawData2
     * @param array $reducedData
     */
    public function testSubtract(
        MoneyCollectionInterface $moneyCollection,
        array $rawData,
        MoneyCollectionInterface $moneyCollection2,
        array $rawData2,
        array $reducedData
    ) {
            $this->assertSame($moneyCollection, $moneyCollection->subtract($moneyCollection2));
            foreach ($moneyCollection as $k => $money) {
                $this->assertEquals($reducedData[$k]['amount'], $money->getAmount());
                $this->assertEquals($reducedData[$k]['currency'], $money->getCurrency());
            }
    }
    
    /**
     * @dataProvider collectionDataProvider
     * @param MoneyCollection $moneyCollection
     * @param array $rawData
     */
    public function testMultiply(MoneyCollectionInterface $moneyCollection, array $rawData)
    {
        $factors = [
            0,
            1,
            -1,
            2.5,
            new DecimalNumberObject(2, 2),
            new DecimalNumberObject(-2, 3),
        ];
        
        foreach ($factors as $factor) {
            $testCollection = clone $moneyCollection;
            
            
            $this->assertSame($testCollection, $testCollection->multiply($factor));
            foreach ($testCollection as $k => $money) {
                
                $moneyObjectAsset = new MoneyObject($rawData[$k]['amount'], $rawData[$k]['currency']);
                $moneyObjectAsset->multiply($factor);
                
                $this->assertEquals(
                    $moneyObjectAsset->getAmount(), 
                    $money->getAmount()
                );
                
                $this->assertEquals($rawData[$k]['currency'], $money->getCurrency());
            }
        }
    }
    
    /**
     * @dataProvider reductionDataProvider
     * @param MoneyCollection $moneyCollection
     * @param array $rawData
     */
    public function testReduce(MoneyCollectionInterface $moneyCollection, array $rawData, array $reducedData)
    {
        $this->assertSame($moneyCollection, $moneyCollection->reduce());
        foreach ($moneyCollection as $k => $money) {
            $this->assertEquals($reducedData[$k]['amount'], $money->getAmount());
            $this->assertEquals($reducedData[$k]['currency'], $money->getCurrency());
        }
    }
    
    /**
     * @dataProvider mergeDataProvider
     * @param MoneyCollectionInterface $moneyCollection
     * @param array $rawData
     * @param MoneyCollectionInterface $moneyCollection2
     * @param array $rawData2
     * @param array $expected
     */
    public function testMerge(
        MoneyCollectionInterface $moneyCollection,
        array $rawData,
        MoneyCollectionInterface $moneyCollection2,
        array $rawData2,
        array $expected
    ) {
        $this->assertSame($moneyCollection, $moneyCollection->merge($moneyCollection2));
        foreach ($moneyCollection as $k => $money) {
            $this->assertEquals($expected[$k]['amount'], $money->getAmount());
            $this->assertEquals($expected[$k]['currency'], $money->getCurrency());
        }
    }
    
    public function testCopy()
    {
        $moneyCollection = new MoneyCollection([(new MoneyObject)->setAmount(4321)->setCurrency('GBP')]);
        $copy = $moneyCollection->copy();
    
        $this->assertNotSame($moneyCollection, $copy);
        $this->assertEquals($moneyCollection->getArrayCopy(), $copy->getArrayCopy());
    }
    
}
