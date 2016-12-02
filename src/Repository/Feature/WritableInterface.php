<?php
/**
 * Polder Knowledge / entityservice (https://polderknowledge.com)
 *
 * @link https://github.com/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2016 Polder Knowledge (https://polderknowledge.com)
 * @license https://github.com/polderknowledge/entityservice/blob/master/LICENSE.md MIT
 */

namespace PolderKnowledge\EntityService\Repository\Feature;

/**
 * The WritableInterface interface makes it possible persist entities to a repository.
 */
interface WritableInterface
{
    /**
     * Persist the given entity.
     *
     * @param object $entity The entity to persist.
     */
    public function persist($entity);
}
