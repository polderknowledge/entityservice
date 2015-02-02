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
use PolderKnowledge\EntityService\Repository\DeletableInterface;
use PolderKnowledge\EntityService\Repository\FlushableInterface;
use PolderKnowledge\EntityService\Repository\ReadableInterface;
use PolderKnowledge\EntityService\Repository\WritableInterface;
use Zend\ServiceManager\AbstractPluginManager;

class EntityRepositoryManager extends AbstractPluginManager
{
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof DeletableInterface ||
            $plugin instanceof FlushableInterface ||
            $plugin instanceof ReadableInterface ||
            $plugin instanceof WritableInterface
            ) {
            return;
        }

        throw new InvalidServiceNameException(sprintf(
            'Plugin of type %s is invalid; must implement '
            . 'PolderKnowledge\EntityService\Repository\ReadableInterface'
            . 'or PolderKnowledge\EntityService\Repository\WritableInterface'
            . 'or PolderKnowledge\EntityService\Repository\FlushableInterface'
            . 'or PolderKnowledge\EntityService\Repository\DeletableInterface',
            is_object($plugin) ? get_class($plugin) : gettype($plugin)
        ));
    }
}
