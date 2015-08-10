<?php
/**
 * Polder Knowledge / Entity Service (http://polderknowledge.nl)
 *
 * @link http://developers.polderknowledge.nl/gitlab/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2015-2015 Polder Knowledge (http://www.polderknowledge.nl)
 * @license http://polderknowledge.nl/license/proprietary proprietary
 */

namespace PolderKnowledge\EntityService\Event;

use Zend\EventManager\EventManagerAwareInterface;
use Zend\ServiceManager\InitializerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Initializer to set the shared event manager to the entity service EventManager
 */
class EventManagerInitializer implements InitializerInterface
{
    /**
     * {@inheritDoc}
     *
     * @param mixed $instance
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function initialize($instance, ServiceLocatorInterface $serviceLocator)
    {
        if ($instance instanceof EventManagerAwareInterface) {
            $eventManager = $serviceLocator->getServiceLocator()->get('EventManager');

            $instance->getEventManager()->setSharedManager($eventManager->getSharedManager());
        }
    }
}
