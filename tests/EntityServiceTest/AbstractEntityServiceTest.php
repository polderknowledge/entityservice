<?php
/**
 * Polder Knowledge / Entity Service (http://polderknowledge.nl)
 *
 * @link http://developers.polderknowledge.nl/gitlab/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2015-2015 Polder Knowledge (http://www.polderknowledge.nl)
 * @license http://polderknowledge.nl/license/proprietary proprietary
 */

namespace PolderKnowledge\EntityServiceTest;

use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use PolderKnowledge\EntityService\AbstractEntityService;
use PolderKnowledge\EntityService\Repository\EntityRepositoryInterface;
use PolderKnowledge\EntityService\Repository\Service\EntityRepositoryManager;
use PolderKnowledge\EntityService\Service\EntityService;
use PolderKnowledge\EntityServiceTestAsset\MyEntity;
use PolderKnowledge\EntityServiceTestAsset\MyRepository;
use PolderKnowledge\EntityServiceTestAsset\MyRepositoryNonTransaction;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;

class AbstractEntityServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $repositoryMock;

    /**
     * @var AbstractEntityService
     */
    protected $service;

    /**
     * @var MyEntity
     */
    protected $entityMock;

    public function setUp()
    {
        $this->entityMock = new MyEntity();
        $this->repositoryMock = $this->getMock(MyRepository::class);

        $repositoryManager = new EntityRepositoryManager();
        $repositoryManager->setService(MyEntity::class, $this->repositoryMock);

        $this->service = $this->getMockForAbstractClass(AbstractEntityService::class, array(
            $this->repositoryMock,
            MyEntity::class,
        ));
    }

    public function testServiceHasEventManager()
    {
        // Arrange
        // ...

        // Act
        $eventManager = $this->service->getEventManager();

        // Assert
        $this->assertInstanceOf(EventManagerInterface::class, $eventManager);
    }

    public function testEventManagerIdentifiers()
    {
        // Arrange
        // ...

        // Act
        $identifiers = $this->service->getEventManager()->getIdentifiers();

        // Assert
        $this->assertEquals(array(
            EntityService::class,
            MyEntity::class,
        ), $identifiers);
    }

    public function testSetEventManager()
    {
        // Arrange
        $eventManager = new EventManager();

        // Act
        $this->service->setEventManager($eventManager);

        // Assert
        $this->assertEquals($eventManager, $this->service->getEventManager());
    }

    public function testSetEventManagerWithSameInstance()
    {
        // Arrange
        $eventManager = new EventManager();

        // Act
        $this->service->setEventManager($eventManager);
        $this->service->setEventManager($eventManager);

        // Assert
        $this->assertEquals($eventManager, $this->service->getEventManager());
    }

    public function testSetEventManagerWithOtherInstance()
    {
        // Arrange
        $eventManager = new EventManager();
        $otherEventManager = new EventManager();

        // Act
        $this->service->setEventManager($eventManager);
        $this->service->setEventManager($otherEventManager);

        // Assert
        $this->assertEquals($otherEventManager, $this->service->getEventManager());
    }

    public function testCountBy()
    {
        // Arrange
        // ...

        // Act
        $result = $this->service->countBy(array());

        // Assert
        $this->assertEquals(0, $result);
    }

    /**
     * @expectedException PolderKnowledge\EntityService\Exception\RuntimeException
     * @expectedExceptionMessage The repository for PolderKnowledge\EntityServiceTestAsset\MyEntity is not readable.
     */
    public function testCountByNonReadable()
    {
        // Arrange
        $repository = $this->getMockForAbstractClass(EntityRepositoryInterface::class);
        $service = $this->getMockForAbstractClass(AbstractEntityService::class, array(
            $repository,
            MyEntity::class,
        ));

        // Act
        $service->countBy(array());

        // Assert
        // ...
    }

    public function testDelete()
    {
        // Arrange
        $this->repositoryMock->expects($this->exactly(1))->method('delete');
        $this->repositoryMock->expects($this->exactly(1))->method('flush');

        // Act
        $this->service->delete($this->entityMock);

        // Assert
        // ...
    }

    /**
     * @expectedException PolderKnowledge\EntityService\Exception\RuntimeException
     * @expectedExceptionMessage The repository for PolderKnowledge\EntityServiceTestAsset\MyEntity is not deletable.
     */
    public function testDeleteNonWritable()
    {
        // Arrange
        $repository = $this->getMockForAbstractClass(EntityRepositoryInterface::class);
        $service = $this->getMockForAbstractClass(AbstractEntityService::class, array(
            $repository,
            MyEntity::class,
        ));

        // Act
        $service->delete($this->entityMock);

        // Assert
        // ...
    }

    public function testDeleteBy()
    {
        // Arrange
        $this->repositoryMock->expects($this->exactly(1))->method('deleteBy');
        $this->repositoryMock->expects($this->exactly(1))->method('flush');

        // Act
        $this->service->deleteBy(array());

        // Assert
        // ...
    }

    /**
     * @expectedException PolderKnowledge\EntityService\Exception\RuntimeException
     * @expectedExceptionMessage The repository for PolderKnowledge\EntityServiceTestAsset\MyEntity is not deletable.
     */
    public function testDeleteByNonWritable()
    {
        // Arrange
        $repository = $this->getMockForAbstractClass(EntityRepositoryInterface::class);
        $service = $this->getMockForAbstractClass(AbstractEntityService::class, array(
            $repository,
            MyEntity::class,
        ));

        // Act
        $service->deleteBy(array());

        // Assert
        // ...
    }

    public function testFind()
    {
        // Arrange
        $this->repositoryMock->expects($this->exactly(1))->method('find');

        // Act
        $this->service->find(1);

        // Assert
        // ...
    }

    /**
     * @expectedException PolderKnowledge\EntityService\Exception\RuntimeException
     * @expectedExceptionMessage The repository for PolderKnowledge\EntityServiceTestAsset\MyEntity is not readable.
     */
    public function testFindNonReadable()
    {
        // Arrange
        $repository = $this->getMockForAbstractClass(EntityRepositoryInterface::class);
        $service = $this->getMockForAbstractClass(AbstractEntityService::class, array(
            $repository,
            MyEntity::class,
        ));

        // Act
        $service->find(1);

        // Assert
        // ...
    }

    public function testFindAll()
    {
        // Arrange
        $this->repositoryMock->expects($this->exactly(1))->method('findAll');

        // Act
        $this->service->findAll();

        // Assert
        // ...
    }

    /**
     * @expectedException PolderKnowledge\EntityService\Exception\RuntimeException
     * @expectedExceptionMessage The repository for PolderKnowledge\EntityServiceTestAsset\MyEntity is not readable.
     */
    public function testFindAllNonReadable()
    {
        // Arrange
        $repository = $this->getMockForAbstractClass(EntityRepositoryInterface::class);
        $service = $this->getMockForAbstractClass(AbstractEntityService::class, array(
            $repository,
            MyEntity::class,
        ));

        // Act
        $service->findAll();

        // Assert
        // ...
    }

    public function testFindBy()
    {
        // Arrange
        $this->repositoryMock->expects($this->exactly(1))->method('findBy');

        // Act
        $this->service->findBy(array());

        // Assert
        // ...
    }

    /**
     * @expectedException PolderKnowledge\EntityService\Exception\RuntimeException
     * @expectedExceptionMessage The repository for PolderKnowledge\EntityServiceTestAsset\MyEntity is not readable.
     */
    public function testFindByNonReadable()
    {
        // Arrange
        $repository = $this->getMockForAbstractClass(EntityRepositoryInterface::class);
        $service = $this->getMockForAbstractClass(AbstractEntityService::class, array(
            $repository,
            MyEntity::class,
        ));

        // Act
        $service->findBy(array());

        // Assert
        // ...
    }

    public function testFindOneBy()
    {
        // Arrange
        $this->repositoryMock->expects($this->exactly(1))->method('findOneBy');

        // Act
        $this->service->findOneBy(array());

        // Assert
        // ...
    }

    /**
     * @expectedException PolderKnowledge\EntityService\Exception\RuntimeException
     * @expectedExceptionMessage The repository for PolderKnowledge\EntityServiceTestAsset\MyEntity is not readable.
     */
    public function testFindOneByNonReadable()
    {
        // Arrange
        $repository = $this->getMockForAbstractClass(EntityRepositoryInterface::class);
        $service = $this->getMockForAbstractClass(AbstractEntityService::class, array(
            $repository,
            MyEntity::class,
        ));

        // Act
        $service->findOneBy(array());

        // Assert
        // ...
    }

    public function testPersist()
    {
        // Arrange
        $this->repositoryMock->expects($this->exactly(1))->method('persist');
        $this->repositoryMock->expects($this->exactly(1))->method('flush');

        // Act
        $this->service->persist($this->entityMock);

        // Assert
        // ...
    }

    /**
     * @expectedException PolderKnowledge\EntityService\Exception\RuntimeException
     * @expectedExceptionMessage The repository for PolderKnowledge\EntityServiceTestAsset\MyEntity is not writable.
     */
    public function testPersistNonWritable()
    {
        // Arrange
        $repository = $this->getMockForAbstractClass(EntityRepositoryInterface::class);
        $service = $this->getMockForAbstractClass(AbstractEntityService::class, array(
            $repository,
            MyEntity::class,
        ));

        // Act
        $service->persist($this->entityMock);

        // Assert
        // ...
    }

    public function testMultiPersist()
    {
        // Arrange
        $this->repositoryMock->expects($this->exactly(2))->method('persist');
        $this->repositoryMock->expects($this->exactly(1))->method('flush');

        // Act
        $this->service->multiPersist(array($this->entityMock, $this->entityMock));

        // Assert
        // ...
    }

    /**
     * @expectedException PolderKnowledge\EntityService\Exception\RuntimeException
     * @expectedExceptionMessage The repository for PolderKnowledge\EntityServiceTestAsset\MyEntity is not writable.
     */
    public function testMultiPersistNonWritable()
    {
        // Arrange
        $repository = $this->getMockForAbstractClass(EntityRepositoryInterface::class);
        $service = $this->getMockForAbstractClass(AbstractEntityService::class, array(
            $repository,
            MyEntity::class,
        ));

        // Act
        $service->multiPersist(array());

        // Assert
        // ...
    }

    public function testBeginTransaction()
    {
        // Arrange
        $this->repositoryMock->expects($this->exactly(1))->method('beginTransaction');

        // Act
        $this->service->beginTransaction();

        // Assert
        // ...
    }

    /**
     * @expectedException PolderKnowledge\EntityService\Exception\ServiceException
     * @expectedExceptionMessage The repository for PolderKnowledge\EntityServiceTestAsset\MyEntity doesn't support Transactions
     */
    public function testBeginTransactionWithNonTransaction()
    {
        // Arrange
        $repository = $this->getMockForAbstractClass(MyRepositoryNonTransaction::class);
        $service = $this->getMockForAbstractClass(AbstractEntityService::class, array(
            $repository,
            MyEntity::class,
        ));

        // Act
        $service->beginTransaction();

        // Assert
        // ...
    }

    public function testCommitTransaction()
    {
        // Arrange
        $this->repositoryMock->expects($this->exactly(1))->method('commitTransaction');

        // Act
        $this->service->commitTransaction();

        // Assert
        // ...
    }

    /**
     * @expectedException PolderKnowledge\EntityService\Exception\ServiceException
     * @expectedExceptionMessage The repository for PolderKnowledge\EntityServiceTestAsset\MyEntity doesn't support Transactions
     */
    public function testCommitTransactionWithNonTransaction()
    {
        // Arrange
        $repository = $this->getMockForAbstractClass(MyRepositoryNonTransaction::class);
        $service = $this->getMockForAbstractClass(AbstractEntityService::class, array(
            $repository,
            MyEntity::class,
        ));

        // Act
        $service->commitTransaction();

        // Assert
        // ...
    }

    public function testRollbackTransaction()
    {
        // Arrange
        $this->repositoryMock->expects($this->exactly(1))->method('rollbackTransaction');

        // Act
        $this->service->rollbackTransaction();

        // Assert
        // ...
    }

    /**
     * @expectedException PolderKnowledge\EntityService\Exception\ServiceException
     * @expectedExceptionMessage The repository for PolderKnowledge\EntityServiceTestAsset\MyEntity doesn't support Transactions
     */
    public function testRollbackTransactionWithNonTransaction()
    {
        // Arrange
        $repository = $this->getMockForAbstractClass(MyRepositoryNonTransaction::class);
        $service = $this->getMockForAbstractClass(AbstractEntityService::class, array(
            $repository,
            MyEntity::class,
        ));

        // Act
        $service->rollbackTransaction();

        // Assert
        // ...
    }

    /**
     * @expectedException PolderKnowledge\EntityService\Exception\RuntimeException
     * @expectedExceptionMessage Hello
     */
    public function testTriggerWithError()
    {
        // Arrange
        $repository = $this->getMockForAbstractClass(MyRepositoryNonTransaction::class);
        $service = $this->getMockForAbstractClass(AbstractEntityService::class, array(
            $repository,
            MyEntity::class,
        ));
        $service->getEventManager()->attach('find', function ($e) {
            $e->setError('Hello');
            $e->setErrorNr('123');
            $e->stopPropagation();
        });

        // Act
        $service->find(1);

        // Assert
        // ...
    }
}
