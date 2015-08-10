<?php
/**
 * Polder Knowledge / Entity Service (http://polderknowledge.nl)
 *
 * @link http://developers.polderknowledge.nl/gitlab/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2015-2015 Polder Knowledge (http://www.polderknowledge.nl)
 * @license http://polderknowledge.nl/license/proprietary proprietary
 */

namespace PolderKnowledge\EntityServiceTest\Repository\Doctrine\Service;

use Doctrine\ORM\EntityManager;
use PHPUnit_Framework_TestCase;
use PolderKnowledge\EntityService\Repository\Doctrine\ORMRepository;
use PolderKnowledge\EntityService\Repository\Doctrine\Service\RepositoryAbstractFactory;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;

class RepositoryAbstractFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testCanCreateServiceWithName()
    {
        // Arrange
        $factory = new RepositoryAbstractFactory();
        $mock = $this->getMockForAbstractClass(ServiceLocatorInterface::class);

        // Act
        $result = $factory->canCreateServiceWithName($mock, null, null);

        // Assert
        $this->assertTrue($result);
    }

    public function testCreateServiceWithName()
    {
        // Arrange
        $factory = new RepositoryAbstractFactory();

        $entityManagerMockBuilder = $this->getMockBuilder(EntityManager::class);
        $entityManagerMockBuilder->disableOriginalConstructor();
        $entityManager = $entityManagerMockBuilder->getMock();

        $serviceLocatorMockBuilder = $this->getMockBuilder(ServiceManager::class);
        $serviceLocatorMockBuilder->setMethods(array('get'));
        $serviceLocator = $serviceLocatorMockBuilder->getMock();
        $serviceLocator->expects($this->once())->method('get')->willReturn($entityManager);

        $pluginManagerMockBuilder = $this->getMockBuilder(AbstractPluginManager::class);
        $pluginManagerMockBuilder->setMethods(array('getServiceLocator'));
        $pluginManager = $pluginManagerMockBuilder->getMockForAbstractClass();
        $pluginManager->expects($this->once())->method('getServiceLocator')->willReturn($serviceLocator);

        // Act
        $result = $factory->createServiceWithName($pluginManager, null, null);

        // Assert
        $this->assertInstanceOf(ORMRepository::class, $result);
    }
}
