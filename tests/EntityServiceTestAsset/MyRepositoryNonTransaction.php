<?php
/**
 * Polder Knowledge / entityservice (https://polderknowledge.com)
 *
 * @link https://github.com/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2016 Polder Knowledge (https://polderknowledge.com)
 * @license https://github.com/polderknowledge/entityservice/blob/master/LICENSE.md MIT
 */

namespace PolderKnowledge\EntityServiceTestAsset;

use Doctrine\Common\Collections\Criteria;
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
    public function delete($entity)
    {
    }

    public function deleteBy(Criteria $criteria)
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

    public function flush($entity = null)
    {
    }

    public function persist($entity)
    {
    }
}
