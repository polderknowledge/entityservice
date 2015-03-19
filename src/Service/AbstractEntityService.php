<?php
/**
 * Polder Knowledge / Entity Service (http://polderknowledge.nl)
 *
 * @link http://developers.polderknowledge.nl/gitlab/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2015-2015 Polder Knowledge (http://www.polderknowledge.nl)
 * @license http://polderknowledge.nl/license/proprietary proprietary
 */

namespace PolderKnowledge\EntityService\Service;

use Doctrine\Common\Collections\Criteria;
use PolderKnowledge\EntityService\EntityEvent;
use PolderKnowledge\EntityService\Exception\InvalidArgumentException;
use PolderKnowledge\EntityService\Exception\RuntimeException;
use PolderKnowledge\EntityService\Exception\ServiceException;
use PolderKnowledge\EntityService\Feature\IdentifiableInterface;
use PolderKnowledge\EntityService\Repository\DeletableInterface;
use PolderKnowledge\EntityService\Repository\FlushableInterface;
use PolderKnowledge\EntityService\Repository\ReadableInterface;
use PolderKnowledge\EntityService\Repository\TransactionAwareInterface;
use PolderKnowledge\EntityService\Repository\WritableInterface;
use PolderKnowledge\EntityService\ServiceProblem;
use PolderKnowledge\EntityService\ServiceResult;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Stdlib\CallbackHandler;

/**
 * Base class for application specific EntityServices. This is a fully event driven class.
 * Each method trigger a method for extendability.
 */
abstract class AbstractEntityService implements
    EntityServiceInterface,
    ListenerAggregateInterface,
    TransactionAwareInterface
{
    /**
     * Repository used for all operations triggered by this service
     *
     * @var FlushableInterface|ReadableInterface|WritableInterface|DeletableInterface|TransactionAwareInterface
     */
    protected $repository;

    /**
     * Used to fetch repositories
     *
     * @var EntityRepositoryManager
     */
    protected $repositoryManager;

    /**
     * EventManager handeling all events triggered by this service
     *
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * Registered callbacks by this service since it implements a ListenerAggregateInterface
     *
     * @var CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * Initialized Event
     *
     * @var EntityEvent
     */
    protected $event;

    /**
     * Array containing ORDER clauses
     *
     * @var array
     */
    protected $order;

    /**
     * Value used as limit
     *
     * @var integer
     */
    protected $limit;

    /**
     * Value used as offset
     *
     * @var integer
     */
    protected $offset;

    /**
     * Initializes a new instance of this class.
     *
     * @param EntityRepositoryManager $manager The repository manager used to find the repository for the entity.
     * @param string $entityClassName The FQCN of the entity.
     */
    public function __construct(EntityRepositoryManager $manager, $entityClassName)
    {
        $entityClassName = trim($entityClassName, '\\');
        if (!class_exists($entityClassName)) {
            throw new RuntimeException('Invalid class name provided.');
        }

        $this->repositoryManager = $manager;
        $this->getEvent()->setEntityClassName($entityClassName);
    }

    /**
     * Will return the repository manager used to fetch repositories
     *
     * @return EntityRepositoryManager
     */
    public function getRepositoryManager()
    {
        return $this->repositoryManager;
    }

    /**
     * Will return a repository for the given $entityName if one is available
     *
     * @param string $entityName
     * @return DeletableInterface|FlushableInterface|ReadableInterface|WritableInterface
     */
    public function getRepositoryForEntity($entityName)
    {
        if (!isset($this->repository[$entityName]) || null === $this->repository[$entityName]) {
            $this->repository[$entityName] = $this->getRepositoryManager()->get($entityName);
        }

        return $this->repository[$entityName];
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
            $this->eventManager->detachAggregate($this);
        }

        $this->eventManager = $eventManager;

        $this->eventManager->addIdentifiers(
            array(
                'PolderKnowledge\EntityService\Service\EntityService',
                $this->getEntityServiceName(),
                trim($this->getEntityServiceName(), '\\'),
            )
        );

        $this->eventManager->attachAggregate($this);
    }

    /**
     * Attach one or more listeners
     *
     * Implementors may add an optional $priority argument; the EventManager
     * implementation will pass this to the aggregate.
     *
     * @param EventManagerInterface $events
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            'find',
            function (EntityEvent $event) {
                $target = $event->getTarget();
                $repository = $target->getRepositoryForEntity($event->getEntityClassName());
                $resultSet = $event->getResult();
                $resultSet->initialize(
                    array(call_user_func_array(
                        array($repository, 'find'),
                        $event->getParams()
                    ))
                );
            },
            0
        );

        $this->listeners[] = $events->attach(
            'findOneBy',
            function (EntityEvent $event) {
                $target = $event->getTarget();
                $repository = $target->getRepositoryForEntity($event->getEntityClassName());
                $resultSet = $event->getResult();
                $resultSet->initialize(
                    array(call_user_func_array(
                        array($repository, 'findOneBy'),
                        $event->getParams()
                    ))
                );
            },
            0
        );

        $this->listeners[] = $events->attach(
            'findBy',
            function (EntityEvent $event) {
                $target = $event->getTarget();
                $repository = $target->getRepositoryForEntity($event->getEntityClassName());
                $resultSet = $event->getResult();
                $resultSet->initialize(
                    call_user_func_array(
                        array($repository, 'findBy'),
                        $event->getParams()
                    )
                );
            },
            0
        );

        $this->listeners[] = $events->attach(
            'countBy',
            function (EntityEvent $event) {
                $target = $event->getTarget();
                $repository = $target->getRepositoryForEntity($event->getEntityClassName());
                $resultSet = $event->getResult();
                $resultSet->initialize(
                    array(call_user_func_array(
                        array($repository, 'countBy'),
                        $event->getParams()
                    ))
                );
            },
            0
        );

        $this->listeners[] = $events->attach(
            'delete',
            function (EntityEvent $event) {
                $target = $event->getTarget();
                $repository = $target->getRepositoryForEntity($event->getEntityClassName());
                call_user_func_array(array($repository, 'delete'), $event->getParams());
                if ($repository instanceof FlushableInterface) {
                    $repository->flush();
                }
            },
            0
        );

        $this->listeners[] = $events->attach(
            'deleteBy',
            function (EntityEvent $event) {
                $target = $event->getTarget();
                $repository = $target->getRepositoryForEntity($event->getEntityClassName());
                call_user_func_array(array($repository, 'deleteBy'), $event->getParams());
                if ($repository instanceof FlushableInterface) {
                    $repository->flush();
                }
            },
            0
        );

        $this->listeners[] = $events->attach(
            'persist',
            function (EntityEvent $event) {
                $target = $event->getTarget();
                $repository = $target->getRepositoryForEntity($event->getEntityClassName());
                call_user_func_array(array($repository, 'persist'), $event->getParams());
                if ($repository instanceof FlushableInterface) {
                    $repository->flush();
                }
            },
            0
        );

        $this->listeners[] = $events->attach(
            'multiPersist',
            function (EntityEvent $event) {
                $target = $event->getTarget();
                $repository = $target->getRepositoryForEntity($event->getEntityClassName());
                $entities = current($event->getParams());
                foreach ($entities as $entity) {
                    call_user_func_array(array($repository, 'persist'), array($entity));
                }
                if ($repository instanceof FlushableInterface) {
                    $repository->flush();
                }
            },
            0
        );

        $this->listeners[] = $events->attach(
            '*',
            function (EntityEvent $event) {
                $event->disableStoppingOfPropagation();
            },
            -1
        );
    }

    /**
     * Detach all previously attached listeners
     *
     * @param EventManagerInterface $events
     * @return void
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * Returns the name of the Entity handled by this service.
     *
     * @return String
     */
    protected function getEntityServiceName()
    {
        return $this->getEvent()->getEntityClassName();
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed $id
     */
    public function find($id)
    {
        if (!$this->repositoryIsReadable($this->getEntityServiceName())) {
            throw new RuntimeException('Repository is not readable');
        }

        return $this->trigger(
            __FUNCTION__,
            array(
                'id' => $id,
            )
        );
    }

    /**
     * {@inheritdoc}
     *
     * @param array|Criteria $criteria
     */
    public function findOneBy($criteria)
    {
        if (!$this->repositoryIsReadable($this->getEntityServiceName())) {
            throw new RuntimeException('Repository is not readable');
        }

        return $this->trigger(
            __FUNCTION__,
            array(
                'criteria' => $criteria,
            )
        );
    }

    /**
     * {@inheritdoc}
     *
     * @param array|Criteria $criteria
     * @param array $order
     * @param type  $limit
     * @param type  $offset
     * @throws RuntimeException
     */
    public function findBy($criteria, array $order = null, $limit = null, $offset = null)
    {
        if (!$this->repositoryIsReadable($this->getEntityServiceName())) {
            throw new RuntimeException('Repository is not readable');
        }

        return $this->trigger(
            __FUNCTION__,
            array(
                'criteria' => $criteria,
                'order' => $order,
                'limit' => $limit,
                'offset' => $offset
            )
        );
    }

    /**
     * {@inheritdoc}
     *
     * @param array|Criteria $criteria
     * @param array $order
     * @param type  $limit
     * @param type  $offset
     * @throws RuntimeException
     */
    public function countBy($criteria, array $order = null, $limit = null, $offset = null)
    {
        if (!$this->repositoryIsReadable($this->getEntityServiceName())) {
            throw new RuntimeException('Repository is not readable');
        }

        return $this->trigger(
            __FUNCTION__,
            array(
                'criteria' => $criteria,
                'order' => $order,
                'limit' => $limit,
                'offset' => $offset
            )
        );
    }

    /**
     * {@inheritdoc}
     *
     * @param IdentifiableInterface $entity
     * @throws RuntimeException
     */
    public function persist(IdentifiableInterface $entity)
    {
        if (!$this->repositoryIsWritable($this->getEntityServiceName())) {
            throw new RuntimeException('Repository is not writable');
        }

        return $this->trigger(
            __FUNCTION__,
            array(
                'entity' => $entity,
                'isNew' => !$entity->hasId(),
            )
        );
    }

    /**
     * {@inheritdoc}
     *
     * @param array $entities
     * @throws RuntimeException
     */
    public function multiPersist(array $entities)
    {
        if (!$this->repositoryIsWritable($this->getEntityServiceName())) {
            throw new RuntimeException('Repository is not writable');
        }

        return $this->trigger(
            __FUNCTION__,
            array(
                'entities' => $entities,
            )
        );
    }

    /**
     * {@inheritdoc}
     *
     * @param IdentifiableInterface $entity
     * @throws RuntimeException
     */
    public function delete(IdentifiableInterface $entity)
    {
        if (!$this->repositoryIsDeletable($this->getEntityServiceName())) {
            throw new RuntimeException('Repository is not deletable');
        }

        return $this->trigger(
            __FUNCTION__,
            array(
                'entity' => $entity,
            )
        );
    }

    /**
     * {@inheritdoc}
     * @param array|Criteria $criteria
     * @throws RuntimeException
     */
    public function deleteBy($criteria)
    {
        if (!$this->repositoryIsDeletable($this->getEntityServiceName())) {
            throw new RuntimeException('Repository is not deletable');
        }

        return $this->trigger(
            __FUNCTION__,
            array(
                'criteria' => $criteria,
            )
        );
    }

    /**
     * will prepare the event object and trigger the event using the internal EventManager
     *
     * @param  sting $name
     * @param  array $params
     * @return ServiceProblem|ServiceResult
     */
    protected function trigger($name, array $params)
    {
        $event = clone $this->getEvent();
        $event->setName($name);
        $event->setParams($params);

        $responseCollection = $this->getEventManager()->trigger($event);

        if ($responseCollection->stopped()) {
            if ($event->isError()) {
                return new ServiceProblem($event->getError(), $event->getErrorNr());
            }
        }

        return $event->getResult();
    }

    /**
     * Set order clause
     *
     * @param array $order
     * @return AbstractEntityService
     * @throws InvalidArgumentException
     */
    public function setOrder(array $order)
    {
        if (count(array_diff(array_values($order), array('ASC', 'DESC'))) > 0) {
            throw new InvalidArgumentException('Order value can only be DESC or ASC');
        }
        $this->order = $order;

        return $this;
    }

    /**
     * Sets limit clause
     *
     * @param integer $limit
     * @return AbstractEntityService
     * @throws InvalidArgumentException
     */
    public function setLimit($limit)
    {
        if (!is_scalar($limit) || !ctype_digit((string)$limit)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected integer, got %s',
                    is_object($limit) ? get_class($limit) : gettype($limit)
                )
            );
        }
        $this->limit = $limit;

        return $this;
    }

    /**
     * Sets order clause
     *
     * @param integer $offset
     * @return AbstractEntityService
     * @throws InvalidArgumentException
     */
    public function setOffset($offset)
    {
        if (!is_scalar($offset) || !ctype_digit((string)$offset)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected integer, got %s',
                    is_object($offset) ? get_class($offset) : gettype($offset)
                )
            );
        }
        $this->offset = $offset;

        return $this;
    }

    /**
     * Return array containing order clauses
     * @return array
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Return value used as limit
     *
     * @return integer
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Return value used as offset
     *
     * @return integer
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Clears dataset manipulation info like ordening and limitation
     *
     * Filter can be one or a combination of the following values
     * - order
     * - limit
     * - offset
     *
     * @param $filter
     * @return void
     */
    public function clear($filter = null)
    {
        if (is_string($filter)) {
            $filter = array($filter);
        }

        $propertiesWhichCanBeCleared = array('order', 'limit', 'offset');
        foreach ($propertiesWhichCanBeCleared as $property) {
            if (null === $filter || in_array($property, $filter)) {
                $method = sprintf('clear%s', ucfirst($property));
                $this->$method();
            }
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @throws ServiceException
     */
    public function beginTransaction()
    {
        if ($this->isTransactionEnabled() === false) {
            throw new ServiceException('Repository doesn\'t support Transactions');
        }

        $this->getRepositoryForEntity($this->getEntityServiceName())->beginTransaction();
    }

    /**
     * {@inheritDoc}
     *
     * @throws ServiceException
     */
    public function commitTransaction()
    {
        if ($this->isTransactionEnabled() === false) {
            throw new ServiceException('Repository doesn\'t support Transactions');
        }

        $this->getRepositoryForEntity($this->getEntityServiceName())->commitTransaction();
    }

    /**
     * {@inheritDoc}
     *
     * @throws ServiceException
     */
    public function rollBackTransaction()
    {
        if ($this->isTransactionEnabled() === false) {
            throw new ServiceException('Repository doesn\'t support Transactions');
        }

        $this->getRepositoryForEntity($this->getEntityServiceName())->rollBackTransaction();
    }

    /**
     * {@inheritDoc}
     */
    public function isTransactionEnabled()
    {
        return $this->getRepositoryForEntity($this->getEntityServiceName()) instanceof TransactionAwareInterface;
    }

    /**
     * Clear value used as offset
     */
    protected function clearOffset()
    {
        $this->offset = null;

        return $this;
    }

    /**
     * Clear value used as offset
     */
    protected function clearLimit()
    {
        $this->limit = null;

        return $this;
    }

    /**
     * Clear value used as offset
     */
    protected function clearOrder()
    {
        $this->order = null;

        return $this;
    }

    /**
     * Calls the flush method on the repository when applicable
     *
     * @return void
     */
    protected function flushRepository()
    {
        $repository = $this->getRepository();
        if ($repository instanceof FlushableInterface) {
            $repository->flush();
        }
    }

    /**
     * Returns true when the repository for $entityName is writable
     *
     * @param $entityName
     * @return bool
     */
    protected function repositoryIsWritable($entityName)
    {
        return $this->getRepositoryForEntity($entityName) instanceof WritableInterface;
    }

    /**
     * Returns true when the repository for $entityName is readable
     *
     * @param $entityName
     * @return bool
     */
    protected function repositoryIsReadable($entityName)
    {
        return $this->getRepositoryForEntity($entityName) instanceof ReadableInterface;
    }

    /**
     * Returns true when the repository for $entityName has delete behavior
     *
     * @param $entityName
     * @return bool
     */
    protected function repositoryIsDeletable($entityName)
    {
        return $this->getRepositoryForEntity($entityName) instanceof DeletableInterface;
    }
}
