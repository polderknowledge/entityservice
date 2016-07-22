<?php
/**
 * Polder Knowledge / Entity Service (http://polderknowledge.nl)
 *
 * @link http://developers.polderknowledge.nl/gitlab/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2015-2015 Polder Knowledge (http://www.polderknowledge.nl)
 * @license http://polderknowledge.nl/license/proprietary proprietary
 */

namespace PolderKnowledge\EntityServiceTest\Repository\Service;

use PHPUnit_Framework_TestCase;
use PolderKnowledge\EntityService\Repository\Feature\DeletableInterface;
use PolderKnowledge\EntityService\Repository\Feature\FlushableInterface;
use PolderKnowledge\EntityService\Repository\Feature\ReadableInterface;
use PolderKnowledge\EntityService\Repository\Feature\WritableInterface;
use PolderKnowledge\EntityService\Repository\Service\EntityRepositoryManager;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\ServiceManager;

class EntityRepositoryManagerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider servicesDataProvider
     * @param string $className
     */
    public function testValidatePlugin($className)
    {
        // Arrange
        $entityRepositoryManager = new EntityRepositoryManager();
        $plugin = $this->getMock($className);

        // Act
        $entityRepositoryManager->validatePlugin($plugin);

        // Assert
        // ...
    }

    public function servicesDataProvider()
    {
        return array(
            array(ReadableInterface::class),
            array(WritableInterface::class),
            array(DeletableInterface::class),
            array(FlushableInterface::class),
        );
    }

    /**
     * @expectedException PolderKnowledge\EntityService\Exception\InvalidServiceNameException
     * @expectedExceptionMessage Plugin of type stdClass is invalid; must implement
     * PolderKnowledge\EntityService\Repository\ReadableInterface or
     * PolderKnowledge\EntityService\Repository\WritableInterface or
     * PolderKnowledge\EntityService\Repository\FlushableInterface or
     * PolderKnowledge\EntityService\Repository\DeletableInterface
     */
    public function testValidatePluginWithInvalidPlugin()
    {
        // Arrange
        $entityRepositoryManager = new EntityRepositoryManager();
        $plugin = new \stdClass();

        // Act
        $entityRepositoryManager->validatePlugin($plugin);

        // Assert
        // ...
    }

    public function testExceptionWhenRequestingNonExistentClass()
    {
        $this->setExpectedException(ServiceNotFoundException::class);

        $entityServiceManager = new EntityRepositoryManager();
        $entityServiceManager->get('This\Class\Does\Not\Exist');
    }

    /**
     * The serviceManager should not fall back to direct construction since this makes no sense when requesting a repository
     */
    public function testNoInvokableFallback()
    {
        $this->setExpectedException(ServiceNotFoundException::class);

        $entityRepositoryManager = new EntityRepositoryManager();
        $entityRepositoryManager->get(MyEntity::class);
    }
}
