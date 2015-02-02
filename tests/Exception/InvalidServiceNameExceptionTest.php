<?php
/**
 * Polder Knowledge / Entity Service (http://polderknowledge.nl)
 *
 * @link http://developers.polderknowledge.nl/gitlab/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2015-2015 Polder Knowledge (http://www.polderknowledge.nl)
 * @license http://polderknowledge.nl/license/proprietary proprietary
 */

namespace PolderKnowledge\EntityServiceTest\Exception;

use PolderKnowledge\EntityService\Exception\InvalidServiceNameException;
use PHPUnit_Framework_TestCase;

class InvalidServiceNameExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testEmptyConstructor()
    {
        // Arrange
        $exception = new InvalidServiceNameException();

        // Act
        // ...

        // Assert
        $this->assertEquals('', $exception->getMessage());
        $this->assertEquals(0, $exception->getCode());
        $this->assertNull($exception->getPrevious());
    }

    public function testConstructorWithoutCodeAndPrevious()
    {
        // Arrange
        $exception = new InvalidServiceNameException('message');

        // Act
        // ...

        // Assert
        $this->assertEquals('message', $exception->getMessage());
        $this->assertEquals(0, $exception->getCode());
        $this->assertNull($exception->getPrevious());
    }

    public function testConstructorWithMessageAndCodeNoPrevious()
    {
        // Arrange
        $exception = new InvalidServiceNameException('message', 123);

        // Act
        // ...

        // Assert
        $this->assertEquals('message', $exception->getMessage());
        $this->assertEquals(123, $exception->getCode());
        $this->assertNull($exception->getPrevious());
    }

    public function testConstructorWithPrevious()
    {
        // Arrange
        $previous = new InvalidServiceNameException('previous');
        $exception = new InvalidServiceNameException('message', 123, $previous);

        // Act
        // ...

        // Assert
        $this->assertEquals('message', $exception->getMessage());
        $this->assertEquals(123, $exception->getCode());
        $this->assertNotNull($exception->getPrevious());
        $this->assertEquals('previous', $exception->getPrevious()->getMessage());
    }
}
