<?php
/**
 * Polder Knowledge / Entity Service (http://polderknowledge.nl)
 *
 * @link http://developers.polderknowledge.nl/gitlab/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2015-2015 Polder Knowledge (http://www.polderknowledge.nl)
 * @license http://polderknowledge.nl/license/proprietary proprietary
 */

namespace PolderKnowledge\EntityService\Service;

use Exception;
use PolderKnowledge\EntityService\Exception\InvalidArgumentException;
use PolderKnowledge\EntityService\Exception\ServiceException;
use PolderKnowledge\EntityService\Service\TransactionAwareInterface;
use SplObjectStorage;
use Zend\Stdlib\CallbackHandler;
use Zend\Stdlib\PriorityQueue;

class AggregateTransactionEntityService
{
    /**
     * @var PriorityQueue|CallbackHandler[]
     */
    protected $transactionCommands;

    /**
     * @var SplObjectStorage|TransactionAwareInterface
     */
    protected $services;

    /**
     * Initializes a new instance of this class.
     */
    public function __construct()
    {
        $this->transactionCommands = new PriorityQueue();
        $this->services = new SplObjectStorage();
    }

    /**
     * Adds a command that should be executed.
     *
     * @param callable $callback Expects PHP callback
     * @param int $priority If provided, the priority at which to register the callable
     * @return CallbackHandler If attaching callable (to allow later unsubscribe);
     * @throws InvalidArgumentException
     */
    public function addCommand($callback, $parameters = array(), $priority = 1)
    {
        if (is_array($callback)) {
            if ((current($callback) instanceof TransactionAwareInterface) === false) {
                throw new InvalidArgumentException('Callback is not an instance of TransactionAwareInterface');
            }

            if (!current($callback)->isTransactionEnabled()) {
                throw new InvalidArgumentException('Transactions are not enabled for service');
            }
        }

        $handler = new CallbackHandler($callback, array('priority' => $priority, 'parameters' => $parameters));
        $this->services->attach(current($callback));
        $this->transactionCommands->insert($handler, $priority);

        return $handler;
    }

    public function execute()
    {
        $this->beginTransaction();

        try {
            foreach ($this->transactionCommands as $command) {
                $command->call($command->getMetadatum('parameters'));
            }

            $this->commitTransaction();
        } catch (Exception $e) {
            $this->rollbackTransaction();

            throw new ServiceException('EntityService transaction failed because of previous exception.', 1, $e);
        }
    }

    private function beginTransaction()
    {
        foreach ($this->services as $service) {
            $service->beginTransaction();
        }
    }

    private function commitTransaction()
    {
        foreach ($this->services as $service) {
            $service->commitTransaction();
        }
    }

    private function rollbackTransaction()
    {
        foreach ($this->services as $service) {
            $service->rollBackTransaction();
        }
    }
}
