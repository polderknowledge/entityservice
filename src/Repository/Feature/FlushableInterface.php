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
 * The FlushableInterface interface makes it possible to flush identifiable entities to a repository.
 */
interface FlushableInterface
{
    /**
     * Will flush the given entity. If non given all queued entities will be flushed.
     *
     * @param IdentifiableInterface $entity The entity to flush.
     * @return void
     */
    public function flush(IdentifiableInterface $entity = null);
}
