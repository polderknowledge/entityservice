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
use PolderKnowledge\EntityService\EntityServiceInterface;
use PolderKnowledge\EntityService\Service\EntityServiceManager;
use PolderKnowledge\EntityServiceTestAsset\MyEntity;
use stdClass;

class EntityServiceManagerTest extends PHPUnit_Framework_TestCase
{
    public function testManagerHasAbstractFactory()
    {
        // Arrange
        $entityServiceManager = new EntityServiceManager();

        // Act
        // ...

        // Assert
        $this->assertTrue($entityServiceManager->canCreateFromAbstractFactory(
            'polderKnowledgeentityservicetestserviceassetcustomentitymock',
            MyEntity::class
        ));
    }

    public function testValidatePlugin()
    {
        // Arrange
        $entityServiceManager = new EntityServiceManager();
        $plugin = $this->getMockForAbstractClass(EntityServiceInterface::class);

        // Act
        $entityServiceManager->validatePlugin($plugin);

        // Assert
        /// ...
    }

    /**
     * @expectedException PolderKnowledge\EntityService\Exception\InvalidServiceNameException
     * @expectedExceptionMessage Plugin of type stdClass is invalid; must implement
     * PolderKnowledge\EntityService\EntityServiceInterface
     */
    public function testValidatePluginWithInvalidPlugin()
    {
        // Arrange
        $entityServiceManager = new EntityServiceManager();
        $plugin = new stdClass();

        // Act
        $entityServiceManager->validatePlugin($plugin);

        // Assert
        /// ...
    }
}
