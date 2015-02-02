<?php
/**
 * Polder Knowledge / Entity Service (http://polderknowledge.nl)
 *
 * @link http://developers.polderknowledge.nl/gitlab/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2015-2015 Polder Knowledge (http://www.polderknowledge.nl)
 * @license http://polderknowledge.nl/license/proprietary proprietary
 */

namespace PolderKnowledge\EntityService\Service;

use PolderKnowledge\EntityService\Repository\DoctrineORMRepository;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DoctrineRepositoryAbstractFactory implements AbstractFactoryInterface
{
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        // Ideally we would check if the class that we want to create is an instance of IdentifiableInterface.
        // Unforunaltey we cannot check if that is the case at this point. The class that we request could expect
        // constructor params meaning that PHP warnings would occur here.

        return true;
    }

    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $serviceManager = $serviceLocator->getServiceLocator();
        $entityManager = $serviceManager->get("Doctrine\Orm\EntityManager");

        return new DoctrineORMRepository($entityManager, $requestedName);
    }
}
