<?php
/**
 * Polder Knowledge / entityservice (https://polderknowledge.com)
 *
 * @link https://github.com/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2016 Polder Knowledge (https://polderknowledge.com)
 * @license https://github.com/polderknowledge/entityservice/blob/master/LICENSE.md MIT
 */

namespace PolderKnowledge\EntityServiceTest\Repository\Doctrine;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ExpressionBuilder;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use PHPUnit_Framework_TestCase;
use PolderKnowledge\EntityService\Repository\Doctrine\ORMRepository;
use PolderKnowledge\EntityServiceTestAsset\MyEntity;

class ORMRepositoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ORMRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var EntityRepository
     */
    private $entityRepository;

    /**
     * @var AbstractQuery
     */
    private $query;

    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    public function setUp()
    {
        $queryMockBuilder = $this->getMockBuilder(AbstractQuery::class);
        $queryMockBuilder->disableOriginalConstructor();
        $queryMockBuilder->setMethods([
            'execute',
            'getOneOrNullResult',
            'getResult',
            'getSingleScalarResult',
        ]);
        $this->query = $queryMockBuilder->getMockForAbstractClass();

        $queryBuilderMockBuilder = $this->getMockBuilder(QueryBuilder::class);
        $queryBuilderMockBuilder->disableOriginalConstructor();
        $this->queryBuilder = $queryBuilderMockBuilder->getMock();
        $this->queryBuilder->expects($this->any())->method('getQuery')->willReturn($this->query);

        $entityRepository = $this->getMockBuilder(EntityRepository::class);
        $entityRepository->disableOriginalConstructor();
        $this->entityRepository = $entityRepository->getMock();
        $this->entityRepository->expects($this->any())->method('createQueryBuilder')->willReturn($this->queryBuilder);

        $entityManagerMock = $this->getMockBuilder(EntityManagerInterface::class);
        $entityManagerMock = $entityManagerMock->disableOriginalConstructor()->getMockForAbstractClass();
        $entityManagerMock->expects($this->any())->method('getRepository')->willReturn($this->entityRepository);
        $this->entityManager = $entityManagerMock;

        $this->repository = new ORMRepository($entityManagerMock, MyEntity::class);
    }

    public function testCountByWithCriteria()
    {
        // Arrange
        $this->query->expects($this->once())->method('getSingleScalarResult')->willReturn(123);

        $criteria = Criteria::create();

        // Act
        $result = $this->repository->countBy($criteria);

        // Assert
        $this->assertEquals(123, $result);
    }

    public function testCountByWithArray()
    {
        // Arrange
        $expr = new ExpressionBuilder();

        $this->query->expects($this->once())->method('getSingleScalarResult')->willReturn(123);
        $this->queryBuilder->expects($this->once())->method('expr')->willReturn($expr);

        $criteria = [
            'test' => 'test',
        ];

        // Act
        $result = $this->repository->countBy($criteria);

        // Assert
        $this->assertEquals(123, $result);
    }

    public function testDelete()
    {
        // Arrange
        $this->entityManager->expects($this->once())->method('remove');

        $entity = new \stdClass();

        // Act
        $this->repository->delete($entity);

        // Assert
        // ...
    }

    public function testDeleteBy()
    {
        // Arrange
        $this->query->expects($this->once())->method('execute');

        $criteria = Criteria::create();

        // Act
        $this->repository->deleteBy($criteria);

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
        $this->entityRepository->expects($this->once())->method('findAll')->willReturn([]);

        // Act
        $result = $this->repository->findAll();

        // Assert
        $this->assertEquals([], $result);
    }

    public function testFindByWithCriteria()
    {
        // Arrange
        $this->query->expects($this->once())->method('getResult')->willReturn([]);

        $criteria = Criteria::create();

        // Act
        $result = $this->repository->findBy($criteria);

        // Assert
        $this->assertEquals([], $result);
    }

    public function testFindByWithArray()
    {
        // Arrange
        $this->query->expects($this->once())->method('getResult')->willReturn([]);

        $criteria = [
            'test' => 'test',
        ];

        // Act
        $result = $this->repository->findBy($criteria);

        // Assert
        $this->assertEquals([], $result);
    }

    public function testFindOneByWithCriteria()
    {
        // Arrange
        $object = new \stdClass();

        $this->query->expects($this->once())->method('getOneOrNullResult')->willReturn($object);

        $criteria = Criteria::create();

        // Act
        $result = $this->repository->findOneBy($criteria);

        // Assert
        $this->assertEquals($object, $result);
    }

    public function testFindOneByWithArray()
    {
        // Arrange
        $object = new \stdClass();

        $this->query->expects($this->once())->method('getOneOrNullResult')->willReturn($object);

        $criteria = [
            'test' => 'test',
        ];

        // Act
        $result = $this->repository->findOneBy($criteria);

        // Assert
        $this->assertEquals($object, $result);
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

        $entity = new \stdClass();

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
