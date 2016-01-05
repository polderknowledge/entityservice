<?php
/**
 * Polder Knowledge / Entity Service (http://polderknowledge.nl)
 *
 * @link http://developers.polderknowledge.nl/gitlab/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2015-2015 Polder Knowledge (http://www.polderknowledge.nl)
 * @license http://polderknowledge.nl/license/proprietary proprietary
 */

namespace PolderKnowledge\EntityService\Service;

use PolderKnowledge\EntityService\DefaultEntityService;
use PolderKnowledge\EntityService\Entity\Feature\IdentifiableInterface;
use ReflectionClass;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Abstract service factory to create a DefaultEntityService
 */
class EntityServiceAbstractServiceFactory implements AbstractFactoryInterface
{
    const REPOSITORY_SERVICE_KEY = 'EntityRepositoryManager';

    /**
     * {@inheritdoc}
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @param $requestedName
     * @return true
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        if (!class_exists($requestedName)) {
            return false;
        }

        $reflectionClass = new ReflectionClass($requestedName);
        return $reflectionClass->implementsInterface(IdentifiableInterface::class);
    }

    /**
     * Creates a new instance of DefaultEntityService configured with the $requestedName
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @param $requestedName
     * @return DefaultEntityService
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $entityRepositoryManager = $serviceLocator->getServiceLocator()->get(self::REPOSITORY_SERVICE_KEY);

        return new DefaultEntityService($entityRepositoryManager->get($requestedName), $requestedName);
    }
}
