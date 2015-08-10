<?php
/**
 * Polder Knowledge / Entity Service (http://polderknowledge.nl)
 *
 * @link http://developers.polderknowledge.nl/gitlab/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2015-2015 Polder Knowledge (http://www.polderknowledge.nl)
 * @license http://polderknowledge.nl/license/proprietary proprietary
 */

namespace PolderKnowledge\EntityServiceTest\Validator;

use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use PolderKnowledge\EntityService\EntityServiceInterface;
use PolderKnowledge\EntityService\Validator\EntityNotExists;

/**
 * EntityNotExists validator test case
 */
class EntityNotExistsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var EntityNotExists
     */
    protected $fixture;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $entityServiceMock;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->entityServiceMock = $this->getMock(EntityServiceInterface::class);

        $this->fixture = new EntityNotExists();
        $this->fixture->setEntityService($this->entityServiceMock);
    }

    /**
     * @covers PolderKnowledge\EntityService\Validator\EntityNotExists::isValid
     * @covers PolderKnowledge\EntityService\Validator\EntityNotExists::setField
     * @covers PolderKnowledge\EntityService\Validator\EntityNotExists::setMethod
     * @covers PolderKnowledge\EntityService\Validator\EntityNotExists::setEntityService
     * @covers PolderKnowledge\EntityService\Validator\EntityNotExists::fetchResult
     *
     * @dataProvider optionsDataProvider
     */
    public function testIsValidForConfiguration($options, $expected)
    {
        // Arrange
        $this->entityServiceMock
            ->expects($this->once())
            ->method($expected['method'])
            ->with(array($expected['field'] => 'foo'))
            ->willReturn(array());

        $this->fixture->setOptions($options);

        // Act
        $result = $this->fixture->isValid('foo');

        // Assert
        $this->assertTrue($result);
    }

    /**
     * Dataprovider for isValid testcase
     *
     * @return array
     */
    public function optionsDataProvider()
    {
        return array(
            array(
                'options' => array(),
                'expected' => array(
                    'method' => 'findBy',
                    'field' => 'id',
                ),
            ),
            array(
                'options' => array(
                    'method' => 'find'
                ),
                'expected' => array(
                    'method' => 'find',
                    'field' => 'id',
                ),
            )
        );
    }

    /**
     * @covers PolderKnowledge\EntityService\Validator\EntityNotExists::isValid
     * @covers PolderKnowledge\EntityService\Validator\EntityNotExists::fetchResult
     */
    public function testIsValidReturnsFalseWithNonEmptyResult()
    {
        // Arrange
        $this->entityServiceMock
            ->expects($this->once())
            ->method('findBy')
            ->with(array('id' => 'foo'))
            ->willReturn(array('entry'));

        // Act
        $result = $this->fixture->isValid('foo');

        // Assert
        $this->assertFalse($result);
        $this->assertEquals(array(
            'objectFound' => 'Object matching \'foo\' was found'
        ), $this->fixture->getMessages());
    }
}
