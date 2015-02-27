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

/**
 * Plugin manager for EntityServiceInterfaces
 */
class EntityServiceManager extends AbstractPluginManager
{
    /**
     * {@inheritdoc}
     * @param ConfigInterface $configuration
     */
    public function __construct(ConfigInterface $configuration = null)
    {
        parent::__construct($configuration);

        $this->addAbstractFactory(new EntityServiceAbstractServiceFactory());
        $this->addInitializer(new EventManagerInitializer());
    }

    /**
     * {@inheritdoc}
     * @param mixed $plugin
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof EntityServiceInterface) {
            return;
        }

        throw new InvalidServiceNameException(
            sprintf(
                'Plugin of type %s is invalid;
                must implement %s\EntityServiceInterface',
                (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
                __NAMESPACE__
            )
        );
    }
}
