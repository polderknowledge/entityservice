<?php
/**
 * Polder Knowledge / entityservice (https://polderknowledge.com)
 *
 * @link https://github.com/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2016 Polder Knowledge (https://polderknowledge.com)
 * @license https://github.com/polderknowledge/entityservice/blob/master/LICENSE.md MIT
 */

namespace PolderKnowledge\EntityServiceTest\Repository\Service;

use Interop\Container\ContainerInterface;
use PHPUnit_Framework_TestCase;
use PolderKnowledge\EntityService\Repository\Feature\DeletableInterface;
use PolderKnowledge\EntityService\Repository\Feature\FlushableInterface;
use PolderKnowledge\EntityService\Repository\Feature\ReadableInterface;
use PolderKnowledge\EntityService\Repository\Feature\WritableInterface;
use PolderKnowledge\EntityService\Repository\Service\EntityRepositoryManager;

class EntityRepositoryManagerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var EntityRepositoryManager
     */
    private $entityRepositoryManager;

    protected function setUp()
    {
        parent::setUp();

        $container = $this->getMockForAbstractClass(ContainerInterface::class);

        $this->entityRepositoryManager = new EntityRepositoryManager($container);
    }

    /**
     * @dataProvider servicesDataProvider
     * @param string $className
     */
    public function testValidate($className)
    {
        // Arrange
        $plugin = $this->getMockBuilder($className)->getMock();

        // Act
        $this->entityRepositoryManager->validate($plugin);

        // Assert
        // ...
    }

    public function servicesDataProvider()
    {
        return [
            [ReadableInterface::class],
            [WritableInterface::class],
            [DeletableInterface::class],
            [FlushableInterface::class],
        ];
    }

    /**
     * @expectedException \PolderKnowledge\EntityService\Exception\InvalidServiceException
     * @expectedExceptionMessage Plugin manager "PolderKnowledge\EntityService\Repository\Service\EntityRepositoryManager" expected a type of PolderKnowledge\EntityService\Repository\Feature\DeletableInterface, PolderKnowledge\EntityService\Repository\Feature\FlushableInterface, PolderKnowledge\EntityService\Repository\Feature\ReadableInterface or PolderKnowledge\EntityService\Repository\Feature\WritableInterface but received "stdClass"
     */
    public function testValidatePluginWithInvalidPlugin()
    {
        // Arrange
        $plugin = new \stdClass();

        // Act
        $this->entityRepositoryManager->validate($plugin);

        // Assert
        // ...
    }

    /**
     * @expectedException \Zend\ServiceManager\Exception\ServiceNotFoundException
     */
    public function testExceptionWhenRequestingNonExistentClass()
    {
        $this->entityRepositoryManager->get('This\\Class\\Does\\Not\\Exist');
    }

    /**
     * @expectedException \Zend\ServiceManager\Exception\ServiceNotFoundException
     *
     * The serviceManager should not fall back to direct construction since this makes no sense when requesting a repository
     */
    public function testNoInvokableFallback()
    {
        $this->entityRepositoryManager->get(MyEntity::class);
    }
}
