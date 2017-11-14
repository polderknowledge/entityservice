<?php

namespace PolderKnowledge\EntityServiceTest\Repository;

use Doctrine\Common\Collections\Criteria;
use PolderKnowledge\EntityService\Repository\Util;

class UtilTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateCriteriaFromArray()
    {
        $expected = Criteria::create()
            ->andWhere(Criteria::expr()->eq('username', 'piet'))
            ->andWhere(Criteria::expr()->eq('age', 24));

        self::assertEquals($expected, Util::createCriteriaFromArray(['username' => 'piet', 'age' => 24]));
        self::assertNotEquals($expected, Util::createCriteriaFromArray(['username' => 'joep', 'age' => 24]));
    }
}
