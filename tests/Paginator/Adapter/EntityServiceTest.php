<?php

namespace PolderKnowledge\EntityServiceTest\Paginator\Adapter;

use PolderKnowledge\EntityService\Paginator\Adapter\EntityService;
use PolderKnowledge\EntityService\Service\EntityServiceInterface;
use PolderKnowledge\EntityService\ServiceResult;

class EntityServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | EntityServiceInterface
     */
    private $entityServiceMock;

    /**
     * @var EntityService
     */
    private $fixture;

    protected function setUp()
    {
        $this->entityServiceMock = $this->getMock(EntityServiceInterface::class);
        $this->fixture = new EntityService($this->entityServiceMock);
    }

    public function testCountCallsMock()
    {
        $result = new ServiceResult();
        $result->initialize(array(10));
        $this->entityServiceMock->expects($this->once())
            ->method('countBy')
            ->willReturn($result);

        $actual = $this->fixture->count();

        $this->assertEquals(10, $actual);
    }

    public function testGetItemsCallsMock()
    {
        $result = new ServiceResult();

        $this->entityServiceMock->expects($this->once())
            ->method('findBy')
            ->with( array(), null, 10, 10)
            ->willReturn($result);

        $actual = $this->fixture->getItems(10, 10);

        $this->assertSame($result, $actual);
    }
}
