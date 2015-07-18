<?php
/**
 * Polder Knowledge / Entity Service (http://polderknowledge.nl)
 *
 * @link http://developers.polderknowledge.nl/gitlab/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2015-2015 Polder Knowledge (http://www.polderknowledge.nl)
 * @license http://polderknowledge.nl/license/proprietary proprietary
 */

namespace PolderKnowledge\EntityServiceTest\Event;

use PHPUnit_Framework_TestCase;
use PolderKnowledge\EntityService\Event\EventManagerInitializer;
use PolderKnowledge\EntityServiceTestAsset\MyPluginManager;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\SharedEventManager;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;

class EventManagerInitializerTest extends PHPUnit_Framework_TestCase
{
    public function testInitializeWithEventManagerAwareInterface()
    {
        // Arrange
        $eventManagerInitializer = new EventManagerInitializer();

        $sharedEventManagerMock = $this->getMock(SharedEventManager::class);

        $eventManagerMock = $this->getMock(EventManager::class);
        $eventManagerMock->expects($this->once())->method('getSharedManager')->willReturn($sharedEventManagerMock);

        $mockInstance = $this->getMockForAbstractClass(EventManagerAwareInterface::class);
        $mockInstance->expects($this->once())->method('getEventManager')->willReturn($eventManagerMock);

        $serviceManagerMock = $this->getMock(ServiceManager::class);
        $serviceManagerMock->expects($this->once())->method('get')->willReturn($eventManagerMock);

        $pluginManagerMock = $this->getMock(MyPluginManager::class);
        $pluginManagerMock->expects($this->once())->method('getServiceLocator')->willReturn($serviceManagerMock);

        // Act
        $eventManagerInitializer->initialize($mockInstance, $pluginManagerMock);

        // Assert
        // ...
    }

    public function testInitializeWithoutEventManagerAwareInterface()
    {
        // Arrange
        $eventManagerInitializer = new EventManagerInitializer();
        $mock = $this->getMockForAbstractClass(ServiceLocatorInterface::class);

        // Act
        $eventManagerInitializer->initialize(null, $mock);

        // Assert
        // ...
    }
}
