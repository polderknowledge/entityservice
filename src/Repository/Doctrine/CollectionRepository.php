<?php

namespace PolderKnowledge\EntityService\Repository\Doctrine;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use PolderKnowledge\EntityService\Repository\EntityRepositoryInterface;
use PolderKnowledge\EntityService\Repository\Feature\DeletableInterface;
use PolderKnowledge\EntityService\Repository\Feature\ReadableInterface;
use PolderKnowledge\EntityService\Repository\Feature\TransactionAwareInterface;
use PolderKnowledge\EntityService\Repository\Feature\WritableInterface;
use PolderKnowledge\EntityService\Repository\Util;
use UnexpectedValueException;

class CollectionRepository implements
    EntityRepositoryInterface,
    DeletableInterface,
    ReadableInterface,
    WritableInterface
{
    private $allItems;

    private $persisted;

    public function __construct(array $initialItems = [])
    {
        $this->allItems = new ArrayCollection($initialItems);
        $this->persisted = new ArrayCollection;
    }

    public function delete($entity)
    {
        $this->allItems->removeElement($entity);
        $this->persisted->removeElement($entity);
    }

    public function deleteBy($criteria)
    {
        $criteria = Util::normalizeCriteria($criteria);

        $itemsToDelete = $this->allItems->matching($criteria);

        foreach ($itemsToDelete as $item) {
            $this->allItems->removeElement($item);
            $this->persisted->removeElement($item);
        }
    }

    public function countBy($criteria)
    {
        $criteria = Util::normalizeCriteria($criteria);

        return $this->allItems->matching($criteria)->count();
    }

    public function find($id)
    {
        return $this->allItems->filter(
            function ($entity) use ($id) {
                return $entity->getId() === $id;
            }
        )->first();
    }

    public function findAll()
    {
        return $this->allItems->toArray();
    }

    public function findBy($criteria)
    {
        $criteria = Util::normalizeCriteria($criteria);

        return $this->allItems->matching($criteria)->toArray();
    }

    public function findOneBy($criteria)
    {
        $criteria = Util::normalizeCriteria($criteria);

        return $this->allItems->matching($criteria)->first();
    }

    public function persist($entity)
    {
        if (!$this->allItems->contains($entity)) {
            $this->allItems->add($entity);
        }
        if (!$this->persisted->contains($entity)) {
            $this->persisted->add($entity);
        }
    }

    /**
     * Method outside of the standard interface, for testing purposes
     */
    public function getPersisted(): ArrayCollection
    {
        return clone $this->persisted;
    }
}
