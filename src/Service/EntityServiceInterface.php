<?php
/**
 * Polder Knowledge / Entity Service (http://polderknowledge.nl)
 *
 * @link http://developers.polderknowledge.nl/gitlab/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2015-2015 Polder Knowledge (http://www.polderknowledge.nl)
 * @license http://polderknowledge.nl/license/proprietary proprietary
 */

namespace PolderKnowledge\EntityService\Service;

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
     * Will return a repository object for a given $entityName
     *
     * @param string $entityName The name of the entity to get the repository for.
     * @return DeletableInterface|FlushableInterface|ReadableInterface|WritableInterface
     */
    public function getRepositoryForEntity($entityName);

    /**
     * Find one object in the repository matching the $id
     *
     * @param mixed $id The id of the
     * @return ServiceResult|ServiceProblem
     */
    public function find($id);

    /**
     * Find one or more objects in the repository matching the criteria respecting the order, limit and offset
     *
     * @param array|Criteria $criteria
     * @param array $order
     * @param int $limit
     * @param int $offset
     * @return ServiceResult|ServiceProblem
     */
    public function findBy($criteria, array $order = null, $limit = null, $offset = null);

    /**
     * Count the objects matching the criteria respecting the order, limit and offset.
     *
     * @param array|Criteria $criteria
     * @param array $order
     * @param int $limit
     * @param int $offset
     * @return ServiceResult|ServiceProblem
     */
    public function countBy($criteria, array $order = null, $limit = null, $offset = null);

    /**
     * Persist the given object
     *
     * @param IdentifiableInterface $entity
     */
    public function persist(IdentifiableInterface $entity);

    /**
     * Deletes the given object from the repository
     *
     * @param IdentifiableInterface $entity
     */
    public function delete(IdentifiableInterface $entity);

    /**
     * Deletes all objects matching the criteria from the repository
     *
     * @param array|Criteria $criteria
     */
    public function deleteBy($criteria);

    /**
     * Sets the default order used in the find methods
     *
     * @param array $order
     * @return void
     */
    public function setOrder(array $order);

    /**
     * Sets the default limit clause used in the find methods
     *
     * @param int $limit
     * @return void
     */
    public function setLimit($limit);

    /**
     * Sets the default offset used in the find methods
     *
     * @param int $offset
     * @return void
     */
    public function setOffset($offset);

    /**
     * Returns the default order criteria used in the find methods
     *
     * @return array
     */
    public function getOrder();

    /**
     * Returns the default limit used in the find methods
     *
     * @return int
     */
    public function getLimit();

    /**
     * Returns the default offset used in the find methods
     *
     * @return int
     */
    public function getOffset();

    /**
     * Clears data set manipulation info like ordering and limitation
     *
     * Filter can be one or a combination of the following values
     * - order
     * - limit
     * - offset
     *
     * @param $filter
     * @return void
     */
    public function clear($filter);
}
