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
use Zend\EventManager\EventManagerAwareInterface;

/**
 * Interface for EntityService. Provides multiple methods for regular crud opperations.
 */
interface EntityServiceInterface extends EventManagerAwareInterface
{
    /**
     * @param string $entityName The name of the entity to get the repository for.
     * @return DeletableInterface|FlushableInterface|ReadableInterface|WritableInterface
     */
    public function getRepositoryForEntity($entityName);

    /**
     * @param mixed $id The id of the
     * @return \PolderKnowledge\EntityService\ServiceResult|\PolderKnowledge\EntityService\ServiceProblem
     */
    public function find($id);

    /**
     * @param array|Criteria $criteria
     * @param array $order
     * @param int $limit
     * @param int $offset
     * @return \PolderKnowledge\EntityService\ServiceResult|\PolderKnowledge\EntityService\ServiceProblem
     */
    public function findBy($criteria, array $order = null, $limit = null, $offset = null);

    /**
     *
     * @param array|Criteria $criteria
     * @param array $order
     * @param int $limit
     * @param int $offset
     * @return \PolderKnowledge\EntityService\ServiceResult|\PolderKnowledge\EntityService\ServiceProblem
     */
    public function countBy($criteria, array $order = null, $limit = null, $offset = null);

    /**
     *
     * @param \PolderKnowledge\EntityService\Feature\IdentifiableInterface $entity
     */
    public function persist(IdentifiableInterface $entity);

    /**
     *
     * @param \PolderKnowledge\EntityService\Feature\IdentifiableInterface $entity
     */
    public function delete(IdentifiableInterface $entity);

    /**
     *
     * @param array|Criteria $criteria
     */
    public function deleteBy($criteria);

    /**
     *
     * @param array $order
     * @return void
     */
    public function setOrder(array $order);

    /**
     *
     * @param int $limit
     * @return void
     */
    public function setLimit($limit);

    /**
     *
     * @param int $offset
     * @return void
     */
    public function setOffset($offset);

    /**
     * @return array
     */
    public function getOrder();

    /**
     * @return int
     */
    public function getLimit();

    /**
     * @return int
     */
    public function getOffset();

    /**
     * Clears dataset manipulation info like ordening and limitation
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
