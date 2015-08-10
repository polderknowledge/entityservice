<?php
/**
 * Polder Knowledge / Entity Service (http://polderknowledge.nl)
 *
 * @link http://developers.polderknowledge.nl/gitlab/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2015-2015 Polder Knowledge (http://www.polderknowledge.nl)
 * @license http://polderknowledge.nl/license/proprietary proprietary
 */

namespace PolderKnowledge\EntityServiceTest\Repository\Doctrine;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use PHPUnit_Framework_TestCase;
use PolderKnowledge\EntityService\Entity\Feature\DeletableInterface;
use PolderKnowledge\EntityService\Entity\Feature\IdentifiableInterface;
use PolderKnowledge\EntityService\Repository\Doctrine\ORMRepository;
use PolderKnowledge\EntityServiceTestAsset\MyEntity;

class ORMRepositoryTest extends PHPUnit_Framework_TestCase
{
    private $repository;
    private $entityManager;
    private $entityRepository;
    private $query;

    public function setUp()
    {
        $queryMockBuilder = $this->getMockBuilder(AbstractQuery::class);
        $queryMockBuilder->disableOriginalConstructor();
        $this->query = $queryMockBuilder->getMockForAbstractClass();

        $queryBuilderMockBuilder = $this->getMockBuilder(QueryBuilder::class);
        $queryBuilderMockBuilder->disableOriginalConstructor();
        $queryBuilder = $queryBuilderMockBuilder->getMock();
        $queryBuilder->expects($this->any())->method('getQuery')->willReturn($this->query);

        $entityRepository = $this->getMockBuilder(EntityRepository::class);
        $entityRepository->disableOriginalConstructor();
        $this->entityRepository = $entityRepository->getMock();
        $this->entityRepository->expects($this->any())->method('createQueryBuilder')->willReturn($queryBuilder);

        $entityManagerMock = $this->getMock(EntityManagerInterface::class);
        $entityManagerMock->expects($this->any())->method('getRepository')->willReturn($this->entityRepository);
        $this->entityManager = $entityManagerMock;

        $this->repository = new ORMRepository($entityManagerMock, MyEntity::class);
    }

    public function testDelete()
    {
        // Arrange
        $this->entityManager->expects($this->once())->method('remove');
        $entity = $this->getMock(DeletableInterface::class);

        // Act
        $this->repository->delete($entity);

        // Assert
        // ...
    }

    public function testFind()
    {
        // Arrange
        $this->entityManager->expects($this->once())->method('find');

        // Act
        $this->repository->find(1);

        // Assert
        // ...
    }

    public function testFindAll()
    {
        // Arrange
        $this->entityRepository->expects($this->once())->method('findAll')->willReturn(array());

        // Act
        $result = $this->repository->findAll();

        // Assert
        $this->assertEquals(array(), $result);
    }

    public function testFlush()
    {
        // Arrange
        $this->entityManager->expects($this->once())->method('flush');

        // Act
        $this->repository->flush();

        // Assert
        // ...
    }

    public function testPersist()
    {
        // Arrange
        $this->entityManager->expects($this->once())->method('persist');
        $entity = $this->getMock(IdentifiableInterface::class);

        // Act
        $this->repository->persist($entity);

        // Assert
        // ...
    }

    public function testGetEntityManager()
    {
        // Arrange
        // ...

        // Act
        $result = $this->repository->getEntityManager();

        // Assert
        $this->assertEquals($this->entityManager, $result);
    }

    public function testBeginTransaction()
    {
        // Arrange
        $this->entityManager->expects($this->once())->method('beginTransaction');

        // Act
        $this->repository->beginTransaction();

        // Assert
        // ...
    }

    public function testCommitTransaction()
    {
        // Arrange
        $this->entityManager->expects($this->once())->method('commit');

        // Act
        $this->repository->commitTransaction();

        // Assert
        // ...
    }

    public function testRollbackTransaction()
    {
        // Arrange
        $this->entityManager->expects($this->once())->method('rollback');

        // Act
        $this->repository->rollBackTransaction();

        // Assert
        // ...
    }
}
