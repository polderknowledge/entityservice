<?php
/**
 * Polder Knowledge / Entity Service (http://polderknowledge.nl)
 *
 * @link http://developers.polderknowledge.nl/gitlab/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2015-2015 Polder Knowledge (http://www.polderknowledge.nl)
 * @license http://polderknowledge.nl/license/proprietary proprietary
 */

namespace PolderKnowledge\EntityServiceTest\Service;

use Exception;
use PHPUnit_Framework_TestCase;
use stdClass;
use PolderKnowledge\EntityService\Exception\InvalidArgumentException;
use PolderKnowledge\EntityService\Service\AggregateTransactionEntityService;

class AggregateTransactionEntityServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var AggregateTransactionEntityService
     */
    protected $service;

    protected function setUp()
    {
        $this->service = new AggregateTransactionEntityService();
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testAddCommandCheckesType()
    {
        // Arrange
        // ...

        // Act
        $this->service->addCommand(array(
            $this->getMock('PolderKnowledge\\EntityService\\Service\\TransactionAwareInterface'),
            'beginTransaction'
        ));

        $this->service->addCommand(array(new stdClass(), 'foo'));

        // Assert
        // ... asserting happens because of the expectedException annotation
    }

    public function testExecuteCallesTransactionMethods()
    {
        // Arrange
        $firstService = $this->serviceSetup();
        $firstService->expects($this->once())->method('beginTransaction');
        $firstService->expects($this->once())->method('commitTransaction');

        // Act
        $this->service->execute();

        // Assert
        // ...
    }

    public function testExecuteRegisteredCommands()
    {
        // Arrange
        $firstService = $this->serviceSetup();
        $firstService->expects($this->once())->method('someMethod');
        $firstService->expects($this->once())->method('someOtherMethod');

        // Act
        $this->service->execute();

        // Assert
        // ...
    }

    /**
     * @expectedException PolderKnowledge\EntityService\Exception\ServiceException
     */
    public function testExecuteRollbackOnException()
    {
        // Arrange
        $firstService = $this->serviceSetup();
        $firstService->expects($this->once())->method('someMethod')->will($this->throwException(new Exception()));
        $firstService->expects($this->never())->method('someOtherMethod');
        $firstService->expects($this->once())->method('rollbackTransaction');

        // Act
        $this->service->execute();

        // Assert
        // ...
    }

    public function testExecuteWithArguments()
    {
        // Arrange
        $argument1 = 'foo';
        $argument2 = 'bar';

        $firstService = $this->serviceSetup();
        $firstService->expects($this->once())->method('methodWithParameters')->with($argument1, $argument2);

        $this->service->addCommand(array($firstService, 'methodWithParameters'), array($argument1, $argument2));

        // Act
        $this->service->execute();

        // Assert
        // ...
    }

    protected function serviceSetup()
    {
        $firstService = $this->getMock(
            'PolderKnowledge\\EntityService\\Service\\TransactionAwareInterface',
            array(
                'beginTransaction',
                'commitTransaction',
                'rollbackTransaction',
                'isTransactionEnabled',
                'someMethod',
                'someOtherMethod',
                'methodWithParameters'
            )
        );
        $firstService->expects($this->any())->method('isTransactionEnabled')->will($this->returnValue(true));

        $this->service->addCommand(array($firstService, 'someMethod'));
        $this->service->addCommand(array($firstService, 'someOtherMethod'));

        return $firstService;
    }
}
