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
use PolderKnowledge\EntityService\DefaultEntityService;
use PolderKnowledge\EntityService\EntityServiceInterface;
use PolderKnowledge\EntityService\Exception\InvalidServiceNameException;
use PolderKnowledge\EntityService\Repository\Service\EntityRepositoryManager;
use PolderKnowledge\EntityService\Service\EntityServiceManager;
use PolderKnowledge\EntityServiceTestAsset\MyEntity;
use PolderKnowledge\EntityServiceTestAsset\MyRepository;
use stdClass;
use Zend\EventManager\EventManager;
use Zend\EventManager\SharedEventManager;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\ServiceManager;

class EntityServiceManagerTest extends PHPUnit_Framework_TestCase
{
    public function testManagerHasAbstractFactory()
    {
        // Arrange
        $entityServiceManager = new EntityServiceManager();

        // Act
        // ...

        // Assert
        $this->assertTrue($entityServiceManager->canCreateFromAbstractFactory(
            'polderKnowledgeentityservicetestserviceassetcustomentitymock',
            MyEntity::class
        ));
    }

    public function testValidatePlugin()
    {
        // Arrange
        $entityServiceManager = new EntityServiceManager();
        $plugin = $this->getMockForAbstractClass(EntityServiceInterface::class);

        // Act
        $entityServiceManager->validatePlugin($plugin);

        // Assert
        /// ...
    }

    /**
     * @expectedException PolderKnowledge\EntityService\Exception\InvalidServiceNameException
     * @expectedExceptionMessage Plugin of type stdClass is invalid; must implement
     * PolderKnowledge\EntityService\EntityServiceInterface
     */
    public function testValidatePluginWithInvalidPlugin()
    {
        // Arrange
        $entityServiceManager = new EntityServiceManager();
        $plugin = new stdClass();

        // Act
        $entityServiceManager->validatePlugin($plugin);

        // Assert
        /// ...
    }

    public function testStandardFunctionality()
    {
        $entityServiceManager = $this->createStandardEntityServiceManager();
        $myEntityService = $entityServiceManager->get(MyEntity::class);
        $this->assertInstanceOf(DefaultEntityService::class, $myEntityService);
    }

    public function testExceptionWhenRequestingNonExistentClass()
    {
        $entityServiceManager = $this->createStandardEntityServiceManager();

        $this->setExpectedException(ServiceNotFoundException::class);
        $entityServiceManager->get('This\Class\Does\Not\Exist');
    }

    public function testExceptionWhenRequestingNonEntityClass()
    {
        $entityServiceManager = $this->createStandardEntityServiceManager();

        $this->setExpectedException(ServiceNotFoundException::class);
        $entityServiceManager->get('ArrayIterator');
    }

    public function testExceptionWhenRequestingNonRegisteredEntityClass()
    {
        $entityServiceManager = $this->createStandardEntityServiceManager(false);

        $this->setExpectedException(ServiceNotCreatedException::class);
        $entityServiceManager->get(MyEntity::class);
    }

    private function createStandardEntityServiceManager($addDefaultRepository = true)
    {
        $applicationServiceManager = new ServiceManager;
        $applicationServiceManager->setFactory('EventManager', function() {
            $eventManager = new EventManager;
            $eventManager->setSharedManager(new SharedEventManager);
            return $eventManager;
        });

        $applicationServiceManager->setFactory('EntityRepositoryManager', function() use ($addDefaultRepository) {
            $entityRepositoryManager = new EntityRepositoryManager();
            if ($addDefaultRepository) {
                $entityRepositoryManager->setInvokableClass(MyEntity::class, MyRepository::class);
            }
            return $entityRepositoryManager;
        });

        $entityServiceManager = new EntityServiceManager;
        $entityServiceManager->setServiceLocator($applicationServiceManager);
        return $entityServiceManager;
    }
}
