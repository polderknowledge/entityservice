<?php
/**
 * Polder Knowledge / entityservice (https://polderknowledge.com)
 *
 * @link https://github.com/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2016 Polder Knowledge (https://polderknowledge.com)
 * @license https://github.com/polderknowledge/entityservice/blob/master/LICENSE.md MIT
 */

namespace PolderKnowledge\EntityService\Repository\Feature;

use Doctrine\Common\Collections\Criteria;

/**
 * The DeletableInterface interface makes it possible to delete entities from a repository.
 */
interface DeletableInterface
{
    /**
     * Deletes the given object
     *
     * @param object $entity The entity to delete.
     */
    public function delete($entity);

    /**
     * Removes objects by a set of criteria.
     *
     * @param object $entityClass the class of the entity on which to run the delete query
     * @param Criteria $criteria
     * @return int Returns the number of records that are deleted.
     */
    public function deleteBy($entityClass, Criteria $criteria);
}
