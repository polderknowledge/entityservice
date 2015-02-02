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
use PolderKnowledge\EntityService\ServiceProblem;

class ServiceProblemTest extends PHPUnit_Framework_TestCase
{
    public function testDefaultConstructor()
    {
        // Arrange
        $problem = new ServiceProblem('message');

        // Act
        // ...

        // Assert
        $this->assertEquals('message', $problem->getMessage());
        $this->assertEquals(1, $problem->getCode());
    }

    public function testGetMessage()
    {
        // Arrange
        $problem = new ServiceProblem('message');

        // Act
        // ...

        // Assert
        $this->assertEquals('message', $problem->getMessage());
    }

    public function testGetCode()
    {
        // Arrange
        $problem = new ServiceProblem('', 123);

        // Act
        // ...

        // Assert
        $this->assertEquals(123, $problem->getCode());
    }
}
