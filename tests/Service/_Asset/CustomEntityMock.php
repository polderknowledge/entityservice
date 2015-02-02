<?php
/**
 * Polder Knowledge / Entity Service (http://polderknowledge.nl)
 *
 * @link http://developers.polderknowledge.nl/gitlab/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2015-2015 Polder Knowledge (http://www.polderknowledge.nl)
 * @license http://polderknowledge.nl/license/proprietary proprietary
 */

namespace PolderKnowledge\EntityServiceTest\Service\_Asset;

use PolderKnowledge\EntityService\Feature\DeletableInterface;
use PolderKnowledge\EntityService\Feature\IdentifiableInterface;

class CustomEntityMock implements IdentifiableInterface, DeletableInterface
{
    public function isDeleted()
    {
    }

    public function getId()
    {
    }

    public function hasId()
    {
    }

    public function setId($id)
    {
    }
}
