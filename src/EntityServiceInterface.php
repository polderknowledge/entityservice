<?php
/**
 * Polder Knowledge / Entity Service (http://polderknowledge.nl)
 *
 * @link http://developers.polderknowledge.nl/gitlab/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2015-2015 Polder Knowledge (http://www.polderknowledge.nl)
 * @license http://polderknowledge.nl/license/proprietary proprietary
 */

namespace PolderKnowledge\EntityService;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use PolderKnowledge\EntityService\Entity\Feature\IdentifiableInterface;
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
     * @param IdentifiableInterface $entity The entity to delete.
     */
    public function delete(IdentifiableInterface $entity);

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
