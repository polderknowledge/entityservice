<?php
/**
 * Polder Knowledge / Entity Service (http://polderknowledge.nl)
 *
 * @link http://developers.polderknowledge.nl/gitlab/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2015-2015 Polder Knowledge (http://www.polderknowledge.nl)
 * @license http://polderknowledge.nl/license/proprietary proprietary
 */

namespace PolderKnowledge\EntityServiceTest\Validator\Service;

use PHPUnit_Framework_TestCase;
use PolderKnowledge\EntityService\EntityServiceInterface;
use PolderKnowledge\EntityService\Service\EntityServiceManager;
use PolderKnowledge\EntityService\Validator\EntityNotExists;
use PolderKnowledge\EntityService\Validator\Service\EntityNotExistsFactory;
use Zend\ServiceManager\AbstractPluginManager;

class EntityNotExistsFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testCreateService()
    {
        // Arrange
        $entityNotExistsFactory = new EntityNotExistsFactory();

        $entityServiceMock = $this->getMockForAbstractClass(EntityServiceInterface::class);

        $entityServiceManager = $this->getMock(EntityServiceManager::class);
        $entityServiceManager->expects($this->once())->method('get')->willReturn($entityServiceMock);

        $serviceManagerMockBuilder = $this->getMockBuilder(ServiceManager::class);
        $serviceManagerMockBuilder->setMethods(array('get'));
        $serviceManager = $serviceManagerMockBuilder->getMock();
        $serviceManager->expects($this->once())->method('get')->willReturn($entityServiceManager);

        $mockBuilder = $this->getMockBuilder(AbstractPluginManager::class);
        $mockBuilder->setMethods(array('getServiceLocator'));
        $mock = $mockBuilder->getMockForAbstractClass();
        $mock->expects($this->once())->method('getServiceLocator')->willReturn($serviceManager);

        // Act
        $result = $entityNotExistsFactory->createService($mock);

        // Assert
        $this->assertInstanceOf(EntityNotExists::class, $result);
    }

    public function testSetCreationOptions()
    {
        // Arrange
        $entityNotExistsFactory = new EntityNotExistsFactory();
        $entityNotExistsFactory->setCreationOptions(array(
            'entity' => MyEntity::class,
        ));

        $entityServiceMock = $this->getMockForAbstractClass(EntityServiceInterface::class);

        $entityServiceManager = $this->getMock(EntityServiceManager::class);
        $entityServiceManager->expects($this->once())->method('get')->willReturn($entityServiceMock);

        $serviceManagerMockBuilder = $this->getMockBuilder(ServiceManager::class);
        $serviceManagerMockBuilder->setMethods(array('get'));
        $serviceManager = $serviceManagerMockBuilder->getMock();
        $serviceManager->expects($this->once())->method('get')->willReturn($entityServiceManager);

        $mockBuilder = $this->getMockBuilder(AbstractPluginManager::class);
        $mockBuilder->setMethods(array('getServiceLocator'));
        $mock = $mockBuilder->getMockForAbstractClass();
        $mock->expects($this->once())->method('getServiceLocator')->willReturn($serviceManager);

        // Act
        $result = $entityNotExistsFactory->createService($mock);

        // Assert
        $this->assertInstanceOf(EntityNotExists::class, $result);
    }
}
