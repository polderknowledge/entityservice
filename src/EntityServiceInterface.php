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
use Iterator;
use PolderKnowledge\EntityService\Exception\RuntimeException;
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
     * @param array|Criteria $criteria The criteria values to match on.
     */
    public function deleteBy($criteria);

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
     * Persist the given object
     *
     * @param object $entity The entity to persist.
     */
    public function persist($entity);

    /**
     * Flushes the provided entity or all persisted entities when no entity is provided.
     *
     * @param object $entity
     * @return mixed
     * @throws RuntimeException
     */
    public function flush($entity = null);
}
