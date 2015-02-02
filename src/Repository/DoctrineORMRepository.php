<?php
/**
 * Polder Knowledge / Entity Service (http://polderknowledge.nl)
 *
 * @link http://developers.polderknowledge.nl/gitlab/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2015-2015 Polder Knowledge (http://www.polderknowledge.nl)
 * @license http://polderknowledge.nl/license/proprietary proprietary
 */

namespace PolderKnowledge\EntityService\Repository;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use PolderKnowledge\EntityService\Feature\DeletableInterface as FeatureDeletable;
use PolderKnowledge\EntityService\Feature\IdentifiableInterface;
use PolderKnowledge\EntityService\Repository\DoctrineQueryBuilderExpressionVisitor;

/**
 * A Doctrine ORM implementation of a repository class.
 */
class DoctrineORMRepository implements
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
     * @var EntityRepository
     */
    protected $repository;

    /**
     * Initializes a new instance of this class.
     *
     * @param EntityManagerInterface $entityManager The Doctrine ORM entity manager used to retrieve and store data.
     * @param string $entityName The FQCN of the entity to work with.
     */
    public function __construct(EntityManagerInterface $entityManager, $entityName)
    {
        $this->entityManager = $entityManager;
        $this->entityName = $entityName;
    }

    // Implementation of ReadableInterface::countBy
    public function countBy($criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        if ($criteria instanceof Criteria) {
            $queryBuilder = $this->getQueryBuilder($criteria, $orderBy, $limit, $offset);
            $queryBuilder->select('count(e)');

            return $queryBuilder->getQuery()->getSingleScalarResult();
        }

        $queryBuilder = $this->getRepository()->createQueryBuilder('e');
        $queryBuilder->select('count(e)');

        if (!empty($criteria)) {
            foreach($criteria as $field => $value) {
                $queryBuilder->andWhere($queryBuilder->expr()->eq('e.' . $field, ':'.$field));
                $queryBuilder->setParameter(':'.$field, $value);
            }
        }

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }

    // Implementation of ReadableInterface::find
    public function find($id)
    {
        return $this->entityManager->find($this->entityName, $id);
    }

    // Implementation of ReadableInterface::findAll
    public function findAll()
    {
        return $this->getRepository()->findAll();
    }

    // Implementation of ReadableInterface::findBy
    public function findBy($criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        if ($criteria instanceof Criteria) {
            $queryBuilder = $this->getQueryBuilder($criteria, $orderBy, $limit, $offset);

            return $queryBuilder->getQuery()->execute();
        }

        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    // Implementation of ReadableInterface::findOneBy
    public function findOneBy($criteria)
    {
        if ($criteria instanceof Criteria) {
            $queryBuilder = $this->getQueryBuilder($criteria, null, 1);
            $result = $queryBuilder->getQuery()->getResult();
            return current($result);
        }

        return $this->getRepository()->findOneBy($criteria);
    }

    /**
     * Creates the QueryBuilder by the given criteria.
     *
     * @param Criteria $criteria The criteria to find entities by.
     * @param array|null $orderBy An array with fields to order by. Set to null for default ordering.
     * @param int|null $limit The amount of entities to retrieve. Set to null for default amount.
     * @param int|null $offset The offset to start retrieving entities from. Set to null for the default offset.
     * @return QueryBuilder
     */
    protected function getQueryBuilder(Criteria $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $queryBuilder = $this->getRepository()->createQueryBuilder('e');

        $visitor = new DoctrineQueryBuilderExpressionVisitor('e', $queryBuilder);

        $whereExpression = $criteria->getWhereExpression();
        if ($whereExpression !== null) {
            $queryBuilder->andWhere($visitor->dispatch($whereExpression));
            $queryBuilder->setParameters($visitor->getParameters());
        }

        if ($offset !== null) {
            $queryBuilder->setFirstResult($offset);
        }

        if ($limit !== null) {
            $queryBuilder->setMaxResults($limit);
        }

        if (is_array($orderBy)) {
            foreach ($orderBy as $field => $direction) {
                $queryBuilder->addOrderBy(sprintf('e.%s', $field), $direction);
            }
        }

        return $queryBuilder;
    }

    /**
     * Gets the Doctrine ORM EntityRepository.
     *
     * @return EntityRepository
     */
    protected function getRepository()
    {
        if ($this->repository === null) {
            $this->repository = $this->entityManager->getRepository($this->entityName);
        }

        return $this->repository;
    }

    // Implementation of FlushableInterface::persist
    public function flush(IdentifiableInterface $entity = null)
    {
        $this->entityManager->flush($entity);
    }

    // Implementation of WritableInterface::persist
    public function persist(IdentifiableInterface $entity)
    {
        $this->entityManager->persist($entity);
    }

    // Implementation of DeletableInterface::delete
    public function delete(FeatureDeletable $entity)
    {
        $this->entityManager->remove($entity);
    }

    // Implementation of DeletableInterface::deleteBy
    public function deleteBy($criteria)
    {
        $entities = $this->findBy($criteria);

        foreach ($entities as $entity) {
            $this->delete($entity);
        }
    }

    // Implementation of TransactionAwareInterface::beginTransaction
    public function beginTransaction()
    {
        $this->entityManager->beginTransaction();
    }

    // Implementation of TransactionAwareInterface::commit
    public function commitTransaction()
    {
        $this->entityManager->commit();
    }

    // Implementation of TransactionAwareInterface::rollBackTransaction
    public function rollBackTransaction()
    {
        $this->entityManager->rollback();
    }
}
