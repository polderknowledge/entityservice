<?php
/**
 * Polder Knowledge / Entity Service (http://polderknowledge.nl)
 *
 * @link http://developers.polderknowledge.nl/gitlab/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2015-2015 Polder Knowledge (http://www.polderknowledge.nl)
 * @license http://polderknowledge.nl/license/proprietary proprietary
 */

namespace PolderKnowledge\EntityService;

use PolderKnowledge\EntityService\Event\EntityEvent;
use PolderKnowledge\EntityService\Exception\RuntimeException;
use PolderKnowledge\EntityService\Exception\ServiceException;
use PolderKnowledge\EntityService\Entity\Feature\IdentifiableInterface;
use PolderKnowledge\EntityService\Repository\EntityRepositoryInterface;
use PolderKnowledge\EntityService\Repository\Feature\DeletableInterface;
use PolderKnowledge\EntityService\Repository\Feature\FlushableInterface;
use PolderKnowledge\EntityService\Repository\Feature\ReadableInterface;
use PolderKnowledge\EntityService\Repository\Feature\WritableInterface;
use PolderKnowledge\EntityService\TransactionAwareInterface;
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
        $this->listeners = array();

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
                $repository = $event->getTarget()->getRepository();

                $event->setResult((int)call_user_func_array(
                    array($repository, 'countBy'),
                    $event->getParams()
                ));
            },
            0
        );

        $this->listeners[] = $events->attach(
            'delete',
            function (EntityEvent $event) {
                $repository = $event->getTarget()->getRepository();

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
                $repository = $event->getTarget()->getRepository();

                call_user_func_array(array($repository, 'deleteBy'), $event->getParams());
                if ($repository instanceof FlushableInterface) {
                    $repository->flush();
                }
            },
            0
        );

        $this->listeners[] = $events->attach(
            'find',
            function (EntityEvent $event) {
                $repository = $event->getTarget()->getRepository();

                $event->setResult(call_user_func_array(
                    array($repository, 'find'),
                    $event->getParams()
                ));
            },
            0
        );

        $this->listeners[] = $events->attach(
            'findAll',
            function (EntityEvent $event) {
                $repository = $event->getTarget()->getRepository();

                $event->setResult(call_user_func_array(
                    array($repository, 'findAll'),
                    $event->getParams()
                ));
            },
            0
        );

        $this->listeners[] = $events->attach(
            'findBy',
            function (EntityEvent $event) {
                $repository = $event->getTarget()->getRepository();

                $event->setResult(call_user_func_array(
                    array($repository, 'findBy'),
                    $event->getParams()
                ));
            },
            0
        );

        $this->listeners[] = $events->attach(
            'findOneBy',
            function (EntityEvent $event) {
                $repository = $event->getTarget()->getRepository();

                $event->setResult(call_user_func_array(
                    array($repository, 'findOneBy'),
                    $event->getParams()
                ));
            },
            0
        );

        $this->listeners[] = $events->attach(
            'persist',
            function (EntityEvent $event) {
                $repository = $event->getTarget()->getRepository();

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
                $repository = $event->getTarget()->getRepository();

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
     * Returns the FQCN of the entity handled by this service.
     *
     * @return string
     */
    protected function getEntityServiceName()
    {
        return $this->getEvent()->getEntityClassName();
    }

    /**
     * @inheritdoc
     */
    public function delete(IdentifiableInterface $entity)
    {
        if (!$this->isRepositoryDeletable()) {
            throw new RuntimeException(sprintf(
                'The repository for %s is not deletable.',
                $this->getEntityServiceName()
            ));
        }

        return $this->trigger(__FUNCTION__, array(
            'entity' => $entity,
        ));
    }

    /**
     * @inheritdoc
     */
    public function deleteBy($criteria)
    {
        if (!$this->isRepositoryDeletable()) {
            throw new RuntimeException(sprintf(
                'The repository for %s is not deletable.',
                $this->getEntityServiceName()
            ));
        }

        return $this->trigger(__FUNCTION__, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * @inheritdoc
     */
    public function countBy($criteria)
    {
        if (!$this->isRepositoryReadable()) {
            throw new RuntimeException(sprintf(
                'The repository for %s is not readable.',
                $this->getEntityServiceName()
            ));
        }

        return $this->trigger(__FUNCTION__, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * @inheritdoc
     */
    public function find($id)
    {
        if (!$this->isRepositoryReadable()) {
            throw new RuntimeException(sprintf(
                'The repository for %s is not readable.',
                $this->getEntityServiceName()
            ));
        }

        return $this->trigger(__FUNCTION__, array(
            'id' => $id,
        ));
    }

    /**
     * @inheritdoc
     */
    public function findAll()
    {
        if (!$this->isRepositoryReadable()) {
            throw new RuntimeException(sprintf(
                'The repository for %s is not readable.',
                $this->getEntityServiceName()
            ));
        }

        return $this->trigger(__FUNCTION__, array());
    }

    /**
     * @inheritdoc
     */
    public function findBy($criteria)
    {
        if (!$this->isRepositoryReadable()) {
            throw new RuntimeException(sprintf(
                'The repository for %s is not readable.',
                $this->getEntityServiceName()
            ));
        }

        return $this->trigger(__FUNCTION__, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * @inheritdoc
     */
    public function findOneBy($criteria)
    {
        if (!$this->isRepositoryReadable()) {
            throw new RuntimeException(sprintf(
                'The repository for %s is not readable.',
                $this->getEntityServiceName()
            ));
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
        if (!$this->isRepositoryWritable()) {
            throw new RuntimeException(sprintf(
                'The repository for %s is not writable.',
                $this->getEntityServiceName()
            ));
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
        if (!$this->isRepositoryWritable()) {
            throw new RuntimeException(sprintf(
                'The repository for %s is not writable.',
                $this->getEntityServiceName()
            ));
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
     * @return mixed
     */
    protected function trigger($name, array $params)
    {
        $event = clone $this->getEvent();
        $event->setName($name);
        $event->setParams($params);

        $responseCollection = $this->getEventManager()->trigger($event);

        if ($responseCollection->stopped() && $event->isError()) {
            throw new RuntimeException($event->getError(), $event->getErrorNr());
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
            throw new ServiceException(sprintf(
                'The repository for %s doesn\'t support Transactions',
                $this->getEntityServiceName()
            ));
        }

        $this->getRepository()->beginTransaction();
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     *
     * @throws ServiceException
     */
    public function rollBackTransaction()
    {
        if ($this->isTransactionEnabled() === false) {
            throw new ServiceException(sprintf(
                'The repository for %s doesn\'t support Transactions',
                $this->getEntityServiceName()
            ));
        }

        $this->getRepository()->rollBackTransaction();
    }

    /**
     * {@inheritDoc}
     */
    public function isTransactionEnabled()
    {
        return $this->getRepository() instanceof TransactionAwareInterface;
    }

    /**
     * Returns true when the repository for $entityName is writable
     *
     * @param $entityName
     * @return bool
     */
    protected function isRepositoryWritable()
    {
        return $this->getRepository() instanceof WritableInterface;
    }

    /**
     * Returns true when the repository for $entityName is readable
     *
     * @param $entityName
     * @return bool
     */
    protected function isRepositoryReadable()
    {
        return $this->getRepository() instanceof ReadableInterface;
    }

    /**
     * Returns true when the repository for $entityName has delete behavior
     *
     * @param $entityName
     * @return bool
     */
    protected function isRepositoryDeletable()
    {
        return $this->getRepository() instanceof DeletableInterface;
    }
}
