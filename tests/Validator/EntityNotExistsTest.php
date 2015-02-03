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
use PolderKnowledge\EntityService\ServiceResult;
use PolderKnowledge\EntityService\Validator\EntityNotExists;

/**
 * EntityNotExists validator test case
 */
class EntityNotExistsTest extends PHPUnit_Framework_TestCase
{
    /**
     *
     * @var
     */
    protected $fixture;

    /**
     *
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $entityServiceMock;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->fixture = new EntityNotExists();
        $this->entityServiceMock = $this->getMock('\PolderKnowledge\EntityService\Service\EntityServiceInterface');
        $this->fixture->setEntityService($this->entityServiceMock);
    }

    /**
     *
     * @covers PolderKnowledge\EntityService\Validator\EntityNotExists::isValid
     * @covers PolderKnowledge\EntityService\Validator\EntityNotExists::setField
     * @covers PolderKnowledge\EntityService\Validator\EntityNotExists::setMethod
     * @covers PolderKnowledge\EntityService\Validator\EntityNotExists::setEntityService
     *
     * @dataProvider optionsDataProvider
     */
    public function testIsValidForConfiguration($options, $expected)
    {
        $this->initializeMock(array(), $expected);
        $this->fixture->setOptions($options);
        $this->assertTrue($this->fixture->isValid('foo'));
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
     */
    public function testIsValidReturnsFalseWithNonEmptyResult()
    {
        $this->initializeMock(
            array(new \stdClass()),
            array(
                'method' => 'findBy',
                'field' => 'id',
            )
        );
        $this->assertFalse($this->fixture->isValid('foo'));
        $this->assertEquals(
            array('objectFound' => 'Object matching \'foo\' was found'),
            $this->fixture->getMessages()
        );
    }

    /**
     * Initializes the entityServiceMock to expect
     * a method call with a succesfull return
     *
     * @param array $resultData
     * @param array $expected
     */
    protected function initializeMock(array $resultData, array $expected)
    {
        $result = new ServiceResult();
        $result->initialize($resultData);

        $this->entityServiceMock
            ->expects($this->once())
            ->method($expected['method'])
            ->with(array($expected['field'] => 'foo'))
            ->willReturn($result);
    }
}
