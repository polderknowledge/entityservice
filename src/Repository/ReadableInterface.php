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
use UnexpectedValueException;

/**
 * The ReadableInterface interface makes it possible to read entities from a repository.
 */
interface ReadableInterface
{
    /**
     * Counts entities by a set of criteria.
     *
     * Optionally sorting and limiting details can be passed. An implementation may throw an UnexpectedValueException
     * if certain values of the sorting or limiting details are not supported.
     *
     * @param array|Criteria $criteria The criteria to find entities by.
     * @param array|null $orderBy An array with fields to order by. Set to null for default ordering.
     * @param int|null $limit The amount of entities to retrieve. Set to null for default amount.
     * @param int|null $offset The offset to start retrieving entities from. Set to null for the default offset.
     * @return int Returns the amount of entities that are found.
     * @throws UnexpectedValueException
     */
    public function countBy($criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * Tries to find an entity in the repository by the given identifier.
     *
     * @param mixed $id The id of the entity which can be any type of object.
     * @return object|null Returns the entity that matches the identifier or null when no instance is found.
     */
    public function find($id);

    /**
     * Tries to find all entities in the repository.
     *
     * @return object[] Returns an array with entities that are found.
     */
    public function findAll();

    /**
     * Tries to find entities by a set of criteria.
     *
     * Optionally sorting and limiting details can be passed. An implementation may throw an UnexpectedValueException
     * if certain values of the sorting or limiting details are not supported.
     *
     * @param array|Criteria $criteria The criteria to find entities by.
     * @param array|null $orderBy An array with fields to order by. Set to null for default ordering.
     * @param int|null $limit The amount of entities to retrieve. Set to null for default amount.
     * @param int|null $offset The offset to start retrieving entities from. Set to null for the default offset.
     * @return object[] Returns an array with found entities. Returns an empty array when no entities are found.
     * @throws UnexpectedValueException Thrown when provided parameters are not supported.
     */
    public function findBy($criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * Tries to find a single entity by a set of criteria.
     *
     * @param array|Criteria $criteria The criteria. The criteria to find the entity by.
     * @return object Returns the entity that is found or null when no entity is found.
     */
    public function findOneBy($criteria);
}
