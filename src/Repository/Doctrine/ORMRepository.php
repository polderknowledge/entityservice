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
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
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
     * @var EntityRepository
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
     * @inheritdoc
     */
    public function countBy($criteria)
    {
        $queryBuilder = $this->getRepository()->createQueryBuilder('e');
        $queryBuilder->select('count(e)');

        if (!empty($criteria)) {
            foreach ($criteria as $field => $value) {
                $queryBuilder->andWhere($queryBuilder->expr()->eq('e.' . $field, ':'.$field));
                $queryBuilder->setParameter(':'.$field, $value);
            }
        }

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }

    /**
     * @inheritdoc
     */
    public function delete(EntityFeatureDeletable $entity)
    {
        $this->entityManager->remove($entity);
    }

    /**
     * @inheritdoc
     */
    public function deleteBy($criteria)
    {
        $entities = $this->findBy($criteria);

        foreach ($entities as $entity) {
            $this->entityManager->remove($entity);
        }
    }

    /**
     * @inheritdoc
     */
    public function find($id)
    {
        return $this->entityManager->find($this->entityName, $id);
    }

    /**
     * @inheritdoc
     */
    public function findAll()
    {
        return $this->getRepository()->findAll();
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
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
     * @inheritdoc
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
     * @inheritdoc
     */
    public function getRepository()
    {
        if ($this->repository === null) {
            $this->repository = $this->entityManager->getRepository($this->entityName);
        }

        return $this->repository;
    }

    /**
     * @inheritdoc
     */
    public function flush(EntityFeatureIdentifiable $entity = null)
    {
        $this->entityManager->flush($entity);
    }

    /**
     * @inheritdoc
     */
    public function persist(EntityFeatureIdentifiable $entity)
    {
        $this->entityManager->persist($entity);
    }

    /**
     * @inheritdoc
     */
    public function beginTransaction()
    {
        $this->entityManager->beginTransaction();
    }

    /**
     * @inheritdoc
     */
    public function commitTransaction()
    {
        $this->entityManager->commit();
    }

    /**
     * @inheritdoc
     */
    public function rollBackTransaction()
    {
        $this->entityManager->rollback();
    }
}
