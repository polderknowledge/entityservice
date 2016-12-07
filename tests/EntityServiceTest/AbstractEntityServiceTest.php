<?php
/**
 * Polder Knowledge / entityservice (https://polderknowledge.com)
 *
 * @link https://github.com/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2016 Polder Knowledge (https://polderknowledge.com)
 * @license https://github.com/polderknowledge/entityservice/blob/master/LICENSE.md MIT
 */

namespace PolderKnowledge\EntityServiceTest;

use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use PolderKnowledge\EntityService\AbstractEntityService;
use PolderKnowledge\EntityService\EntityService;
use PolderKnowledge\EntityService\Repository\EntityRepositoryInterface;
use PolderKnowledge\EntityServiceTestAsset\MyEntity;
use PolderKnowledge\EntityServiceTestAsset\MyRepository;
use PolderKnowledge\EntityServiceTestAsset\MyRepositoryNonTransaction;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;

class AbstractEntityServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var MyEntity
     */
    protected $entity;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $repository;

    /**
     * @var AbstractEntityService
     */
    protected $service;

    public function setUp()
    {
        $this->entity = new MyEntity();
        $this->repository = $this->getMockBuilder(MyRepository::class)->getMock();

        $this->service = $this->getMockForAbstractClass(AbstractEntityService::class, [
            $this->repository,
            MyEntity::class,
        ]);
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
        $this->assertEquals([
            'EntityService',
            'PolderKnowledge\EntityService\Service\EntityService',
            MyEntity::class,
        ], $identifiers);
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
        $result = $this->service->countBy([]);

        // Assert
        $this->assertEquals(0, $result);
    }

    /**
     * @expectedException \PolderKnowledge\EntityService\Exception\RuntimeException
     * @expectedExceptionMessage It is not possible to read entities of type "PolderKnowledge\EntityServiceTestAsset\MyEntity" from its repository.
     */
    public function testCountByNonReadable()
    {
        // Arrange
        $repository = $this->getMockForAbstractClass(EntityRepositoryInterface::class);
        $service = $this->getMockForAbstractClass(AbstractEntityService::class, [
            $repository,
            MyEntity::class,
        ]);

        // Act
        $service->countBy([]);

        // Assert
        // ...
    }

    public function testDelete()
    {
        // Arrange
        $this->repository->expects($this->exactly(1))->method('delete');

        // Act
        $this->service->delete($this->entity);

        // Assert
        // ...
    }

    /**
     * @expectedException \PolderKnowledge\EntityService\Exception\RuntimeException
     * @expectedExceptionMessage The entities of type "PolderKnowledge\EntityServiceTestAsset\MyEntity" cannot be deleted from its repository.
     */
    public function testDeleteNonDeletable()
    {
        // Arrange
        $repository = $this->getMockForAbstractClass(EntityRepositoryInterface::class);
        $service = $this->getMockForAbstractClass(AbstractEntityService::class, [
            $repository,
            MyEntity::class,
        ]);

        // Act
        $service->delete($this->entity);

        // Assert
        // ...
    }

    public function testDeleteBy()
    {
        // Arrange
        $this->repository->expects($this->exactly(1))->method('deleteBy');

        // Act
        $this->service->deleteBy([]);

        // Assert
        // ...
    }

    /**
     * @expectedException \PolderKnowledge\EntityService\Exception\RuntimeException
     * @expectedExceptionMessage The entities of type "PolderKnowledge\EntityServiceTestAsset\MyEntity" cannot be deleted from its repository.
     */
    public function testDeleteByNonDeltable()
    {
        // Arrange
        $repository = $this->getMockForAbstractClass(EntityRepositoryInterface::class);
        $service = $this->getMockForAbstractClass(AbstractEntityService::class, [
            $repository,
            MyEntity::class,
        ]);

        // Act
        $service->deleteBy([]);

        // Assert
        // ...
    }

    public function testFind()
    {
        // Arrange
        $this->repository->expects($this->exactly(1))->method('find');

        // Act
        $this->service->find(1);

        // Assert
        // ...
    }

    /**
     * @expectedException \PolderKnowledge\EntityService\Exception\RuntimeException
     * @expectedExceptionMessage It is not possible to read entities of type "PolderKnowledge\EntityServiceTestAsset\MyEntity" from its repository.
     */
    public function testFindNonReadable()
    {
        // Arrange
        $repository = $this->getMockForAbstractClass(EntityRepositoryInterface::class);
        $service = $this->getMockForAbstractClass(AbstractEntityService::class, [
            $repository,
            MyEntity::class,
        ]);

        // Act
        $service->find(1);

        // Assert
        // ...
    }

    public function testFindAll()
    {
        // Arrange
        $this->repository->expects($this->exactly(1))->method('findAll');

        // Act
        $this->service->findAll();

        // Assert
        // ...
    }

    /**
     * @expectedException \PolderKnowledge\EntityService\Exception\RuntimeException
     * @expectedExceptionMessage It is not possible to read entities of type "PolderKnowledge\EntityServiceTestAsset\MyEntity" from its repository.
     */
    public function testFindAllNonReadable()
    {
        // Arrange
        $repository = $this->getMockForAbstractClass(EntityRepositoryInterface::class);
        $service = $this->getMockForAbstractClass(AbstractEntityService::class, [
            $repository,
            MyEntity::class,
        ]);

        // Act
        $service->findAll();

        // Assert
        // ...
    }

    public function testFindBy()
    {
        // Arrange
        $this->repository->expects($this->exactly(1))->method('findBy');

        // Act
        $this->service->findBy([]);

        // Assert
        // ...
    }

    /**
     * @expectedException \PolderKnowledge\EntityService\Exception\RuntimeException
     * @expectedExceptionMessage It is not possible to read entities of type "PolderKnowledge\EntityServiceTestAsset\MyEntity" from its repository.
     */
    public function testFindByNonReadable()
    {
        // Arrange
        $repository = $this->getMockForAbstractClass(EntityRepositoryInterface::class);
        $service = $this->getMockForAbstractClass(AbstractEntityService::class, [
            $repository,
            MyEntity::class,
        ]);

        // Act
        $service->findBy([]);

        // Assert
        // ...
    }

    public function testFindOneBy()
    {
        // Arrange
        $this->repository->expects($this->exactly(1))->method('findOneBy');

        // Act
        $this->service->findOneBy([]);

        // Assert
        // ...
    }

    /**
     * @expectedException \PolderKnowledge\EntityService\Exception\RuntimeException
     * @expectedExceptionMessage It is not possible to read entities of type "PolderKnowledge\EntityServiceTestAsset\MyEntity" from its repository.
     */
    public function testFindOneByNonReadable()
    {
        // Arrange
        $repository = $this->getMockForAbstractClass(EntityRepositoryInterface::class);
        $service = $this->getMockForAbstractClass(AbstractEntityService::class, [
            $repository,
            MyEntity::class,
        ]);

        // Act
        $service->findOneBy([]);

        // Assert
        // ...
    }

    public function testPersist()
    {
        // Arrange
        $this->repository->expects($this->exactly(1))->method('persist');

        // Act
        $this->service->persist($this->entity);

        // Assert
        // ...
    }

    /**
     * @expectedException \PolderKnowledge\EntityService\Exception\RuntimeException
     * @expectedExceptionMessage The entities of type "PolderKnowledge\EntityServiceTestAsset\MyEntity" cannot be written to its repository.
     */
    public function testPersistNonWritable()
    {
        // Arrange
        $repository = $this->getMockForAbstractClass(EntityRepositoryInterface::class);
        $service = $this->getMockForAbstractClass(AbstractEntityService::class, [
            $repository,
            MyEntity::class,
        ]);

        // Act
        $service->persist($this->entity);

        // Assert
        // ...
    }

    public function testFlush()
    {
        // Arrange
        $this->repository->expects($this->exactly(1))->method('flush');

        // Act
        $this->service->flush($this->entity);

        // Assert
        // ...
    }

    /**
     * @expectedException \PolderKnowledge\EntityService\Exception\RuntimeException
     * @expectedExceptionMessage The entities of type "PolderKnowledge\EntityServiceTestAsset\MyEntity" cannot be written to its repository.
     */
    public function testFlushNonWritable()
    {
        // Arrange
        $repository = $this->getMockForAbstractClass(EntityRepositoryInterface::class);
        $service = $this->getMockForAbstractClass(AbstractEntityService::class, [
            $repository,
            MyEntity::class,
        ]);

        // Act
        $service->flush([]);

        // Assert
        // ...
    }

    public function testBeginTransaction()
    {
        // Arrange
        $this->repository->expects($this->exactly(1))->method('beginTransaction');

        // Act
        $this->service->beginTransaction();

        // Assert
        // ...
    }

    /**
     * @expectedException \PolderKnowledge\EntityService\Exception\ServiceException
     * @expectedExceptionMessage The repository for PolderKnowledge\EntityServiceTestAsset\MyEntity doesn't support Transactions
     */
    public function testBeginTransactionWithNonTransaction()
    {
        // Arrange
        $repository = $this->getMockForAbstractClass(MyRepositoryNonTransaction::class);
        $service = $this->getMockForAbstractClass(AbstractEntityService::class, [
            $repository,
            MyEntity::class,
        ]);

        // Act
        $service->beginTransaction();

        // Assert
        // ...
    }

    public function testCommitTransaction()
    {
        // Arrange
        $this->repository->expects($this->exactly(1))->method('commitTransaction');

        // Act
        $this->service->commitTransaction();

        // Assert
        // ...
    }

    /**
     * @expectedException \PolderKnowledge\EntityService\Exception\ServiceException
     * @expectedExceptionMessage The repository for PolderKnowledge\EntityServiceTestAsset\MyEntity doesn't support Transactions
     */
    public function testCommitTransactionWithNonTransaction()
    {
        // Arrange
        $repository = $this->getMockForAbstractClass(MyRepositoryNonTransaction::class);
        $service = $this->getMockForAbstractClass(AbstractEntityService::class, [
            $repository,
            MyEntity::class,
        ]);

        // Act
        $service->commitTransaction();

        // Assert
        // ...
    }

    public function testRollbackTransaction()
    {
        // Arrange
        $this->repository->expects($this->exactly(1))->method('rollbackTransaction');

        // Act
        $this->service->rollbackTransaction();

        // Assert
        // ...
    }

    /**
     * @expectedException \PolderKnowledge\EntityService\Exception\ServiceException
     * @expectedExceptionMessage The repository for PolderKnowledge\EntityServiceTestAsset\MyEntity doesn't support Transactions
     */
    public function testRollbackTransactionWithNonTransaction()
    {
        // Arrange
        $repository = $this->getMockForAbstractClass(MyRepositoryNonTransaction::class);
        $service = $this->getMockForAbstractClass(AbstractEntityService::class, [
            $repository,
            MyEntity::class,
        ]);

        // Act
        $service->rollbackTransaction();

        // Assert
        // ...
    }

    /**
     * @expectedException \PolderKnowledge\EntityService\Exception\RuntimeException
     * @expectedExceptionMessage Hello
     */
    public function testTriggerWithError()
    {
        // Arrange
        $repository = $this->getMockForAbstractClass(MyRepositoryNonTransaction::class);

        $service = $this->getMockForAbstractClass(AbstractEntityService::class, [
            $repository,
            MyEntity::class,
        ]);

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
