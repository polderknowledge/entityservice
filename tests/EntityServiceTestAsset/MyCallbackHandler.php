<?php
/**
 * Polder Knowledge / entityservice (https://polderknowledge.com)
 *
 * @link https://github.com/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2016 Polder Knowledge (https://polderknowledge.com)
 * @license https://github.com/polderknowledge/entityservice/blob/master/LICENSE.md MIT
 */

namespace PolderKnowledge\EntityServiceTestAsset;

use PolderKnowledge\EntityService\Feature\TransactionAwareInterface;
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

    public function rollbackTransaction()
    {
    }
}
