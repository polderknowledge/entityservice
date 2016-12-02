<?php
/**
 * Polder Knowledge / entityservice (https://polderknowledge.com)
 *
 * @link https://github.com/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2016 Polder Knowledge (https://polderknowledge.com)
 * @license https://github.com/polderknowledge/entityservice/blob/master/LICENSE.md MIT
 */

namespace PolderKnowledge\EntityService;

use Doctrine\Common\Collections\Criteria;
use PolderKnowledge\EntityService\Event\EntityEvent;
use PolderKnowledge\EntityService\Exception\RuntimeException;
use PolderKnowledge\EntityService\Exception\ServiceException;
use PolderKnowledge\EntityService\Feature\TransactionAwareInterface;
use PolderKnowledge\EntityService\Repository\EntityRepositoryInterface;
use PolderKnowledge\EntityService\Repository\Feature\DeletableInterface;
use PolderKnowledge\EntityService\Repository\Feature\ReadableInterface;
use PolderKnowledge\EntityService\Repository\Feature\WritableInterface;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;

/**
 * Base class for application specific EntityServices. This is a fully event driven class.
 * Each method trigger a method for extendability.
 */
abstract class AbstractEntityService extends AbstractListenerAggregate implements
    EntityServiceInterface,
    TransactionAwareInterface
{
    /**
     * The repository that is used for this entity service.
     *
     * @var EntityRepositoryInterface
     */
    private $repository;

    /**
     * EventManager handeling all events triggered by this service
     *
     * @var EventManagerInterface
     */
    private $eventManager;

    /**
     * Initialized Event
     *
     * @var EntityEvent
     */
    private $event;

    /**
     * Initializes a new instance of this class.
     *
     * @param EntityRepositoryInterface $repository The repository that is used to communicate with.
     * @param string $entityClassName The FQCN of the entity.
     */
    public function __construct(EntityRepositoryInterface $repository, $entityClassName)
    {
        $this->repository = $repository;
        $this->listeners = [];

        $this->getEvent()->setEntityClassName($entityClassName);
    }

    /**
     * Gets the repository that is used by the service.
     *
     * @return EntityRepositoryInterface
     */
    protected function getRepository()
    {
        return $this->repository;
    }

    /**
     * Get the pre initialized event object
     *
     * @return EntityEvent
     */
    protected function getEvent()
    {
        if (null === $this->event) {
            $this->event = $event = new EntityEvent;

            $event->setTarget($this);
        }

        return $this->event;
    }

    /**
     * Will create an EventManager when no EventManager was provided.
     * The returned EventManager is used to handle events triggered by this service instance.
     *
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        if (null === $this->eventManager) {
            $this->setEventManager(new EventManager);
        }

        return $this->eventManager;
    }

    /**
     * Set the EventManager used by this service instance to handle its events.
     * It will take care of disabling the old EventManager and will subscribe the internal
     * listeners to the new EventManager
     *
     * @param EventManagerInterface $eventManager
     */
    public function setEventManager(EventManagerInterface $eventManager)
    {
        if ($this->eventManager === $eventManager || $eventManager === null) {
            return;
        }

        if ($this->eventManager !== null) {
            $this->detach($this->eventManager);
        }

        $this->eventManager = $eventManager;
        $this->eventManager->addIdentifiers([
            'EntityService',
            'PolderKnowledge\EntityService\Service\EntityService',
            $this->getEntityServiceName(),
            trim($this->getEntityServiceName(), '\\'),
        ]);

        $this->attach($this->eventManager);
    }

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $callback = function (EntityEvent $event) {
            $repository = $event->getTarget()->getRepository();

            $event->setResult(call_user_func_array(
                [$repository, $event->getName()],
                $event->getParams()
            ));
        };

        $this->listeners[] = $events->attach('countBy', $callback, 0);
        $this->listeners[] = $events->attach('delete', $callback, 0);
        $this->listeners[] = $events->attach('deleteBy', $callback, 0);
        $this->listeners[] = $events->attach('find', $callback, 0);
        $this->listeners[] = $events->attach('findAll', $callback, 0);
        $this->listeners[] = $events->attach('findBy', $callback, 0);
        $this->listeners[] = $events->attach('findOneBy', $callback, 0);
        $this->listeners[] = $events->attach('persist', $callback, 0);
        $this->listeners[] = $events->attach('flush', $callback, 0);

        $this->listeners[] = $events->attach('*', function (EntityEvent $event) {
            $event->disableStoppingOfPropagation();
        }, -1);
    }

    /**
     * Returns the FQCN of the entity handled by this service.
     *
     * @return string
     */
    protected function getEntityServiceName()
    {
        return $this->getEvent()->getEntityClassName();
    }

    /**
     * Deletes the given object from the repository
     *
     * @param object $entity The entity to delete.
     * @return mixed
     */
    public function delete($entity)
    {
        if (!$this->isRepositoryDeletable()) {
            throw $this->createNotDeletableException();
        }

        return $this->trigger(__FUNCTION__, [
            'entity' => $entity,
        ]);
    }

    /**
     * Deletes all objects matching the criteria from the repository
     *
     * @param array|Criteria $criteria The criteria values to match on.
     * @return mixed
     */
    public function deleteBy($criteria)
    {
        if (!$this->isRepositoryDeletable()) {
            throw $this->createNotDeletableException();
        }

        return $this->trigger(__FUNCTION__, [
            'criteria' => $criteria,
        ]);
    }

    /**
     * Count the objects matching the criteria respecting the order, limit and offset.
     *
     * @param array|Criteria $criteria The criteria values to match on.
     * @return int
     */
    public function countBy($criteria)
    {
        if (!$this->isRepositoryReadable()) {
            throw $this->createNotReadableException();
        }

        return $this->trigger(__FUNCTION__, [
            'criteria' => $criteria,
        ]);
    }

    /**
     * Find one object in the repository matching the $id
     *
     * @param mixed $id The id of the entity.
     * @return object|null
     */
    public function find($id)
    {
        if (!$this->isRepositoryReadable()) {
            throw $this->createNotReadableException();
        }

        return $this->trigger(__FUNCTION__, [
            'id' => $id,
        ]);
    }

    /**
     * Finds all entities in the repository.
     *
     * @return array Returns the entities that exist.
     */
    public function findAll()
    {
        if (!$this->isRepositoryReadable()) {
            throw $this->createNotReadableException();
        }

        return $this->trigger(__FUNCTION__, []);
    }

    /**
     * Find one or more objects in the repository matching the criteria respecting the order, limit and offset
     *
     * @param array|Criteria $criteria The array with criteria to search on.
     * @return array
     */
    public function findBy($criteria)
    {
        if (!$this->isRepositoryReadable()) {
            throw $this->createNotReadableException();
        }

        return $this->trigger(__FUNCTION__, [
            'criteria' => $criteria,
        ]);
    }

    /**
     * Find one object in the repository matching the criteria
     *
     * @param array|Criteria $criteria The criteria values to match on.
     * @return object|null
     */
    public function findOneBy($criteria)
    {
        if (!$this->isRepositoryReadable()) {
            throw $this->createNotReadableException();
        }

        return $this->trigger(__FUNCTION__, [
            'criteria' => $criteria,
        ]);
    }

    /**
     * Persist the given entity
     *
     * @param object $entity
     * @return mixed
     * @throws RuntimeException
     */
    public function persist($entity)
    {
        if (!$this->isRepositoryWritable()) {
            throw $this->createNotWritableException();
        }

        return $this->trigger(__FUNCTION__, [
            'entity' => $entity,
        ]);
    }

    /**
     * Flushes the provided entity or all persisted entities when no entity is provided.
     *
     * @param object $entity
     * @return mixed
     * @throws RuntimeException
     */
    public function flush($entity = null)
    {
        if (!$this->isRepositoryWritable()) {
            throw $this->createNotWritableException();
        }

        return $this->trigger(__FUNCTION__, [
            'entity' => $entity,
        ]);
    }

    /**
     * will prepare the event object and trigger the event using the internal EventManager
     *
     * @param  string $name
     * @param  array $params
     * @return mixed
     */
    protected function trigger($name, array $params)
    {
        $event = clone $this->getEvent();
        $event->setName($name);
        $event->setParams($params);

        $responseCollection = $this->getEventManager()->triggerEvent($event);

        if ($responseCollection->stopped() && $event->isError()) {
            throw new RuntimeException($event->getError(), $event->getErrorNr());
        }

        return $event->getResult();
    }

    /**
     * Starts a new transaction.
     *
     * @throws ServiceException
     */
    public function beginTransaction()
    {
        if ($this->isTransactionEnabled() === false) {
            throw new ServiceException(sprintf(
                'The repository for %s doesn\'t support Transactions',
                $this->getEntityServiceName()
            ));
        }

        $this->getRepository()->beginTransaction();
    }

    /**
     * Commits a started transaction.
     *
     * @throws ServiceException
     */
    public function commitTransaction()
    {
        if ($this->isTransactionEnabled() === false) {
            throw new ServiceException(sprintf(
                'The repository for %s doesn\'t support Transactions',
                $this->getEntityServiceName()
            ));
        }

        $this->getRepository()->commitTransaction();
    }

    /**
     * Rolls back a started transaction.
     *
     * @throws ServiceException
     */
    public function rollbackTransaction()
    {
        if ($this->isTransactionEnabled() === false) {
            throw new ServiceException(sprintf(
                'The repository for %s doesn\'t support Transactions',
                $this->getEntityServiceName()
            ));
        }

        $this->getRepository()->rollbackTransaction();
    }

    /**
     * Returns true when possible to start an transaction
     */
    public function isTransactionEnabled()
    {
        return $this->getRepository() instanceof TransactionAwareInterface;
    }

    /**
     * Returns true when the repository for $entityName is writable
     *
     * @return bool
     */
    protected function isRepositoryWritable()
    {
        return $this->getRepository() instanceof WritableInterface;
    }

    /**
     * Returns true when the repository for $entityName is readable
     *
     * @return bool
     */
    protected function isRepositoryReadable()
    {
        return $this->getRepository() instanceof ReadableInterface;
    }

    /**
     * Returns true when the repository for $entityName has delete behavior
     *
     * @return bool
     */
    protected function isRepositoryDeletable()
    {
        return $this->getRepository() instanceof DeletableInterface;
    }

    /**
     * Throws an exception for cases where it's not possible to delete from the repository.
     *
     * @throws RuntimeException
     */
    private function createNotDeletableException()
    {
        throw new RuntimeException(sprintf(
            'The entities of type "%s" cannot be deleted from its repository.',
            $this->getEntityServiceName()
        ));
    }

    /**
     * Throws an exception for cases where it's not possible to read from the repository.
     *
     * @throws RuntimeException
     */
    private function createNotReadableException()
    {
        throw new RuntimeException(sprintf(
            'It is not possible to read entities of type "%s" from its repository.',
            $this->getEntityServiceName()
        ));
    }

    /**
     * Throws an exception for cases where it's not possible to write to the repository.
     *
     * @throws RuntimeException
     */
    private function createNotWritableException()
    {
        throw new RuntimeException(sprintf(
            'The entities of type "%s" cannot be written to its repository.',
            $this->getEntityServiceName()
        ));
    }
}
