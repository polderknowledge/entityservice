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
 * The FlushableInterface interface makes it possible to flush entities to a repository.
 */
interface FlushableInterface
{
    /**
     * Will flush the given entity. If non given all queued entities will be flushed.
     *
     * @param object $entity The entity to flush.
     * @return void
     */
    public function flush($entity = null);
}
