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
use PolderKnowledge\EntityService\Exception\RuntimeException;
use PolderKnowledge\EntityService\Exception\ServiceException;
use PolderKnowledge\EntityService\Feature\IdentifiableInterface;
use PolderKnowledge\EntityService\Repository\DeletableInterface;
use PolderKnowledge\EntityService\Repository\EntityRepositoryInterface;
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
     * Used to fetch repositories
     *
     * @var EntityRepositoryManager
     */
    private $repositoryManager;

    /**
     * A list with repositories that are used in the service.
     *
     * @var EntityRepositoryInterface[]
     */
    private $repositories;

    /**
     * EventManager handeling all events triggered by this service
     *
     * @var EventManagerInterface
     */
    private $eventManager;

    /**
     * Registered callbacks by this service since it implements a ListenerAggregateInterface
     *
     * @var CallbackHandler[]
     */
    private $listeners;

    /**
     * Initialized Event
     *
     * @var EntityEvent
     */
    private $event;

    /**
     * Initializes a new instance of this class.
     *
     * @param EntityRepositoryManager $manager The repository manager used to find the repository for the entity.
     * @param string $entityClassName The FQCN of the entity.
     */
    public function __construct(EntityRepositoryManager $manager, $entityClassName)
    {
        $this->repositoryManager = $manager;
        $this->repositories = array();
        $this->listeners = array();

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
     * Gets the repository that is used by the service.
     *
     * @return DeletableInterface|FlushableInterface|ReadableInterface|WritableInterface
     */
    public function getRepository()
    {
        return $this->getRepositoryForEntity($this->getEvent()->getEntityClassName());
    }

    /**
     * Will return a repository object for a given $entityName
     *
     * @param string $entityName The name of the entity to get the repository for.
     * @return DeletableInterface|FlushableInterface|ReadableInterface|WritableInterface
     */
    public function getRepositoryForEntity($entityName)
    {
        if (!isset($this->repositories[$entityName]) || null === $this->repositories[$entityName]) {
            $this->repositories[$entityName] = $this->getRepositoryManager()->get($entityName);
        }

        return $this->repositories[$entityName];
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

        $this->eventManager->addIdentifiers(array(
            'PolderKnowledge\EntityService\Service\EntityService',
            $this->getEntityServiceName(),
            trim($this->getEntityServiceName(), '\\'),
        ));

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
            'countByCriteria',
            function (EntityEvent $event) {
                $target = $event->getTarget();
                $repository = $target->getRepositoryForEntity($event->getEntityClassName());
                $resultSet = $event->getResult();
                $resultSet->initialize(
                    array(call_user_func_array(
                        array($repository, 'countByCriteria'),
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
            'deleteByCriteria',
            function (EntityEvent $event) {
                $target = $event->getTarget();
                $repository = $target->getRepositoryForEntity($event->getEntityClassName());
                call_user_func_array(array($repository, 'deleteByCriteria'), $event->getParams());
                if ($repository instanceof FlushableInterface) {
                    $repository->flush();
                }
            },
            0
        );

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
            'findAll',
            function (EntityEvent $event) {
                $target = $event->getTarget();
                $repository = $target->getRepositoryForEntity($event->getEntityClassName());
                $resultSet = $event->getResult();
                $resultSet->initialize(
                    array(call_user_func_array(
                        array($repository, 'findAll'),
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
            'findByCriteria',
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
            'findOneByCriteria',
            function (EntityEvent $event) {
                $target = $event->getTarget();
                $repository = $target->getRepositoryForEntity($event->getEntityClassName());
                $resultSet = $event->getResult();
                $resultSet->initialize(
                    array(call_user_func_array(
                        array($repository, 'findOneByCriteria'),
                        $event->getParams()
                    ))
                );
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
     * Returns the FQCN of the entity handled by this service.
     *
     * @return string
     */
    protected function getEntityServiceName()
    {
        return $this->getEvent()->getEntityClassName();
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

        return $this->trigger(__FUNCTION__, array(
            'entity' => $entity,
        ));
    }

    /**
     * {@inheritdoc}
     * @param array $criteria
     * @throws RuntimeException
     */
    public function deleteBy(array $criteria)
    {
        if (!$this->repositoryIsDeletable($this->getEntityServiceName())) {
            throw new RuntimeException('Repository is not deletable');
        }

        return $this->trigger(__FUNCTION__, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * {@inheritdoc}
     *
     * @param Criteria $criteria
     * @throws RuntimeException
     */
    public function deleteByCriteria(Criteria $criteria)
    {
        if (!$this->repositoryIsDeletable($this->getEntityServiceName())) {
            throw new RuntimeException('Repository is not deletable');
        }

        return $this->trigger(__FUNCTION__, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * {@inheritdoc}
     *
     * @param array $criteria
     * @throws RuntimeException
     */
    public function countBy(array $criteria)
    {
        if (!$this->isRepositoryReadable($this->getEntityServiceName())) {
            throw new RuntimeException('Repository is not readable');
        }

        return $this->trigger(__FUNCTION__, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * {@inheritdoc}
     *
     * @param Criteria $criteria
     * @throws RuntimeException
     */
    public function countByCriteria(Criteria $criteria)
    {
        if (!$this->isRepositoryReadable($this->getEntityServiceName())) {
            throw new RuntimeException('Repository is not readable');
        }

        return $this->trigger(__FUNCTION__, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed $id
     */
    public function find($id)
    {
        if (!$this->isRepositoryReadable($this->getEntityServiceName())) {
            throw new RuntimeException('Repository is not readable');
        }

        return $this->trigger(__FUNCTION__, array(
            'id' => $id,
        ));
    }

    /**
     * {@inheritdoc}
     *
     * @return array Returns the entities that exist.
     */
    public function findAll()
    {
        if (!$this->isRepositoryReadable($this->getEntityServiceName())) {
            throw new RuntimeException('Repository is not readable');
        }

        return $this->trigger(__FUNCTION__, array());
    }

    /**
     * {@inheritdoc}
     *
     * @param array|Criteria $criteria
     * @param array|null $order
     * @param int|null $limit
     * @param int|null $offset
     * @throws RuntimeException
     */
    public function findBy(array $criteria, array $order = null, $limit = null, $offset = null)
    {
        if (!$this->isRepositoryReadable($this->getEntityServiceName())) {
            throw new RuntimeException('Repository is not readable');
        }

        return $this->trigger(__FUNCTION__, array(
            'criteria' => $criteria,
            'order' => $order,
            'limit' => $limit,
            'offset' => $offset
        ));
    }

    /**
     * {@inheritdoc}
     *
     * @param Criteria $criteria
     * @throws RuntimeException
     */
    public function findByCriteria(Criteria $criteria)
    {
        if (!$this->isRepositoryReadable($this->getEntityServiceName())) {
            throw new RuntimeException('Repository is not readable');
        }

        return $this->trigger(__FUNCTION__, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * {@inheritdoc}
     *
     * @param array $criteria
     * @param array|null $order
     */
    public function findOneBy(array $criteria, array $order = null)
    {
        if (!$this->isRepositoryReadable($this->getEntityServiceName())) {
            throw new RuntimeException('Repository is not readable');
        }

        return $this->trigger(__FUNCTION__, array(
            'criteria' => $criteria,
            'order' => $order,
        ));
    }

    /**
     * {@inheritdoc}
     *
     * @param Criteria $criteria
     */
    public function findOneByCriteria(Criteria $criteria)
    {
        if (!$this->isRepositoryReadable($this->getEntityServiceName())) {
            throw new RuntimeException('Repository is not readable');
        }

        return $this->trigger(__FUNCTION__, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * {@inheritdoc}
     *
     * @param IdentifiableInterface $entity
     * @throws RuntimeException
     */
    public function persist(IdentifiableInterface $entity)
    {
        if (!$this->isRepositoryWritable($this->getEntityServiceName())) {
            throw new RuntimeException('Repository is not writable');
        }

        return $this->trigger(__FUNCTION__, array(
            'entity' => $entity,
            'isNew' => !$entity->hasId(),
        ));
    }

    /**
     * {@inheritdoc}
     *
     * @param array|Iterator|Collection $entities The collection with entities.
     * @throws RuntimeException
     */
    public function multiPersist($entities)
    {
        if (!$this->isRepositoryWritable($this->getEntityServiceName())) {
            throw new RuntimeException('Repository is not writable');
        }

        return $this->trigger(__FUNCTION__, array(
            'entities' => $entities,
        ));
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
    protected function isRepositoryWritable($entityName)
    {
        return $this->getRepositoryForEntity($entityName) instanceof WritableInterface;
    }

    /**
     * Returns true when the repository for $entityName is readable
     *
     * @param $entityName
     * @return bool
     */
    protected function isRepositoryReadable($entityName)
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
