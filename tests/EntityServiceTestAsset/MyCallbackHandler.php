<?php
/**
 * Polder Knowledge / Entity Service (http://polderknowledge.nl)
 *
 * @link http://developers.polderknowledge.nl/gitlab/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2015-2015 Polder Knowledge (http://www.polderknowledge.nl)
 * @license http://polderknowledge.nl/license/proprietary proprietary
 */

namespace PolderKnowledge\EntityServiceTestAsset;

use PolderKnowledge\EntityService\TransactionAwareInterface;
use RuntimeException;

class MyCallbackHandler implements TransactionAwareInterface
{
    private $throwException;

    public function __construct($throwException)
    {
        $this->throwException = $throwException;
    }

    public function onAction()
    {
        if ($this->throwException) {
            throw new RuntimeException('Oh oh..');
        }
    }

    public function beginTransaction()
    {
    }

    public function commitTransaction()
    {
    }

    public function isTransactionEnabled()
    {
        return true;
    }

    public function rollBackTransaction()
    {
    }
}
