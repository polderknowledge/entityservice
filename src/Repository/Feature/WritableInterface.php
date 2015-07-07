<?php
/**
 * Polder Knowledge / Entity Service (http://polderknowledge.nl)
 *
 * @link http://developers.polderknowledge.nl/gitlab/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2015-2015 Polder Knowledge (http://www.polderknowledge.nl)
 * @license http://polderknowledge.nl/license/proprietary proprietary
 */

namespace PolderKnowledge\EntityService\Repository\Feature;

use PolderKnowledge\EntityService\Entity\Feature\IdentifiableInterface;

/**
 * The WritableInterface interface makes it possible persist entities to a repository.
 */
interface WritableInterface
{
    /**
     * Persist the given entity.
     *
     * @param IdentifiableInterface $entity The entity to persist.
     */
    public function persist(IdentifiableInterface $entity);
}
