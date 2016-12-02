<?php
/**
 * Polder Knowledge / entityservice (https://polderknowledge.com)
 *
 * @link https://github.com/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2016 Polder Knowledge (https://polderknowledge.com)
 * @license https://github.com/polderknowledge/entityservice/blob/master/LICENSE.md MIT
 */

namespace PolderKnowledge\EntityServiceTest\Event;

use PHPUnit_Framework_TestCase;
use PolderKnowledge\EntityService\Event\EntityEvent;

class EntityEventTest extends PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        // Arrange
        // ...

        // Act
        $entityEvent = new EntityEvent('test1', 'test2', array());

        // Assert
        $this->assertEquals($entityEvent->getName(), 'test1');
        $this->assertEquals($entityEvent->getTarget(), 'test2');
        $this->assertEquals($entityEvent->getParams(), array());
    }

    public function testEmptyConstructor()
    {
        // Arrange
        // ...

        // Act
        $entityEvent = new EntityEvent();

        // Assert
        $this->assertNull($entityEvent->getName());
        $this->assertNull($entityEvent->getTarget());
        $this->assertEquals($entityEvent->getParams(), array());
    }

    public function testSetGetResultWithArray()
    {
        // Arrange
        $entityEvent = new EntityEvent();

        // Act
        $entityEvent->setResult(array(1, 2, 3));

        // Assert
        $this->assertEquals(array(1, 2, 3), $entityEvent->getResult());
    }

    public function testSetGetResultWithInt()
    {
        // Arrange
        $entityEvent = new EntityEvent();

        // Act
        $entityEvent->setResult(123);

        // Assert
        $this->assertEquals(123, $entityEvent->getResult());
    }

    public function testSetGetResultWithString()
    {
        // Arrange
        $entityEvent = new EntityEvent();

        // Act
        $entityEvent->setResult('test');

        // Assert
        $this->assertEquals('test', $entityEvent->getResult());
    }

    public function testSetGetEntityClassName()
    {
        // Arrange
        $entityEvent = new EntityEvent();

        // Act
        $entityEvent->setEntityClassName('PolderKnowledge\\EntityServiceTestAsset\\MyEntity');

        // Assert
        $this->assertEquals('PolderKnowledge\\EntityServiceTestAsset\\MyEntity', $entityEvent->getEntityClassName());
    }

    public function testSetGetEntityClassNameWithSlashes()
    {
        // Arrange
        $entityEvent = new EntityEvent();

        // Act
        $entityEvent->setEntityClassName('\\PolderKnowledge\\EntityServiceTestAsset\\MyEntity');

        // Assert
        $this->assertEquals('PolderKnowledge\\EntityServiceTestAsset\\MyEntity', $entityEvent->getEntityClassName());
    }

    /**
     * @expectedException RuntimeException
     */
    public function testSetGetEntityClassNameWithInvalidClass()
    {
        // Arrange
        $entityEvent = new EntityEvent();

        // Act
        $entityEvent->setEntityClassName('bla');

        // Assert
        // ...
    }

    public function testStopPropagationIsFaleByDefault()
    {
        // Arrange
        // ...

        // Act
        $entityEvent = new EntityEvent();

        // Assert
        $this->assertFalse($entityEvent->propagationIsStopped());
    }

    public function testStopPropagation()
    {
        // Arrange
        $entityEvent = new EntityEvent();

        // Act
        $entityEvent->stopPropagation();

        // Assert
        $this->assertTrue($entityEvent->propagationIsStopped());
    }

    public function testStopPropagationWhenDisabled()
    {
        // Arrange
        $entityEvent = new EntityEvent();
        $entityEvent->disableStoppingOfPropagation();

        // Act
        $entityEvent->stopPropagation();

        // Assert
        $this->assertFalse($entityEvent->propagationIsStopped());
    }

    public function testSetGetError()
    {
        // Arrange
        $entityEvent = new EntityEvent();

        // Act
        $entityEvent->setError('test');

        // Assert
        $this->assertEquals('test', $entityEvent->getError());
    }

    public function testSetGetErrorNr()
    {
        // Arrange
        $entityEvent = new EntityEvent();

        // Act
        $entityEvent->setErrorNr(123);

        // Assert
        $this->assertEquals(123, $entityEvent->getErrorNr());
    }

    public function testSetGetErrorNrWithString()
    {
        // Arrange
        $entityEvent = new EntityEvent();

        // Act
        $entityEvent->setErrorNr('test');

        // Assert
        $this->assertEquals(0, $entityEvent->getErrorNr());
    }

    public function testIsErrorWithError()
    {
        // Arrange
        $entityEvent = new EntityEvent();

        // Act
        $entityEvent->setError('test');

        // Assert
        $this->assertTrue($entityEvent->isError());
    }

    public function testIsErrorWithErrorNr()
    {
        // Arrange
        $entityEvent = new EntityEvent();

        // Act
        $entityEvent->setErrorNr(123);

        // Assert
        $this->assertTrue($entityEvent->isError());
    }

    public function testIsErrorWithoutErrorBeingSet()
    {
        // Arrange
        // ...

        // Act
        $entityEvent = new EntityEvent();

        // Assert
        $this->assertFalse($entityEvent->isError());
    }
}
