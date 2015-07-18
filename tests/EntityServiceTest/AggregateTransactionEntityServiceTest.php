<?php
/**
 * Polder Knowledge / Entity Service (http://polderknowledge.nl)
 *
 * @link http://developers.polderknowledge.nl/gitlab/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2015-2015 Polder Knowledge (http://www.polderknowledge.nl)
 * @license http://polderknowledge.nl/license/proprietary proprietary
 */

namespace PolderKnowledge\EntityServiceTest;

use PHPUnit_Framework_TestCase;
use PolderKnowledge\EntityService\AggregateTransactionEntityService;
use PolderKnowledge\EntityService\Exception\InvalidArgumentException;
use PolderKnowledge\EntityService\TransactionAwareInterface;
use PolderKnowledge\EntityServiceTestAsset\MyCallbackHandler;

class AggregateTransactionEntityServiceTest extends PHPUnit_Framework_TestCase
{
    public function testAddCommand()
    {
        // Arrange
        $aggregateTransactionEntityService = new AggregateTransactionEntityService();

        $command = new MyCallbackHandler(false);

        // Act
        $aggregateTransactionEntityService->addCommand(array($command, 'onAction'));
        $aggregateTransactionEntityService->execute();

        // Assert
        // ...
    }

    /**
     * @expectedException PolderKnowledge\EntityService\Exception\InvalidArgumentException
     * @expectedExceptionMessage Callback is not an instance of TransactionAwareInterface
     */
    public function testAddCommandNotTransactionAware()
    {
        // Arrange
        $aggregateTransactionEntityService = new AggregateTransactionEntityService();

        // Act
        $aggregateTransactionEntityService->addCommand(array());

        // Assert
        // ...
    }

    /**
     * @expectedException PolderKnowledge\EntityService\Exception\InvalidArgumentException
     * @expectedExceptionMessage Transactions are not enabled for service
     */
    public function testAddCommandTransactionAwareNotEnabled()
    {
        // Arrange
        $aggregateTransactionEntityService = new AggregateTransactionEntityService();
        $command = $this->getMockForAbstractClass(TransactionAwareInterface::class);

        // Act
        $aggregateTransactionEntityService->addCommand(array($command, 'onAction'));

        // Assert
        // ...
    }

    /**
     * @expectedException PolderKnowledge\EntityService\Exception\ServiceException
     * @expectedExceptionMessage EntityService transaction failed because of previous exception.
     */
    public function testAddCommandWithException()
    {
        // Arrange
        $aggregateTransactionEntityService = new AggregateTransactionEntityService();
        $command = new MyCallbackHandler(true);

        // Act
        $aggregateTransactionEntityService->addCommand(array($command, 'onAction'));
        $aggregateTransactionEntityService->execute();

        // Assert
        // ...
    }
}
