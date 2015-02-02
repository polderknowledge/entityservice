<?php
/**
 * Polder Knowledge / Entity Service (http://polderknowledge.nl)
 *
 * @link http://developers.polderknowledge.nl/gitlab/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2015-2015 Polder Knowledge (http://www.polderknowledge.nl)
 * @license http://polderknowledge.nl/license/proprietary proprietary
 */

namespace PolderKnowledge\EntityService\Feature;

interface DeletableInterface
{
    /**
     * Checks if the object is deleted.
     *
     * @return boolean Returns true when the object is deleted; false otherwise.
     */
    public function isDeleted();
}
