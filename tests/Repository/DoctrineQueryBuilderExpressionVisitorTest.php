<?php
/**
 * Polder Knowledge / Entity Service (http://polderknowledge.nl)
 *
 * @link http://developers.polderknowledge.nl/gitlab/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2015-2015 Polder Knowledge (http://www.polderknowledge.nl)
 * @license http://polderknowledge.nl/license/proprietary proprietary
 */

namespace PolderKnowledge\EntityServiceTest\Repository;

use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\ORM\Query\Expr\Comparison as OrmComparison;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use PHPUnit_Framework_TestCase;
use PolderKnowledge\EntityService\Repository\DoctrineQueryBuilderExpressionVisitor;
use Youngguns\StdLib\Criterion\Expression\ExpressionInterface;

class DoctrineQueryBuilderExpressionVisitorTest extends PHPUnit_Framework_TestCase
{
    const ROOT_ALIAS = 'fake';

    /**
     * @var DoctrineQueryBuilderExpressionVisitor
     */
    private $fixture;

    protected function setUp()
    {
        $queryBuilder = $this->getMock(QueryBuilder::class, array('expr'), array(), '', false);
        $queryBuilder->expects($this->any())->method('expr')->willReturn(new Expr());

        $this->fixture = new DoctrineQueryBuilderExpressionVisitor(static::ROOT_ALIAS, $queryBuilder);
    }

    /**
     * @param string $field
     * @param string $operator
     * @param string $value
     * @param string $expectedOperator
     * @dataProvider comparisonProvider
     */
    public function testWalkComparison($field, $operator, $value, $expectedOperator)
    {
        $input = new Comparison($field, $operator, $value);

        $result = $this->fixture->walkComparison($input);

        $this->assertEquals(static::ROOT_ALIAS . '.' . $field, $result->getLeftExpr());
        $this->assertEquals($expectedOperator, $result->getOperator());
        $this->assertEquals(':' . $field, $result->getRightExpr());

        $params = $this->fixture->getParameters();

        $this->assertEquals($field, $params[0]->getName());
        $this->assertEquals($value, $params[0]->getValue());
    }

    /**
     * provides test parameters
     *
     * array(
     *  //field
     *  //operator
     *  //value
     *  //expectedOperator
     * )
     *
     * @return array
     */
    public function comparisonProvider()
    {
        return array(
            array(
                'field',
                Comparison::EQ,
                'bar',
                OrmComparison::EQ
            ),
            array(
                'field',
                Comparison::IS,
                'bar',
                OrmComparison::EQ
            ),
            array(
                'field',
                Comparison::NEQ,
                'bar',
                OrmComparison::NEQ
            ),
            array(
                'field',
                Comparison::GT,
                'bar',
                OrmComparison::GT,
            ),
            array(
                'field',
                Comparison::GTE,
                'bar',
                OrmComparison::GTE,
            ),
            array(
                'field',
                Comparison::LT,
                'bar',
                OrmComparison::LT,
            ),
            array(
                'field',
                Comparison::LTE,
                'bar',
                OrmComparison::LTE,
            ),
        );
    }

    /**
     * @param $field
     * @param $operator
     * @param $value
     * @param $expectedOperator
     * @param $expectedValue
     * @dataProvider likeDataprovider
     * @dataProvider inDataProvider
     */
    public function testLikeExpression($field, $operator, $value, $expectedOperator, $expectedValue)
    {
        $input = new Comparison($field, $operator, $value);

        $result = $this->fixture->walkComparison($input);
        $params = $this->fixture->getParameters();

        $expectedField = sprintf('LOWER(%s.%s)', static::ROOT_ALIAS, $field);

        $this->assertEquals($expectedField, $result->getLeftExpr());
        $this->assertEquals($expectedOperator, $result->getOperator());
        $this->assertEquals(':' . $field, $result->getRightExpr());
        $this->assertEquals($field, $params[0]->getName());
        $this->assertEquals($expectedValue, $params[0]->getValue());
    }

    public function likeDataprovider()
    {
        return array(
            array(
                'field',
                Comparison::CONTAINS,
                'Bar',
                'LIKE',
                '%bar%',
            ),
            array(
                'field',
                ExpressionInterface::NOT_CONTAINS,
                'Bar',
                'NOT LIKE',
                '%bar%',
            ),
            array(
                'field',
                ExpressionInterface::STARTS_WITH,
                'Bar',
                'LIKE',
                'bar%',
            ),
            array(
                'field',
                ExpressionInterface::ENDS_WITH,
                'Bar',
                'LIKE',
                '%bar',
            ),
        );
    }

    public function testInExpression()
    {
        $field = 'field';
        $value =  array('bar', 'foo');

        $input = new Comparison($field, Comparison::IN, $value);

        $result = $this->fixture->walkComparison($input);
        $params = $this->fixture->getParameters();

        $expectedResult = sprintf('%s.%s IN(:%s)', static::ROOT_ALIAS, $field, $field);

        $this->assertEquals($expectedResult, $result->__toString());
        $this->assertEquals($field, $params[0]->getName());
        $this->assertEquals($value, $params[0]->getValue());
    }

    public function testNotInExpression()
    {
        $field = 'field';
        $value =  array('bar', 'foo');

        $input = new Comparison($field, Comparison::NIN, $value);

        $result = $this->fixture->walkComparison($input);
        $params = $this->fixture->getParameters();

        $expectedResult = sprintf('%s.%s NOT IN(:%s)', static::ROOT_ALIAS, $field, $field);

        $this->assertEquals($expectedResult, $result->__toString());
        $this->assertEquals($field, $params[0]->getName());
        $this->assertEquals($value, $params[0]->getValue());
    }

    public function testIsNullExpression()
    {
        $field = 'field';
        $input = new Comparison($field, Comparison::IS, null);
        $expectedResult = sprintf('%s.%s IS NULL', static::ROOT_ALIAS, $field, $field);

        $result = $this->fixture->walkComparison($input);

        $this->assertEquals($expectedResult, $result);
    }

    public function testIsNotNullExpression()
    {
        $field = 'field';
        $input = new Comparison($field, Comparison::NEQ, null);
        $expectedResult = sprintf('%s.%s IS NOT NULL', static::ROOT_ALIAS, $field, $field);

        $result = $this->fixture->walkComparison($input);

        $this->assertEquals($expectedResult, $result);
    }

    public function testMultipleExpressionsSameField()
    {
        $field = 'field';
        $operator = Comparison::IS;
        $value1 = 'value';

        $input = new Comparison($field, $operator, $value1);
        $this->fixture->walkComparison($input);
        $this->fixture->walkComparison($input);

        $params = $this->fixture->getParameters();
        $paramNames = array();
        foreach ($params as $param) {
            $paramNames[] = $param->getName();
        }

        $this->assertEquals(array('field', 'field_1'), $paramNames);
    }
}
