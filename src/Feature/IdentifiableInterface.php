<?php
/**
 * Polder Knowledge / Entity Service (http://polderknowledge.nl)
 *
 * @link http://developers.polderknowledge.nl/gitlab/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2015-2015 Polder Knowledge (http://www.polderknowledge.nl)
 * @license http://polderknowledge.nl/license/proprietary proprietary
 */

namespace PolderKnowledge\EntityService\Feature;

/**
 * IdentifiableInterface makes it possible to identify a object by id
 */
interface IdentifiableInterface
{
    /**
     * Gets the identifier.
     *
     * @return mixed
     */
    public function getId();

    /**
     * Checks if the instance of this class has an identifier
     *
     * @return bool Returns true when there is an id available; false otherwise.
     */
    public function hasId();

    /**
     * Sets the identifier.
     *
     * @param mixed $id The value to set.
     * @return IdentifiableInterface
     */
    public function setId($id);
}
