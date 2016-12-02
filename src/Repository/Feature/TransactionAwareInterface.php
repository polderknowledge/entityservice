<?php
/**
 * Polder Knowledge / entityservice (https://polderknowledge.com)
 *
 * @link https://github.com/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2016 Polder Knowledge (https://polderknowledge.com)
 * @license https://github.com/polderknowledge/entityservice/blob/master/LICENSE.md MIT
 */

namespace PolderKnowledge\EntityService\Repository\Feature;

/**
 * The TransactionAwareInterface interface makes it possible work with transactions on a repository.
 */
interface TransactionAwareInterface
{
    /**
     * Starts a new transaction.
     *
     * @return void
     */
    public function beginTransaction();

    /**
     * Commits a started transaction.
     *
     * @return void
     */
    public function commitTransaction();

    /**
     * Rolls back a started transaction.
     *
     * @return void
     */
    public function rollbackTransaction();
}
