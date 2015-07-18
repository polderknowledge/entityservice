<?php
/**
 * Polder Knowledge / Entity Service (http://polderknowledge.nl)
 *
 * @link http://developers.polderknowledge.nl/gitlab/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2015-2015 Polder Knowledge (http://www.polderknowledge.nl)
 * @license http://polderknowledge.nl/license/proprietary proprietary
 */

namespace PolderKnowledge\EntityServiceTest\Paginator\Adapter;

use PHPUnit_Framework_TestCase;
use PolderKnowledge\EntityService\Paginator\Adapter\EntityService;

class EntityServiceTest extends PHPUnit_Framework_TestCase
{
    public function testCount()
    {
        // Arrange
        $mock = $this->getMockForAbstractClass('PolderKnowledge\\EntityService\\EntityServiceInterface');
        $mock->expects($this->once())->method('countBy')->willReturn(123);
        $paginator = new EntityService($mock);

        // Act
        $result = $paginator->count();

        // Assert
        $this->assertEquals(123, $result);
    }

    public function testGetItems()
    {
        // Arrange
        $criteria = $this->getMock('Doctrine\\Common\\Collections\\Criteria');
        $criteria->expects($this->once())->method('setFirstResult')->with(10);
        $criteria->expects($this->once())->method('setMaxResults')->with(20);

        $entityService = $this->getMockForAbstractClass('PolderKnowledge\\EntityService\\EntityServiceInterface');
        $entityService->expects($this->once())->method('findBy')->with($criteria)->willReturn(array());

        $paginator = new EntityService($entityService, $criteria);

        // Act
        $result = $paginator->getItems(10, 20);

        // Assert
        $this->assertEquals(array(), $result);
    }
}
