<?php
/**
 * Polder Knowledge / Entity Service (http://polderknowledge.nl)
 *
 * @link http://developers.polderknowledge.nl/gitlab/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2015-2015 Polder Knowledge (http://www.polderknowledge.nl)
 * @license http://polderknowledge.nl/license/proprietary proprietary
 */

namespace PolderKnowledge\EntityServiceTestAsset;

use PolderKnowledge\EntityService\Entity\Feature\DeletableInterface;
use PolderKnowledge\EntityService\Entity\Feature\IdentifiableInterface;

class MyEntity implements IdentifiableInterface, DeletableInterface
{
    public function getId()
    {
    }

    public function hasId()
    {
    }

    public function setId($id)
    {
    }

    public function isDeleted()
    {
    }
}
