<?php
/**
 * Polder Knowledge / Entity Service (http://polderknowledge.nl)
 *
 * @link http://developers.polderknowledge.nl/gitlab/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2015-2015 Polder Knowledge (http://www.polderknowledge.nl)
 * @license http://polderknowledge.nl/license/proprietary proprietary
 */

namespace PolderKnowledge\EntityService\Event;

use PolderKnowledge\EntityService\Exception\RuntimeException;
use Zend\EventManager\Event;

/**
 * The representation of an event with an entity.
 */
final class EntityEvent extends Event
{
    /**
     * The FQCN of the entity that this event handles.
     *
     * @var string
     */
    private $entityClassName;

    /**
     * A flag that indicates whether or not prpagation can be stopped.
     *
     * @var bool
     */
    private $canStopPropagation;

    /**
     * The error message that can occur when working with the entity.
     *
     * @var string
     */
    private $error;

    /**
     * The error code of the error that can occur when working with the entity.
     *
     * @var integer
     */
    private $errornr;

    /**
     * The result of a service manager call.
     *
     * @var mixed
     */
    private $result;

    /**
     * Initializes a new instance of this class.
     * Accepts a target and its parameters.
     *
     * @param string $name Event name The name of the event.
     * @param string|object $target The target of the event.
     * @param array|ArrayAccess $params Additional parameters of this event.
     */
    public function __construct($name = null, $target = null, $params = null)
    {
        parent::__construct($name, $target, $params);

        $this->canStopPropagation = true;
    }

    /**
     * Gets the list with entities that are the result of this event.
     *
     * @return mixed Returns the result of a service manager call.
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Sets the list with entities that are the result of this event.
     *
     * @param mixed $result The result of a service manager call.
     * @return EntityEvent Returns the instance of this class so that chaining can be used.
     */
    public function setResult($result)
    {
        $this->result = $result;
        return $this;
    }

    /**
     * Gets the FQCN of the entity that is used.
     *
     * @return string Returns a string containing the FQCN.
     */
    public function getEntityClassName()
    {
        return $this->entityClassName;
    }

    /**
     * Sets the FQCN of the entity that is used.
     *
     * @param string $name The FQCN of the entity.
     * @return EntityEvent Returns the instance of this class so that chaining can be used.
     * @throws RuntimeException
     */
    public function setEntityClassName($name)
    {
        $trimmedName = trim($name, '\\');

        if (!class_exists($trimmedName)) {
            throw new RuntimeException(sprintf('The class "%s" does not exist.', $trimmedName));
        }

        $this->entityClassName = $trimmedName;

        return $this;
    }

    /**
     * Enables or disables further event propagation.
     *
     * @param bool $flag A flag that indicates whether or not to stop propagation.
     * @return void
     */
    public function stopPropagation($flag = true)
    {
        if ($this->canStopPropagation) {
            parent::stopPropagation($flag);
        }
    }

    /**
     * Disable the option of stopping the event propagation
     *
     * @return self
     */
    public function disableStoppingOfPropagation()
    {
        $this->canStopPropagation = false;
        return $this;
    }

    /**
     * Gets the message of the error that occured when working with the entity.
     *
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Sets the message of the error that occured when working with the entity.
     *
     * @param string $error The error message to set.
     * @return self
     */
    public function setError($error)
    {
        $this->error = (string)$error;
        return $this;
    }

    /**
     * Gets the error number of the error that occured when working with the entity.
     *
     * @return integer
     */
    public function getErrorNr()
    {
        return $this->errornr;
    }

    /**
     * Sets the error number of the error that occured when working with the entity.
     *
     * @param integer $errorNr The error number to set.
     * @return EntityEvent Returns the instance of this class so that chaining can be used.
     */
    public function setErrorNr($errorNr)
    {
        $this->errornr = (int)$errorNr;
        return $this;
    }

    /**
     * Checks if error information is set.
     *
     * @return bool Returns true when error information is set; false otherwise.
     */
    public function isError()
    {
        return (null !== $this->errornr || null !== $this->error);
    }
}
