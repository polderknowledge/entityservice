<?php
/**
 * Polder Knowledge / Entity Service (http://polderknowledge.nl)
 *
 * @link http://developers.polderknowledge.nl/gitlab/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2015-2015 Polder Knowledge (http://www.polderknowledge.nl)
 * @license http://polderknowledge.nl/license/proprietary proprietary
 */

namespace PolderKnowledge\EntityServiceTest\Service;

use PHPUnit_Framework_TestCase;
use PolderKnowledge\EntityService\Service\EntityRepositoryManager;
use PolderKnowledge\EntityService\Service\EntityServiceManager;
use stdClass;

class EntityServiceManagerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var EntityRepositoryManager
     */
    protected $manager;

    public function setUp()
    {
        $this->manager = new EntityServiceManager();
    }

    /**
     * @expectedException PolderKnowledge\EntityService\Exception\InvalidServiceNameException
     * @expectedExceptionMessage Plugin of type stdClass is invalid;
     * must implement PolderKnowledge\EntityService\Service\EntityServiceInterface
     */
    public function testManagerThrowsExceptionOnInvalidClass()
    {
        // Arrange
        // ...

        // Act
        $this->manager->validatePlugin(new stdClass());

        // Assert
        // ... asserting happens because of the expectedException annotation
    }

    public function testManagerHasAbstractFactory()
    {
        // Arrange
        // ...

        // Act
        // ...

        // Assert
        $this->assertTrue($this->manager->canCreateFromAbstractFactory(
            'polderKnowledgeentityservicetestserviceassetcustomentitymock',
            'PolderKnowledge\\EntityServiceTest\\Service\\_Asset\\CustomEntityMock'
        ));
    }
}
