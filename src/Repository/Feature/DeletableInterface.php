<?php
/**
 * Polder Knowledge / Entity Service (http://polderknowledge.nl)
 *
 * @link http://developers.polderknowledge.nl/gitlab/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2015-2015 Polder Knowledge (http://www.polderknowledge.nl)
 * @license http://polderknowledge.nl/license/proprietary proprietary
 */

namespace PolderKnowledge\EntityService\Repository\Feature;

use Doctrine\Common\Collections\Criteria;
use PolderKnowledge\EntityService\Entity\Feature\DeletableInterface as FeatureDeletable;

/**
 * The DeletableInterface interface makes it possible to delete entities from a repository.
 */
interface DeletableInterface
{
    /**
     * Deletes the given object
     *
     * @param FeatureDeletable $entity The entity to delete.
     */
    public function delete(FeatureDeletable $entity);

    /**
     * Removes objects by a set of criteria.
     *
     * @param array|Criteria $criteria
     * @return int Returns the number of records that are deleted.
     */
    public function deleteBy($criteria);
}
