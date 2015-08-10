<?php
/**
 * Polder Knowledge / Entity Service (http://polderknowledge.nl)
 *
 * @link http://developers.polderknowledge.nl/gitlab/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2015-2015 Polder Knowledge (http://www.polderknowledge.nl)
 * @license http://polderknowledge.nl/license/proprietary proprietary
 */

namespace PolderKnowledge\EntityServiceTestAsset;

use PolderKnowledge\EntityService\Entity\Feature\DeletableInterface as FeatureDeletable;
use PolderKnowledge\EntityService\Entity\Feature\IdentifiableInterface;
use PolderKnowledge\EntityService\Repository\EntityRepositoryInterface;
use PolderKnowledge\EntityService\Repository\Feature\DeletableInterface;
use PolderKnowledge\EntityService\Repository\Feature\FlushableInterface;
use PolderKnowledge\EntityService\Repository\Feature\ReadableInterface;
use PolderKnowledge\EntityService\Repository\Feature\WritableInterface;

class MyRepositoryNonTransaction implements
    EntityRepositoryInterface,
    DeletableInterface,
    FlushableInterface,
    ReadableInterface,
    WritableInterface
{
    public function delete(FeatureDeletable $entity)
    {
    }

    public function deleteBy($criteria)
    {
    }

    public function find($id)
    {
    }

    public function findAll()
    {
    }

    public function findBy($criteria, array $orderBy = null, $limit = null, $offset = null)
    {
    }

    public function countBy($criteria, array $orderBy = null, $limit = null, $offset = null)
    {
    }

    public function findOneBy($criteria)
    {
    }

    public function flush(IdentifiableInterface $entity = null)
    {
    }

    public function persist(IdentifiableInterface $entity)
    {
    }
}
