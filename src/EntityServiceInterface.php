<?php
/**
 * Polder Knowledge / entityservice (https://polderknowledge.com)
 *
 * @link https://github.com/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2016 Polder Knowledge (https://polderknowledge.com)
 * @license https://github.com/polderknowledge/entityservice/blob/master/LICENSE.md MIT
 */

namespace PolderKnowledge\EntityService;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use PolderKnowledge\EntityService\Exception\RuntimeException;
use Traversable;
use Zend\EventManager\EventManagerAwareInterface;

/**
 * Interface for EntityService. Provides multiple methods for regular crud operations.
 */
interface EntityServiceInterface extends EventManagerAwareInterface
{
    /**
     * Count the objects matching the criteria respecting the order, limit and offset.
     *
     * @param array|Criteria $criteria The criteria values to match on.
     * @return int
     */
    public function countBy($criteria);

    /**
     * Deletes the given object from the repository
     *
     * @param object $entity The entity to delete.
     */
    public function delete($entity);

    /**
     * Deletes all objects matching the criteria from the repository
     *
     * @param object $entityClass the class of the entity on which to run the delete query
     * @param Criteria $criteria The criteria values to match on.
     */
    public function deleteBy($entityClass, Criteria $criteria);

    /**
     * Find one object in the repository matching the $id
     *
     * @param mixed $id The id of the entity.
     * @return object|null
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
     * @param array|Criteria $criteria The array with criteria to search on.
     * @return array
     */
    public function findBy($criteria);

    /**
     * Find one object in the repository matching the criteria
     *
     * @param array|Criteria $criteria The criteria values to match on.
     * @return object|null
     */
    public function findOneBy($criteria);

    /**
     * Persist the given object and flushes it to the storage device.
     *
     * @param object $entity The entity to persist.
     * @throws RuntimeException
     */
    public function persist($entity);

    /**
     * Persist the given object and flushes it to the storage device.
     *
     * @param array|Collection|Traversable $entities The entities to persist.
     * @throws RuntimeException
     */
    public function multiPersist($entities);
}
