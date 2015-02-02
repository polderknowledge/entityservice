<?php
/**
 * Polder Knowledge / Entity Service (http://polderknowledge.nl)
 *
 * @link http://developers.polderknowledge.nl/gitlab/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2015-2015 Polder Knowledge (http://www.polderknowledge.nl)
 * @license http://polderknowledge.nl/license/proprietary proprietary
 */

namespace PolderKnowledge\EntityServiceTest\Repository;

use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use PolderKnowledge\EntityService\Repository\DoctrineORMRepository;
use stdClass;

class DoctrineOrmRepositoryTest extends PHPUnit_Framework_TestCase
{
    const MY_ENTITY = 'My\\Entity';

    /**
     * @var DoctrineORMRepository
     */
    private $repository;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $entityManagerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $repositoryMock;

    public function setUp()
    {
        $this->entityManagerMock = $this->getMock('Doctrine\\ORM\\EntityManagerInterface');

        $this->repositoryMock = $this->getMockBuilder('Doctrine\\ORM\\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->entityManagerMock
            ->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($this->repositoryMock));

        $this->repository = new DoctrineORMRepository($this->entityManagerMock, static::MY_ENTITY);
    }

    public function testFind()
    {
        // Arrange
        $entity = new stdClass;

        $this->entityManagerMock
            ->expects($this->once())
            ->method('find')
            ->with(static::MY_ENTITY, 1)
            ->will($this->returnValue($entity));

        // Act
        // ...

        // Assert
        $this->assertEquals($entity, $this->repository->find(1));
    }

    public function testFindAll()
    {
        // Arrange
        $result = array(new stdClass(), new stdClass());

        $this->repositoryMock
            ->expects($this->once())
            ->method('findAll')
            ->will($this->returnValue($result));

        // Act
        // ...

        // Assert
        $this->assertEquals($result, $this->repository->findAll());
    }

    public function testFindBy()
    {
        // Arrange
        $result = array(new stdClass(), new stdClass());

        $criteria = array('foo', 'bar');
        $order = array('column');
        $limit = 100;
        $offset = 10;

        $this->repositoryMock
            ->expects($this->once())
            ->method('findBy')
            ->with($criteria, $order, $limit, $offset)
            ->will($this->returnValue($result));

        // Act
        // ...

        // Assert
        $this->assertEquals($result, $this->repository->findBy($criteria, $order, $limit, $offset));
    }

    public function testFindByOne()
    {
        // Arrange
        $entity = new stdClass();
        $criteria = array('foo', 'bar');

        $this->repositoryMock
            ->expects($this->once())
            ->method('FindOneBy')
            ->with($criteria)
            ->will($this->returnValue($entity));

        // Act
        // ...

        // Assert
        $this->assertEquals($entity, $this->repository->findOneBy($criteria));
    }

    public function testFlush()
    {
        // Arrange
        $identifable = $this->getMock('PolderKnowledge\\EntityService\\Feature\\IdentifiableInterface');
        $this->entityManagerMock
            ->expects($this->once())
            ->method('flush')
            ->with($identifable);

        // Act
        $this->repository->flush($identifable);

        // Assert
        // ...
    }

    public function testPersist()
    {
        // Arrange
        $identifable = $this->getMock('PolderKnowledge\\EntityService\\Feature\\IdentifiableInterface');

        $this->entityManagerMock
            ->expects($this->once())
            ->method('persist')
            ->with($identifable);

        // Act
        $this->repository->persist($identifable);

        // Assert
        // ...
    }

    public function testDelete()
    {
        // Arrange
        $deletable = $this->getMock('PolderKnowledge\\EntityService\\Feature\\DeletableInterface');

        $this->entityManagerMock
            ->expects($this->once())
            ->method('remove')
            ->with($deletable);

        // Act
        $this->repository->delete($deletable);

        // Assert
        // ...
    }

    public function testDeleteBy()
    {
        // Arrange
        $result = array(
            $this->getMock('PolderKnowledge\\EntityService\\Feature\\DeletableInterface'),
            $this->getMock('PolderKnowledge\\EntityService\\Feature\\DeletableInterface')
        );

        $criteria = array('foo', 'bar');

        $this->repositoryMock
            ->expects($this->once())
            ->method('findBy')
            ->with($criteria)
            ->will($this->returnValue($result));

        $this->entityManagerMock
            ->expects($this->exactly(count($result)))
            ->method('remove')
            ->withAnyParameters();

        // Act
        $this->repository->deleteBy($criteria);

        // Assert
        // ...
    }

    public function testBeginTransaction()
    {
        // Arrange
        $this->entityManagerMock
            ->expects($this->exactly(1))
            ->method('beginTransaction');

        // Act
        $this->repository->beginTransaction();

        // Assert
        // ...
    }

    public function testCommitTransaction()
    {
        // Arrange
        $this->entityManagerMock
            ->expects($this->exactly(1))
            ->method('commit');

        // Act
        $this->repository->commitTransaction();

        // Assert
        // ...
    }

    public function testRollbackTransaction()
    {
        // Arrange
        $this->entityManagerMock
            ->expects($this->exactly(1))
            ->method('rollback');

        // Act
        $this->repository->rollBackTransaction();

        // Assert
        // ...
    }
}
