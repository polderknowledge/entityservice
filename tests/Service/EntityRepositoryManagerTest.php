<?php
/**
 * Polder Knowledge / Entity Service (http://polderknowledge.nl)
 *
 * @link http://developers.polderknowledge.nl/gitlab/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2015-2015 Polder Knowledge (http://www.polderknowledge.nl)
 * @license http://polderknowledge.nl/license/proprietary proprietary
 */

namespace PolderKnowledge\EntityServiceTest\Service;

use PHPUnit_Framework_TestCase;
use PolderKnowledge\EntityService\Service\EntityRepositoryManager;
use stdClass;

class EntityRepositoryManagerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var EntityRepositoryManager
     */
    protected $manager;

    public function setUp()
    {
        $this->manager = new EntityRepositoryManager();
    }

    /**
     * @expectedException PolderKnowledge\EntityService\Exception\InvalidServiceNameException
     */
    public function testManagerThrowsExceptionOnInvalidClass()
    {
        // Arrange
        // ...

        // Act
        $this->manager->validatePlugin(new stdClass());

        // Assert
        // ... asserting happens because of the expectedException annotation
    }

    /**
     * @dataProvider servicesDataProvider
     *
     * @param string $classname
     */
    public function testIsValidService($classname)
    {
        // Arrange
        $reabableMock = $this->getMock($classname);

        // Act
        // ...

        // Assert
        $this->assertNull($this->manager->validatePlugin($reabableMock));
    }

    public function servicesDataProvider()
    {
        return array(
            array('PolderKnowledge\\EntityService\\Repository\\ReadableInterface'),
            array('PolderKnowledge\\EntityService\\Repository\\WritableInterface'),
            array('PolderKnowledge\\EntityService\\Repository\\DeletableInterface'),
            array('PolderKnowledge\\EntityService\\Repository\\FlushableInterface'),
        );
    }
}
