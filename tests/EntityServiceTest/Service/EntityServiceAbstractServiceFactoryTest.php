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
use PolderKnowledge\EntityService\EntityServiceInterface;
use PolderKnowledge\EntityService\Repository\EntityRepositoryInterface;
use PolderKnowledge\EntityService\Repository\Service\EntityRepositoryManager;
use PolderKnowledge\EntityService\Service\EntityServiceAbstractServiceFactory;
use PolderKnowledge\EntityServiceTestAsset\MyEntity;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ServiceLocatorInterface;

class EntityServiceAbstractServiceFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers PolderKnowledge\EntityService\Service\EntityServiceAbstractServiceFactory::canCreateServiceWithName
     */
    public function testCanCreateServiceWithName()
    {
        // Arrange
        $entityServiceAbstractServiceFactory = new EntityServiceAbstractServiceFactory();
        $mock = $this->getMockForAbstractClass('Zend\ServiceManager\ServiceLocatorInterface');

        // Act
        $result = $entityServiceAbstractServiceFactory->canCreateServiceWithName($mock, null, null);

        // Assert
        $this->assertTrue($result);
    }

    /**
     * @covers PolderKnowledge\EntityService\Service\EntityServiceAbstractServiceFactory::createServiceWithName
     */
    public function testCreateServiceWithName()
    {
        // Arrange
        $entityServiceAbstractServiceFactory = new EntityServiceAbstractServiceFactory();

        $entityRepository = $this->getMockForAbstractClass(EntityRepositoryInterface::class);

        $entityRepositoryManager = $this->getMock(EntityRepositoryManager::class);
        $entityRepositoryManager->expects($this->once())->method('get')->willReturn($entityRepository);

        $serviceLocator = $this->getMockForAbstractClass(ServiceLocatorInterface::class);
        $serviceLocator->expects($this->once())->method('get')->willReturn($entityRepositoryManager);

        $pluginManagerBuilder = $this->getMockBuilder(AbstractPluginManager::class);
        $pluginManagerBuilder->disableOriginalConstructor();
        $pluginManagerBuilder->setMethods(array(
            'getServiceLocator'
        ));
        $pluginManager = $pluginManagerBuilder->getMockForAbstractClass();
        $pluginManager->expects($this->once())->method('getServiceLocator')->willReturn($serviceLocator);

        // Act
        $result = $entityServiceAbstractServiceFactory->createServiceWithName(
            $pluginManager,
            MyEntity::class,
            MyEntity::class
        );

        // Assert
        $this->assertInstanceOf(EntityServiceInterface::class, $result);
    }
}
