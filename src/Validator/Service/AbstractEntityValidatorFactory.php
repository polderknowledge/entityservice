<?php
/**
 * Polder Knowledge / Entity Service (http://polderknowledge.nl)
 *
 * @link http://developers.polderknowledge.nl/gitlab/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2015-2015 Polder Knowledge (http://www.polderknowledge.nl)
 * @license http://polderknowledge.nl/license/proprietary proprietary
 */

namespace PolderKnowledge\EntityService\Validator\Service;

use PolderKnowledge\EntityService\EntityServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Base class for Entity validator Factories.
 * Contains a until method to create the EntityService Required for these validators
 */
abstract class AbstractEntityValidatorFactory implements FactoryInterface
{
    /**
     * Fetches a EntityServiceInterface instance from the EntityServiceManager
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param string $entityName
     * @return EntityServiceInterface
     */
    protected function createEntityService(ServiceLocatorInterface $serviceLocator, $entityName)
    {
        if ($serviceLocator instanceof ServiceLocatorAwareInterface) {
            $serviceLocator = $serviceLocator->getServiceLocator();
        }

        $entityServiceManager = $serviceLocator->get('EntityServiceManager');


        return $entityServiceManager->get($entityName);
    }
}
