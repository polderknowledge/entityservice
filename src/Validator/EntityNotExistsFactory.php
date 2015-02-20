<?php
/**
 * Polder Knowledge / Entity Service (http://polderknowledge.nl)
 *
 * @link http://developers.polderknowledge.nl/gitlab/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2015-2015 Polder Knowledge (http://www.polderknowledge.nl)
 * @license http://polderknowledge.nl/license/proprietary proprietary
 */

namespace PolderKnowledge\EntityService\Validator;

use PolderKnowledge\EntityService\Validator\EntityNotExists;
use Zend\ServiceManager\MutableCreationOptionsInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for EntityNotExists validator
 */
class EntityNotExistsFactory extends AbstractEntityValidatorFactory implements MutableCreationOptionsInterface
{
    /**
     * Creation options
     *
     * @var array
     */
    protected $options;

    /**
     * Uses the EntityServiceManager to fetch a EntityService for the entity set in the options array.
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return EntityNotExists
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $validator = new EntityNotExists($this->options);
        $validator->setEntityService($this->createEntityService($serviceLocator, $this->options['entity']));
        $this->options = null;

        return $validator;
    }

    /**
     * {@inheritdoc}
     *
     * @param array $options
     */
    public function setCreationOptions(array $options)
    {
        $this->options = $options;
    }

}
