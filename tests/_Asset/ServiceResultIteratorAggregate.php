<?php
/**
 * Polder Knowledge / Entity Service (http://polderknowledge.nl)
 *
 * @link http://developers.polderknowledge.nl/gitlab/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2015-2015 Polder Knowledge (http://www.polderknowledge.nl)
 * @license http://polderknowledge.nl/license/proprietary proprietary
 */

namespace PolderKnowledge\EntityServiceTest\_Asset;

use IteratorAggregate;

class ServiceResultIteratorAggregate implements IteratorAggregate
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getIterator()
    {
        return $this->data;
    }
}
