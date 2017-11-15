<?php

namespace PolderKnowledge\EntityServiceTest\Repository\Doctrine;

use Doctrine\Common\Collections\Criteria;
use PolderKnowledge\EntityService\EntityService;
use PolderKnowledge\EntityService\Repository\Doctrine\CollectionRepository;
use PolderKnowledge\EntityServiceTestAsset\MyEntity;

class CollectionRepositoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCountWithInitialEmpty()
    {
        $collectionRepository = new CollectionRepository();
        $service = new EntityService($collectionRepository, MyEntity::class);

        self::assertEquals([], $service->findAll());
        self::assertEquals(0, $service->countBy(Criteria::create()));

        $entityOne = new MyEntity(1);
        $service->persist($entityOne);
        self::assertEquals([$entityOne], $service->findAll());
        self::assertEquals(1, $service->countBy(Criteria::create()));
    }

    public function testCountWithInitialOne()
    {
        $entityOne = new MyEntity(1);
        $collectionRepository = new CollectionRepository([$entityOne]);
        $service = new EntityService($collectionRepository, MyEntity::class);

        self::assertEquals(1, $service->countBy(Criteria::create()));

        $entityTwo = new MyEntity(2);
        $service->persist($entityTwo);
        self::assertEquals(2, $service->countBy(Criteria::create()));
    }

    public function testPersistIsIdempotent()
    {
        $collectionRepository = new CollectionRepository();
        $service = new EntityService($collectionRepository, MyEntity::class);

        $entityOne = new MyEntity(1);
        $service->persist($entityOne);
        $service->persist($entityOne);
        self::assertEquals(1, $collectionRepository->getPersisted()->count());
        self::assertEquals([$entityOne], $service->findAll());
    }

    public function testPersistExistingEntity()
    {
        $entityOne = new MyEntity(1);
        $collectionRepository = new CollectionRepository([$entityOne]);
        $service = new EntityService($collectionRepository, MyEntity::class);

        self::assertEquals(0, $collectionRepository->getPersisted()->count());

        $service->persist($entityOne);
        self::assertEquals(1, $collectionRepository->getPersisted()->count());
        self::assertEquals([$entityOne], $service->findAll());
    }

    public function testFindById()
    {
        $entityOne = new MyEntity(1);
        $entityTwo = new MyEntity(2);

        $collectionRepository = new CollectionRepository([$entityOne, $entityTwo]);
        $service = new EntityService($collectionRepository, MyEntity::class);

        self::assertEquals($entityOne, $service->find(1));
        self::assertEquals($entityTwo, $service->find(2));
        self::assertEquals(null, $service->find(3));
    }

    public function testArrayCriteria()
    {
        $entityOne = new MyEntity(1);
        $entityTwo = new MyEntity(2);

        $collectionRepository = new CollectionRepository([$entityOne, $entityTwo]);
        $service = new EntityService($collectionRepository, MyEntity::class);

        self::assertEquals([$entityTwo], array_values($service->findBy(['id' => 2])));
    }
}
