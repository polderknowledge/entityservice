<?php
/**
 * Polder Knowledge / Entity Service (http://polderknowledge.nl)
 *
 * @link http://developers.polderknowledge.nl/gitlab/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2015-2015 Polder Knowledge (http://www.polderknowledge.nl)
 * @license http://polderknowledge.nl/license/proprietary proprietary
 */

namespace PolderKnowledge\EntityServiceTest\Exception;

use ArrayIterator;
use PHPUnit_Framework_TestCase;
use PolderKnowledge\EntityService\ServiceResult;
use PolderKnowledge\EntityServiceTest\_Asset\ServiceResultIteratorAggregate;

class ServiceResultTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ServiceResult
     */
    private $result;

    public function setUp()
    {
        $this->result = new ServiceResult();
        $this->result->initialize(array(
            'firstElement',
            'secondElement',
            'thirdElement',
            'fourthElement',
        ));
    }

    public function testRewindCall()
    {
        // Arrange
        // ...

        // Act
        $this->result->next();
        $this->result->rewind();

        // Assert
        $this->assertEquals('firstElement', $this->result->current());
        $this->assertEquals(0, $this->result->key());
    }

    public function testNextCall()
    {
        // Arrange
        // ...

        // Act
        $this->result->next();

        // Assert
        $this->assertEquals('secondElement', $this->result->current());
    }

    public function testnextCallOnLastItemIsNotValid()
    {
        // Arrange
        // ...

        // Act
        for ($i = 0; $i < 4; $i++) {
            $this->result->next();
        }

        // Assert
        $this->assertFalse($this->result->valid());
    }

    public function testPrevCall()
    {
        // Arrange
        // ...

        // Act
        $this->result->next();
        $this->result->next();
        $this->result->prev();

        // Assert
        $this->assertEquals('secondElement', $this->result->current());
    }

    public function testPrevCallOnFirstItemIsNotValid()
    {
        // Arrange
        // ...

        // Act
        $this->result->prev();

        // Assert
        $this->assertFalse($this->result->valid());
    }

    public function testCountCall()
    {
        // Arrange
        // ...

        // Act
        // ...

        // Assert
        $this->assertEquals(4, $this->result->count());
    }

    public function testWithIteratorAggregate()
    {
        // Arrange
        $data = array('1', '2', '3');
        $result = new ServiceResult();
        $iteratorAggregate = new ServiceResultIteratorAggregate($data);

        // Act
        $result->initialize($iteratorAggregate);

        // Assert
        $this->assertEquals($data, $result->getDataSource());
    }

    public function testWithIterator()
    {
        // Arrange
        $data = array('1', '2', '3');
        $result = new ServiceResult();
        $iterator = new ArrayIterator($data);

        // Act
        $result->initialize($iterator);

        // Assert
        $this->assertInstanceOf('ArrayIterator', $result->getDataSource());
    }

    /**
     * @expectedException PolderKnowledge\EntityService\Exception\InvalidArgumentException
     * @expectedExceptionMessage DataSource provided is not an array,
     * nor does it implement Iterator or IteratorAggregate, got NULL
     */
    public function testWithInvalidConstructor()
    {
        // Arrange
        $data = null;
        $result = new ServiceResult();

        // Act
        $result->initialize($data);

        // Assert
        // ... asserting happens because of the expectedException annotation
    }

    public function testCountWithNonArray()
    {
        // Arrange
        $data = array('1', '2', '3');
        $result = new ServiceResult();
        $iteratorAggregate = new ServiceResultIteratorAggregate($data);

        // Act
        $result->initialize($iteratorAggregate);

        // Assert
        $this->assertCount(3, $result);
    }
}
