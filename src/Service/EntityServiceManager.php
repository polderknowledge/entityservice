<?php
/**
 * Polder Knowledge / Entity Service (http://polderknowledge.nl)
 *
 * @link http://developers.polderknowledge.nl/gitlab/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2015-2015 Polder Knowledge (http://www.polderknowledge.nl)
 * @license http://polderknowledge.nl/license/proprietary proprietary
 */

namespace PolderKnowledge\EntityService\Service;

use PolderKnowledge\EntityService\Exception\InvalidServiceNameException;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ConfigInterface;

class EntityServiceManager extends AbstractPluginManager
{
    public function __construct(ConfigInterface $configuration = null)
    {
        parent::__construct($configuration);

        $this->addAbstractFactory(new EntityServiceAbstractServiceFactory());
        $this->addInitializer(new EventManagerInitializer());
    }

    public function validatePlugin($plugin)
    {
        if ($plugin instanceof EntityServiceInterface) {
            return;
        }

        throw new InvalidServiceNameException(sprintf(
            'Plugin of type %s is invalid; must implement PolderKnowledge\EntityService\Service\EntityServiceInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
            __NAMESPACE__
        ));
    }
}
