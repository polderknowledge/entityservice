<?php
/**
 * Polder Knowledge / Entity Service (http://polderknowledge.nl)
 *
 * @link http://developers.polderknowledge.nl/gitlab/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2015-2015 Polder Knowledge (http://www.polderknowledge.nl)
 * @license http://polderknowledge.nl/license/proprietary proprietary
 */

namespace PolderKnowledge\EntityServiceTest\Service;

use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use PolderKnowledge\EntityService\Service\AbstractEntityService;
use PolderKnowledge\EntityService\Service\EntityRepositoryManager;
use PolderKnowledge\EntityServiceTest\Service\_Asset\CustomEntityMock;
use stdClass;

class AbstractEntityServiceTest extends PHPUnit_Framework_TestCase
{
    const ENTITYNAME = 'PolderKnowledge\\EntityServiceTest\\Service\\_Asset\\CustomEntityMock';

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $repositoryMock;

    /**
     * @var AbstractEntityService
     */
    protected $service;

    /**
     * @var CustomEntityMock
     */
    protected $entityMock;

    public function setUp()
    {
        $entityName = self::ENTITYNAME;

        $this->entityMock = new $entityName();

        $this->repositoryMock = $this->getMock('PolderKnowledge\\EntityServiceTest\\Service\\_Asset\\RepositoryMock');

        $repositoryManager = new EntityRepositoryManager();
        $repositoryManager->setService($entityName, $this->repositoryMock);

        $this->service = $this->getMockForAbstractClass(
            'PolderKnowledge\\EntityService\\Service\\AbstractEntityService',
            array(
                $repositoryManager,
                $entityName
            )
        );
    }

    public function testServiceHasEventManager()
    {
        // Arrange
        $em = $this->service->getEventManager();

        // Act
        // ...

        // Assert
        $this->assertInstanceOf('Zend\EventManager\EventManagerInterface', $em);
    }

    public function testEventManagerIdentifiers()
    {
        // Arrange
        // ...

        // Act
        $identifiers = $this->service->getEventManager()->getIdentifiers();

        // Assert
        $this->assertEquals(array(
            'PolderKnowledge\\EntityService\\Service\\EntityService',
            self::ENTITYNAME,
        ), $identifiers);
    }

    public function testEventManagerIdentifiersTrimmed()
    {
        // Arrange
        $repositoryManager = new EntityRepositoryManager();
        $repositoryManager->setService(self::ENTITYNAME, $this->repositoryMock);

        $service = $this->getMockForAbstractClass(
            'PolderKnowledge\\EntityService\\Service\\AbstractEntityService',
            array(
                $repositoryManager,
                '\\PolderKnowledge\\EntityServiceTest\\Service\\_Asset\\CustomEntityMock'
            )
        );

        // Act
        $identifiers = $service->getEventManager()->getIdentifiers();

        // Assert
        $this->assertEquals(array(
            'PolderKnowledge\\EntityService\\Service\\EntityService',
            self::ENTITYNAME,
        ), $identifiers);
    }

    /**
     * @expectedException PolderKnowledge\EntityService\Exception\RuntimeException
     * @expectedExceptionMessage Invalid class name provided.
     */
    public function testConstructingNonExistingClassThrowsException()
    {
        // Arrange
        $repositoryManager = new EntityRepositoryManager();
        $repositoryManager->setService(self::ENTITYNAME, $this->repositoryMock);

        $this->getMockForAbstractClass(
            'PolderKnowledge\\EntityService\\Service\\AbstractEntityService',
            array(
                $repositoryManager,
                'NonExistingClass'
            )
        );

        // Act
        // ...

        // Assert
        // ... asserting happens because of the expectedException annotation
    }

    public function testSetterGetterOrder()
    {
        // Arrange
        $order = array('test' => 'DESC');

        // Act
        $this->service->setOrder($order);

        // Assert
        $this->assertEquals($order, $this->service->getOrder());
    }

    /**
     * @expectedException PolderKnowledge\EntityService\Exception\InvalidArgumentException
     * @expectedExceptionMessage Order value can only be DESC or ASC
     */
    public function testOrderValuesCanOnlyBeDescOrAsc()
    {
        // Arrange
        $order = array('test' => 'dummy');

        // Act
        $this->service->setOrder($order);

        // Assert
        // ... asserting happens because of the expectedException annotation
    }

    public function testSetterGetterLimit()
    {
        // Arrange
        $limit = 10;

        // Act
        $this->service->setLimit($limit);

        // Assert
        $this->assertEquals($limit, $this->service->getLimit());
    }

    /**
     * @expectedException PolderKnowledge\EntityService\Exception\InvalidArgumentException
     * @expectedExceptionMessage Expected integer, got stdClass
     */
    public function testSetLimitThrowsExceptionOnInvalidArgument()
    {
        // Arrange
        $limit = new stdClass;

        // Act
        $this->service->setLimit($limit);

        // Assert
        // ... asserting happens because of the expectedException annotation
    }

    public function testSetterGetterOffset()
    {
        // Arrange
        $offset = 10;

        // Act
        $this->service->setOffset($offset);

        // Assert
        $this->assertEquals($offset, $this->service->getOffset());
    }

    /**
     * @expectedException PolderKnowledge\EntityService\Exception\InvalidArgumentException
     * @expectedExceptionMessage Expected integer, got stdClass
     */
    public function testSetOffsetThrowsExceptionOnInvalidArgument()
    {
        // Arrange
        $limit = new stdClass;

        // Act
        $this->service->setOffset($limit);

        // Assert
        // ... asserting happens because of the expectedException annotation
    }

    public function testClearAll()
    {
        // Arrange
        $this->service->setOffset(10);
        $this->service->setLimit(10);
        $this->service->setOrder(array('blaat' => 'DESC'));

        // Act
        $this->service->clear();

        // Assert
        $this->assertEmpty($this->service->getOffset());
        $this->assertEmpty($this->service->getLimit());
        $this->assertEmpty($this->service->getOrder());
    }

    public function testClearSingle()
    {
        // Arrange
        $this->service->setOffset(10);
        $this->service->setLimit(10);
        $this->service->setOrder(array('blaat' => 'DESC'));

        // Act
        $this->service->clear('offset');

        // Assert
        $this->assertEmpty($this->service->getOffset());
        $this->assertEquals(10, $this->service->getLimit());
        $this->assertEquals(array('blaat' => 'DESC'), $this->service->getOrder());
    }

    public function testClearMultiple()
    {
        $this->service->setOffset(10);
        $this->service->setLimit(10);
        $this->service->setOrder(array('blaat' => 'DESC'));

        // Act
        $this->service->clear(array('limit', 'offset'));

        // Assert
        $this->assertEmpty($this->service->getOffset());
        $this->assertEmpty($this->service->getLimit());
        $this->assertEquals(array('blaat' => 'DESC'), $this->service->getOrder());
    }

    /**
     * @expectedException PolderKnowledge\EntityService\Exception\RuntimeException
     * @expectedExceptionMessage Repository is not writable
     */
    public function testPersistOnNonWritableRepositoryThrowsException()
    {
        // Arrange
        $this->service->getRepositoryManager()->setService(
            self::ENTITYNAME,
            $this->getMock('PolderKnowledge\\EntityService\\Repository\\ReadableInterface')
        );

        // Act
        $this->service->persist(new CustomEntityMock());

        // Assert
        // ... asserting happens because of the expectedException annotation
    }

    /**
     * @expectedException PolderKnowledge\EntityService\Exception\RuntimeException
     * @expectedExceptionMessage Repository is not writable
     */
    public function testMultiPersistOnNonWritableRepositoryThrowsException()
    {
        // Arrange
        $this->service->getRepositoryManager()->setService(
            self::ENTITYNAME,
            $this->getMock('PolderKnowledge\\EntityService\\Repository\\ReadableInterface')
        );

        // Act
        $this->service->multiPersist(
            array(
                new CustomEntityMock(),
            )
        );

        // Assert
        // ... asserting happens because of the expectedException annotation
    }

    /**
     * @expectedException PolderKnowledge\EntityService\Exception\RuntimeException
     * @expectedExceptionMessage Repository is not deletable
     */
    public function testDeleteOnNonDeletableRepositoryThrowsException()
    {
        // Arrange
        $this->service->getRepositoryManager()->setService(
            self::ENTITYNAME,
            $this->getMock('PolderKnowledge\\EntityService\\Repository\\ReadableInterface')
        );

        // Act
        $this->service->delete(new CustomEntityMock());

        // Assert
        // ... asserting happens because of the expectedException annotation
    }

    /**
     * @expectedException PolderKnowledge\EntityService\Exception\RuntimeException
     * @expectedExceptionMessage Repository is not readable
     */
    public function testReadFromNonReadableRepositoryThrowsException()
    {
        // Arrange
        $this->service->getRepositoryManager()->setService(
            self::ENTITYNAME,
            $this->getMock('PolderKnowledge\\EntityService\\Repository\\WritableInterface')
        );

        // Act
        $this->service->find(1);

        // Assert
        // ... asserting happens because of the expectedException annotation
    }

    public function testAllPersistRepositoryCallsAreMade()
    {
        // Arrange
        $this->service->getRepositoryForEntity(self::ENTITYNAME)->expects($this->exactly(2))->method('flush');
        $this->service->getRepositoryForEntity(self::ENTITYNAME)->expects($this->exactly(3))->method('persist');

        // Act
        $this->service->persist($this->entityMock);
        $this->service->multiPersist(array($this->entityMock, $this->entityMock));

        // Assert
        // ...
    }

    public function testAllDeleteRepositoryCallsAreMade()
    {
        // Arrange
        $this->service->getRepositoryForEntity(self::ENTITYNAME)->expects($this->exactly(2))->method('flush');
        $this->service->getRepositoryForEntity(self::ENTITYNAME)->expects($this->exactly(1))->method('delete');
        $this->service->getRepositoryForEntity(self::ENTITYNAME)->expects($this->exactly(1))->method('deleteBy');

        // Act
        $this->service->delete($this->entityMock);
        $this->service->deleteBy(array());

        // Assert
        // ...
    }

    public function testAllReadRepositoryCallsAreMade()
    {
        // Arrange
        $repositoryMock = $this->service->getRepositoryForEntity(self::ENTITYNAME);
        $repositoryMock->expects($this->once())->method('find')->will($this->returnValue(array()));
        $repositoryMock->expects($this->once())->method('findBy')->will($this->returnValue(array()));
        $repositoryMock->expects($this->once())->method('findOneBy')->will($this->returnValue(array()));
        $repositoryMock->expects($this->exactly(1))->method('find');
        $repositoryMock->expects($this->exactly(1))->method('findOneBy');
        $repositoryMock->expects($this->exactly(1))->method('findBy');

        // Act
        $this->service->find(1);
        $this->service->findBy(array());
        $this->service->findOneBy(array());

        // Assert
        // ...
    }

    public function testPreStoppedCallReturnsServiceProblem()
    {
        // Arrange
        $this->service->getEventManager()->attach('find', function ($e) {
            $e->setError('dummy message')
                ->setErrorNr(403)
                ->stopPropagation();
        }, 10);

        // Act
        $result = $this->service->find(1);

        // Assert
        $this->assertInstanceOf('PolderKnowledge\\EntityService\\ServiceProblem', $result);
        $this->assertEquals('dummy message', $result->getMessage());
        $this->assertEquals(403, $result->getCode());
    }

    public function testPostStoppingCallIgnored()
    {
        // Arrange
        $this->service->getEventManager()->attach(
            'find', function ($e) {
            $e->setError('dummy message')
                ->setErrorNr(403)
                ->stopPropagation();
        }, -10);

        $this->service->getRepositoryForEntity(self::ENTITYNAME)->expects($this->once())->method('find')->will($this->returnValue(array()));

        // Act
        $result = $this->service->find(1);

        // Assert
        $this->assertInstanceOf('PolderKnowledge\\EntityService\\ServiceResult', $result);
    }
}
