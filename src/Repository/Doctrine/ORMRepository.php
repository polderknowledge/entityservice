<?php
/**
 * Polder Knowledge / Entity Service (http://polderknowledge.nl)
 *
 * @link http://developers.polderknowledge.nl/gitlab/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2015-2015 Polder Knowledge (http://www.polderknowledge.nl)
 * @license http://polderknowledge.nl/license/proprietary proprietary
 */

namespace PolderKnowledge\EntityService\Repository\Doctrine;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PolderKnowledge\EntityService\Entity\Feature\DeletableInterface as EntityFeatureDeletable;
use PolderKnowledge\EntityService\Entity\Feature\IdentifiableInterface as EntityFeatureIdentifiable;
use PolderKnowledge\EntityService\Repository\Doctrine\QueryBuilderExpressionVisitor;
use PolderKnowledge\EntityService\Repository\EntityRepositoryInterface;
use PolderKnowledge\EntityService\Repository\Feature\DeletableInterface;
use PolderKnowledge\EntityService\Repository\Feature\FlushableInterface;
use PolderKnowledge\EntityService\Repository\Feature\ReadableInterface;
use PolderKnowledge\EntityService\Repository\Feature\TransactionAwareInterface;
use PolderKnowledge\EntityService\Repository\Feature\WritableInterface;

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
     * @throws UnexpectedValueException
     */
    public function countBy($criteria)
    {
        /* @var $queryBuilder \Doctrine\ORM\QueryBuilder */
        $queryBuilder = $this->getRepository()->createQueryBuilder('e');
        $queryBuilder->select('count(e)');

        if ($criteria instanceof Criteria) {
            $queryBuilder->addCriteria($criteria);
        } elseif (!empty($criteria)) {
            foreach ($criteria as $field => $value) {
                $queryBuilder->andWhere($queryBuilder->expr()->eq('e.' . $field, ':'.$field));
                $queryBuilder->setParameter(':'.$field, $value);
            }
        }

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }

    /**
     * Deletes the given object
     *
     * @param EntityFeatureDeletable $entity The entity to delete.
     */
    public function delete(EntityFeatureDeletable $entity)
    {
        $this->entityManager->remove($entity);
    }

    /**
     * Removes objects by a set of criteria.
     *
     * @param array|Criteria $criteria
     * @return int Returns the number of records that are deleted.
     */
    public function deleteBy($criteria)
    {
        $entities = $this->findBy($criteria);

        foreach ($entities as $entity) {
            $this->entityManager->remove($entity);
        }
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
     * @return object Returns the entity that is found or null when no entity is found.
     */
    public function findOneBy($criteria)
    {
        if (!$criteria instanceof Criteria) {
            $criteriaParams = $criteria;

            $criteria = Criteria::create();
            $criteria->where($criteriaParams);
        }

        $queryBuilder = $this->getQueryBuilder($criteria);

        return $queryBuilder->getQuery()->getSingleResult();
    }

    /**
     * Creates a new query builder using the $criteria.
     *
     * @param Criteria $criteria
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function getQueryBuilder(Criteria $criteria)
    {
        $queryBuilder = $this->getRepository()->createQueryBuilder('e');

        $visitor = new QueryBuilderExpressionVisitor('e', $queryBuilder);

        $whereExpression = $criteria->getWhereExpression();
        if ($whereExpression !== null) {
            $queryBuilder->andWhere($visitor->dispatch($whereExpression));
            $queryBuilder->setParameters($visitor->getParameters());
        }

        if ($criteria->getFirstResult() !== null) {
            $queryBuilder->setFirstResult($criteria->getFirstResult());
        }

        if ($criteria->getMaxResults() !== null) {
            $queryBuilder->setMaxResults($criteria->getMaxResults());
        }

        if ($criteria->getOrderings()) {
            foreach ($criteria->getOrderings() as $field => $direction) {
                $queryBuilder->addOrderBy(sprintf('e.%s', $field), $direction);
            }
        }

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
     * @param EntityFeatureIdentifiable $entity The entity to flush.
     * @return void
     */
    public function flush(EntityFeatureIdentifiable $entity = null)
    {
        $this->entityManager->flush($entity);
    }

    /**
     * Persist the given entity.
     *
     * @param EntityFeatureIdentifiable $entity The entity to persist.
     */
    public function persist(EntityFeatureIdentifiable $entity)
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
    public function rollBackTransaction()
    {
        $this->entityManager->rollback();
    }
}
