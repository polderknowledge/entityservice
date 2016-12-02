<?php
/**
 * Polder Knowledge / entityservice (https://polderknowledge.com)
 *
 * @link https://github.com/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2016 Polder Knowledge (https://polderknowledge.com)
 * @license https://github.com/polderknowledge/entityservice/blob/master/LICENSE.md MIT
 */

namespace PolderKnowledge\EntityService\Repository\Doctrine\Service;

use Interop\Container\ContainerInterface;
use PolderKnowledge\EntityService\Repository\Doctrine\ORMRepository;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * RepositoryAbstractFactory creates a default ORMRepository for an EntityService
 */
class RepositoryAbstractFactory implements AbstractFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $doctrineServiceName = 'Doctrine\\ORM\\EntityManager';

        if ($options && array_key_exists('doctrine_entity_manager', $options)) {
            $doctrineServiceName = $options['doctrine_entity_manager'];
        }

        $entityManager = $container->get($doctrineServiceName);

        return new ORMRepository($entityManager, $requestedName);
    }

    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        if (method_exists($serviceLocator, 'getServiceLocator')) {
            $serviceLocator = $serviceLocator->getServiceLocator();
        }

        return $this->canCreate($serviceLocator, $requestedName);
    }

    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        if (method_exists($serviceLocator, 'getServiceLocator')) {
            $serviceLocator = $serviceLocator->getServiceLocator();
        }

        return $this($serviceLocator, $requestedName);
    }
}
