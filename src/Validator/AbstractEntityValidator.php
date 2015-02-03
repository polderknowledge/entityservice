<?php
/**
 * Polder Knowledge / Entity Service (http://polderknowledge.nl)
 *
 * @link http://developers.polderknowledge.nl/gitlab/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2015-2015 Polder Knowledge (http://www.polderknowledge.nl)
 * @license http://polderknowledge.nl/license/proprietary proprietary
 */

namespace PolderKnowledge\EntityService\Validator;

use PolderKnowledge\EntityService\ServiceProblem;
use PolderKnowledge\EntityService\ServiceResult;
use PolderKnowledge\EntityService\Service\EntityServiceInterface;
use Zend\Validator\AbstractValidator;

/**
 * Base class for validators using an EntityServiceInterface.
 */
abstract class AbstractEntityValidator extends AbstractValidator
{
    /**
     * @var EntityServiceInterface
     */
    protected $entityService;

    /**
     * @var string Method to be called
     */
    protected $method = 'findBy';

    /**
     * @var string Name of the field to search
     */
    protected $field = 'id';

    /**
     * @param EntityServiceInterface $entityService
     */
    public function setEntityService(EntityServiceInterface $entityService)
    {
        $this->entityService = $entityService;
    }

    /**
     * Sets the method option.
     *
     * @param string $method The name of the method to set.
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * Sets the field option.
     *
     * @param string $field The name of the field to set.
     */
    public function setField($field)
    {
        $this->field = $field;
    }

    /**
     * @param mixed $value
     * @return ServiceResult|ServiceProblem
     */
    protected function fetchResult($value)
    {
        return call_user_func_array(array(
            $this->entityService,
            $this->method
        ), array(
            'citeria' => array($this->field => $value)
        ));
    }
}
