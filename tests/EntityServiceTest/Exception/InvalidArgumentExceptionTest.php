<?php
/**
 * Polder Knowledge / Entity Service (http://polderknowledge.nl)
 *
 * @link http://developers.polderknowledge.nl/gitlab/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2015-2015 Polder Knowledge (http://www.polderknowledge.nl)
 * @license http://polderknowledge.nl/license/proprietary proprietary
 */

namespace PolderKnowledge\EntityServiceTest\Exception;

use PHPUnit_Framework_TestCase;
use PolderKnowledge\EntityService\Exception\InvalidArgumentException;

class InvalidArgumentExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testEmptyConstructor()
    {
        // Arrange
        $exception = new InvalidArgumentException();

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
        $exception = new InvalidArgumentException('message');

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
        $exception = new InvalidArgumentException('message', 123);

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
        $previous = new InvalidArgumentException('previous');
        $exception = new InvalidArgumentException('message', 123, $previous);

        // Act
        // ...

        // Assert
        $this->assertEquals('message', $exception->getMessage());
        $this->assertEquals(123, $exception->getCode());
        $this->assertNotNull($exception->getPrevious());
        $this->assertEquals('previous', $exception->getPrevious()->getMessage());
    }
}
