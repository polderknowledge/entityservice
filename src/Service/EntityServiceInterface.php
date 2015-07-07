<?php
/**
 * Polder Knowledge / Entity Service (http://polderknowledge.nl)
 *
 * @link http://developers.polderknowledge.nl/gitlab/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2015-2015 Polder Knowledge (http://www.polderknowledge.nl)
 * @license http://polderknowledge.nl/license/proprietary proprietary
 */

namespace PolderKnowledge\EntityService\Service;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use PolderKnowledge\EntityService\Repository\DeletableInterface;
use PolderKnowledge\EntityService\Repository\FlushableInterface;
use PolderKnowledge\EntityService\Repository\ReadableInterface;
use PolderKnowledge\EntityService\Repository\WritableInterface;
use PolderKnowledge\EntityService\Feature\IdentifiableInterface;
use PolderKnowledge\EntityService\ServiceProblem;
use PolderKnowledge\EntityService\ServiceResult;
use Zend\EventManager\EventManagerAwareInterface;

/**
 * Interface for EntityService. Provides multiple methods for regular crud operations.
 */
interface EntityServiceInterface extends EventManagerAwareInterface
{
    /**
     * Count the objects matching the criteria respecting the order, limit and offset.
     *
     * @param array $criteria The criteria values to match on.
     * @return ServiceResult|ServiceProblem
     */
    public function countBy(array $criteria);

    /**
     * Count the objects matching the criteria.
     *
     * @param Criteria $criteria The criteria object to match on.
     * @return ServiceResult|ServiceProblem
     */
    public function countByCriteria(Criteria $criteria);

    /**
     * Deletes the given object from the repository
     *
     * @param IdentifiableInterface $entity The entity to delete.
     */
    public function delete(IdentifiableInterface $entity);

    /**
     * Deletes all objects matching the criteria from the repository
     *
     * @param array $criteria The criteria values to match on.
     */
    public function deleteBy(array $criteria);

    /**
     * Deletes all objects from the repository that match the given criteria.
     *
     * @param Criteria $criteria The criteria object to match on.
     */
    public function deleteByCriteria(Criteria $criteria);

    /**
     * Find one object in the repository matching the $id
     *
     * @param mixed $id The id of the entity.
     * @return ServiceResult|ServiceProblem
     */
    public function find($id);

    /**
     * Finds all entities in the repository.
     *
     * @return array Returns the entities that exist.
     */
    public function findAll();

    /**
     * Find one or more objects in the repository matching the criteria respecting the order, limit and offset
     *
     * @param array $criteria The array with criteria to search on.
     * @param array|null $order The array with fields to sort on.
     * @param int|null $limit The limit
     * @param int|null $offset
     * @return ServiceResult|ServiceProblem
     */
    public function findBy(array $criteria, array $order = null, $limit = null, $offset = null);

    /**
     * Find one or more objects in the repository matching the criteria.
     *
     * @param Criteria $criteria The criteria object to match on.
     * @return ServiceResult|ServiceProblem
     */
    public function findByCriteria(Criteria $criteria);

    /**
     * Find one object in the repository matching the criteria
     *
     * @param array $criteria The criteria values to match on.
     * @param array $order The values to sort on.
     * @return ServiceResult|ServiceProblem
     */
    public function findOneBy(array $criteria, array $order);

    /**
     * Find one object in the repository matching the criteria
     *
     * @param Criteria $criteria The criteria object to match on.
     * @return ServiceResult|ServiceProblem
     */
    public function findOneByCriteria(Criteria $criteria);

    /**
     * Gets the repository that is used by the service.
     *
     * @return DeletableInterface|FlushableInterface|ReadableInterface|WritableInterface
     */
    public function getRepository();

    /**
     * Will return a repository object for a given $entityName
     *
     * @param string $entityName The name of the entity to get the repository for.
     * @return DeletableInterface|FlushableInterface|ReadableInterface|WritableInterface
     */
    public function getRepositoryForEntity($entityName);

    /**
     * Persist the given object
     *
     * @param IdentifiableInterface $entity The entity to persist.
     */
    public function persist(IdentifiableInterface $entity);

    /**
     * Persists multiple entities to repository.
     *
     * @param array|Iterator|Collection $entities The collection with entities.
     * @throws RuntimeException
     */
    public function multiPersist($entities);
}
