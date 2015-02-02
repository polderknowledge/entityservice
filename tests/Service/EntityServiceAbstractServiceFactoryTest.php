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
use PolderKnowledge\EntityService\Service\EntityServiceAbstractServiceFactory;

class EntityServiceAbstractServiceFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Service\EntityServiceAbstractServiceFactory
     */
    protected $factory;

    protected $serviceLocatorMock;

    public function setUp()
    {
        $this->factory = new EntityServiceAbstractServiceFactory;

        $repositoryManagerMock = $this->getMock('PolderKnowledge\\EntityService\\Service\\EntityRepositoryManager');

        $serviceManagerMock = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceManagerMock->expects($this->any())
             ->method('get')
             ->withAnyParameters()
             ->will($this->returnValue($repositoryManagerMock));

        $this->serviceLocatorMock = $this->getMock('PolderKnowledge\\EntityService\\Service\\EntityServiceManager');
        $this->serviceLocatorMock->expects($this->any())
             ->method('getServiceLocator')
             ->will($this->returnValue($serviceManagerMock));
    }

    public function testManagerCanCreateServiceReturnsFalseOnInValidObject()
    {
        // Arrange
        // ...

        // Act
        // ...

        // Assert
        $this->assertTrue($this->factory->canCreateServiceWithName($this->serviceLocatorMock, 'stdclass', 'stdClass'));
    }

    public function testManagerCanCreateServiceReturnsTrueOnValidObject()
    {
        // Arrange
        // ...

        // Act
        // ...

        // Assert
        $this->assertTrue($this->factory->canCreateServiceWithName(
            $this->serviceLocatorMock,
            'polderKnowledgeentityservicetestserviceassetcustomentitymock',
            'PolderKnowledge\\EntityServiceTest\\Service\\_Asset\\CustomEntityMock'
        ));
    }

    public function testFactoryReturnsEntityServiceForValidObject()
    {
        // Arrange
        // ...

        // Act
        // ...

        // Assert
        $this->assertInstanceOf(
            'PolderKnowledge\\EntityService\\Service\\DefaultEntityService',
            $this->factory->createServiceWithName(
                $this->serviceLocatorMock,
            'polderKnowledgeentityservicetestserviceassetcustomentitymock',
                'PolderKnowledge\\EntityServiceTest\\Service\\_Asset\\CustomEntityMock'
            )
        );
    }
}
