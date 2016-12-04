<?php
/**
 * Polder Knowledge / entityservice (https://polderknowledge.com)
 *
 * @link https://github.com/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2016 Polder Knowledge (https://polderknowledge.com)
 * @license https://github.com/polderknowledge/entityservice/blob/master/LICENSE.md MIT
 */

namespace PolderKnowledge\EntityService\Repository\Doctrine;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use PolderKnowledge\EntityService\Repository\EntityRepositoryInterface;
use PolderKnowledge\EntityService\Repository\Feature\DeletableInterface;
use PolderKnowledge\EntityService\Repository\Feature\FlushableInterface;
use PolderKnowledge\EntityService\Repository\Feature\ReadableInterface;
use PolderKnowledge\EntityService\Repository\Feature\TransactionAwareInterface;
use PolderKnowledge\EntityService\Repository\Feature\WritableInterface;
use UnexpectedValueException;

/**
 * Class ORMRepository is a default implementation for a repository using doctrine orm.
 */
class ORMRepository implements
    EntityRepositoryInterface,
    DeletableInterface,
    FlushableInterface,
    ReadableInterface,
    TransactionAwareInterface,
    WritableInterface
{
    /**
     * The Doctrine ORM entity manager that is used to retrieve and store data.
     *
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * The FQCN of the entity to work with.
     *
     * @var string
     */
    protected $entityName;

    /**
     * The Doctrine ORM repository that is used to retrieve and store data.
     *
     * @var ObjectRepository
     */
    protected $repository;

    /**
     * Initializes the self::$entityManager and self::$entityName
     *
     * @param EntityManagerInterface $entityManager The Doctrine ORM entity manager used to retrieve and store data.
     * @param string $entityName The FQCN of the entity to work with.
     */
    public function __construct(EntityManagerInterface $entityManager, $entityName)
    {
        $this->entityManager = $entityManager;
        $this->entityName = $entityName;
    }

    /**
     * Counts entities by a set of criteria.
     *
     * Optionally sorting and limiting details can be passed. An implementation may throw an UnexpectedValueException
     * if certain values of the sorting or limiting details are not supported.
     *
     * @param array|Criteria $criteria The criteria to find entities by.
     * @return int Returns the amount of entities that are found.
     * @throws \Doctrine\ORM\Query\QueryException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws UnexpectedValueException
     */
    public function countBy($criteria)
    {
        /* @var $queryBuilder \Doctrine\ORM\QueryBuilder */
        $queryBuilder = $this->getRepository()->createQueryBuilder('e');
        $queryBuilder->select('count(e)');

        if ($criteria instanceof Criteria) {
            $clonedCriteria = clone $criteria;
            $clonedCriteria->setFirstResult(null);
            $clonedCriteria->setMaxResults(null);

            $queryBuilder->addCriteria($clonedCriteria);
        } elseif (!empty($criteria)) {
            foreach ($criteria as $field => $value) {
                $queryBuilder->andWhere($queryBuilder->expr()->eq('e.' . $field, ':' . $field));
                $queryBuilder->setParameter(':' . $field, $value);
            }
        }

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }

    /**
     * Deletes the given object
     *
     * @param object $entity The entity to delete.
     */
    public function delete($entity)
    {
        $this->entityManager->remove($entity);
    }

    /**
     * Removes objects by a set of criteria.
     *
     * @param array|Criteria $criteria
     * @return void
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function deleteBy($criteria)
    {
        $queryBuilder = $this->getQueryBuilder($criteria);
        $queryBuilder->delete('e');
        $queryBuilder->getQuery()->execute();
    }

    /**
     * Tries to find an entity in the repository by the given identifier.
     *
     * @param mixed $id The id of the entity which can be any type of object.
     * @return object|null Returns the entity that matches the identifier or null when no instance is found.
     */
    public function find($id)
    {
        return $this->entityManager->find($this->entityName, $id);
    }

    /**
     * Tries to find all entities in the repository.
     *
     * @return object[] Returns an array with entities that are found.
     */
    public function findAll()
    {
        return $this->getRepository()->findAll();
    }

    /**
     * Tries to find entities by a set of criteria.
     *
     * Optionally sorting and limiting details can be passed. An implementation may throw an UnexpectedValueException
     * if certain values of the sorting or limiting details are not supported.
     *
     * @param array|Criteria $criteria The criteria to find entities by.
     * @return array Returns an array with found entities. Returns an empty array when no entities are found.
     * @throws \Doctrine\ORM\Query\QueryException
     * @throws UnexpectedValueException Thrown when provided parameters are not supported.
     */
    public function findBy($criteria)
    {
        if (!$criteria instanceof Criteria) {
            $criteriaParams = $criteria;

            $criteria = Criteria::create();
            foreach ($criteriaParams as $name => $value) {
                $criteria->andWhere(Criteria::expr()->eq($name, $value));
            }
        }

        $queryBuilder = $this->getQueryBuilder($criteria);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Tries to find a single entity by a set of criteria.
     *
     * @param array|Criteria $criteria The criteria. The criteria to find the entity by.
     * @return object|null Returns the entity that is found or null when no entity is found.
     * @throws \Doctrine\ORM\Query\QueryException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneBy($criteria)
    {
        if (!$criteria instanceof Criteria) {
            $criteriaParams = $criteria;

            $criteria = Criteria::create();
            foreach ($criteriaParams as $name => $value) {
                $criteria->andWhere(Criteria::expr()->eq($name, $value));
            }
        }

        $criteria->setFirstResult(0);
        $criteria->setMaxResults(1);

        $queryBuilder = $this->getQueryBuilder($criteria);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /**
     * Creates a new query builder using the $criteria.
     *
     * @param Criteria $criteria
     * @return \Doctrine\ORM\QueryBuilder
     * @throws \Doctrine\ORM\Query\QueryException
     */
    protected function getQueryBuilder(Criteria $criteria)
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->getRepository()->createQueryBuilder('e');
        $queryBuilder->addCriteria($criteria);

        return $queryBuilder;
    }

    /**
     * Gets the Doctrine EntityManager.
     *
     * @return EntityManagerInterface
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * Returns the doctrine repository.
     *
     * @return ObjectRepository
     */
    public function getRepository()
    {
        if ($this->repository === null) {
            $this->repository = $this->entityManager->getRepository($this->entityName);
        }

        return $this->repository;
    }

    /**
     * Will flush the given entity. If non given all queued entities will be flushed.
     *
     * @param object $entity The entity to flush.
     * @return void
     */
    public function flush($entity = null)
    {
        $this->entityManager->flush($entity);
    }

    /**
     * Persist the given entity.
     *
     * @param object $entity The entity to persist.
     */
    public function persist($entity)
    {
        $this->entityManager->persist($entity);
    }

    /**
     * Starts a new transaction.
     *
     * @return void
     */
    public function beginTransaction()
    {
        $this->entityManager->beginTransaction();
    }

    /**
     * Commits a started transaction.
     *
     * @return void
     */
    public function commitTransaction()
    {
        $this->entityManager->commit();
    }

    /**
     * Rolls back a started transaction.
     *
     * @return void
     */
    public function rollbackTransaction()
    {
        $this->entityManager->rollback();
    }
}
