<?php
/**
 * Polder Knowledge / Entity Service (http://polderknowledge.nl)
 *
 * @link http://developers.polderknowledge.nl/gitlab/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2015-2015 Polder Knowledge (http://www.polderknowledge.nl)
 * @license http://polderknowledge.nl/license/proprietary proprietary
 */

namespace PolderKnowledge\EntityServiceTest\Repository\Doctrine;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Expr\CompositeExpression;
use Doctrine\Common\Collections\Expr\Value;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Andx;
use Doctrine\ORM\Query\Expr\Comparison as OrmComparison;
use Doctrine\ORM\Query\Expr\Orx;
use Doctrine\ORM\QueryBuilder;
use PHPUnit_Framework_TestCase;
use PolderKnowledge\EntityService\Repository\Doctrine\QueryBuilderExpressionVisitor;
use RuntimeException;
use Youngguns\StdLib\Criterion\Expression\DateTimeExpression;
use Youngguns\StdLib\Criterion\Expression\ExpressionInterface;
use Youngguns\StdLib\Criterion\Expression\MultiParameterExpression;

class QueryBuilderExpressionVisitorTest extends PHPUnit_Framework_TestCase
{
    private $queryBuilder;
    private $expressionBuilder;

    protected function setUp()
    {
        parent::setUp();

        $this->expressionBuilder = $this->getMock(Expr::class);

        $queryBuilder = $this->getMockBuilder(QueryBuilder::class);
        $queryBuilder->disableOriginalConstructor();
        $this->queryBuilder = $queryBuilder->getMock();
        $this->queryBuilder->expects($this->any())->method('expr')->willReturn($this->expressionBuilder);
    }

    public function testGetParameters()
    {
        // Arrange
        $visitor = new QueryBuilderExpressionVisitor('v', $this->queryBuilder);

        // Act
        $result = $visitor->getParameters();

        // Assert
        $this->assertInstanceOf(ArrayCollection::class, $result);
    }

    public function testClearParameters()
    {
        // Arrange
        $visitor = new QueryBuilderExpressionVisitor('v', $this->queryBuilder);

        // Act
        $visitor->clearParameters();
        $result = $visitor->getParameters();

        // Assert
        $this->assertInstanceOf(ArrayCollection::class, $result);
        $this->assertCount(0, $result);
    }

    public function testWalkCompositeExpressionWithAndType()
    {
        // Arrange
        $visitor = new QueryBuilderExpressionVisitor('v', $this->queryBuilder);
        $expression = new CompositeExpression(CompositeExpression::TYPE_AND, array());

        // Act
        $result = $visitor->walkCompositeExpression($expression);

        // Assert
        $this->assertInstanceOf(Andx::class, $result);
    }

    public function testWalkCompositeExpressionWithOrType()
    {
        // Arrange
        $visitor = new QueryBuilderExpressionVisitor('v', $this->queryBuilder);
        $expression = new CompositeExpression(CompositeExpression::TYPE_OR, array());

        // Act
        $result = $visitor->walkCompositeExpression($expression);

        // Assert
        $this->assertInstanceOf(Orx::class, $result);
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Unknown composite test
     */
    public function testWalkCompositeExpressionWithInvalidType()
    {
        // Arrange
        $visitor = new QueryBuilderExpressionVisitor('v', $this->queryBuilder);
        $expression = new CompositeExpression('test', array());

        // Act
        $result = $visitor->walkCompositeExpression($expression);

        // Assert
        // ...
    }

    public function testWalkCompositeExpressionWithExpressions()
    {
        // Arrange
        $visitor = new QueryBuilderExpressionVisitor('v', $this->queryBuilder);
        $expression = new CompositeExpression(CompositeExpression::TYPE_AND, array(
            new CompositeExpression(CompositeExpression::TYPE_AND, array()),
        ));

        // Act
        $result = $visitor->walkCompositeExpression($expression);

        // Assert
        $this->assertInstanceOf(Andx::class, $result);
    }

    /**
     * @dataProvider comparisonDataProvider
     */
    public function testWalkComparison($lft, $operator, $rgt, $method)
    {
        // Arrange
        $visitor = new QueryBuilderExpressionVisitor('v', $this->queryBuilder);
        $comparison = new Comparison($lft, $operator, $rgt);
        $this->expressionBuilder->expects($this->once())->method($method);

        // Act
        $visitor->walkComparison($comparison);

        // Assert
        // ...
    }

    public function comparisonDataProvider()
    {
        return array(
            array(
                'lft' => '',
                'operator' => Comparison::CONTAINS,
                'rgt' => '',
                'method' => 'like',
            ),
            array(
                'lft' => '',
                'operator' => Comparison::EQ,
                'rgt' => '',
                'method' => 'eq',
            ),
            array(
                'lft' => '',
                'operator' => Comparison::EQ,
                'rgt' => null,
                'method' => 'isNull',
            ),
            array(
                'lft' => '',
                'operator' => Comparison::IN,
                'rgt' => '',
                'method' => 'in',
            ),
            array(
                'lft' => '',
                'operator' => Comparison::IS,
                'rgt' => '',
                'method' => 'eq',
            ),
            array(
                'lft' => '',
                'operator' => Comparison::IS,
                'rgt' => null,
                'method' => 'isNull',
            ),
            array(
                'lft' => '',
                'operator' => Comparison::NEQ,
                'rgt' => '',
                'method' => 'neq',
            ),
            array(
                'lft' => '',
                'operator' => Comparison::NEQ,
                'rgt' => null,
                'method' => 'isNotNull',
            ),
            array(
                'lft' => '',
                'operator' => Comparison::NIN,
                'rgt' => '',
                'method' => 'notIn',
            ),
            array(
                'lft' => '',
                'operator' => ExpressionInterface::NOT_CONTAINS,
                'rgt' => '',
                'method' => 'notLike',
            ),
            array(
                'lft' => '',
                'operator' => ExpressionInterface::STARTS_WITH,
                'rgt' => '',
                'method' => 'like',
            ),
            array(
                'lft' => '',
                'operator' => ExpressionInterface::ENDS_WITH,
                'rgt' => '',
                'method' => 'like',
            ),
        );
    }

    public function testWalkComparisonWithDefaultOperator()
    {
        // Arrange
        $visitor = new QueryBuilderExpressionVisitor('v', $this->queryBuilder);
        $comparison = new Comparison('', '<', '');

        // Act
        $result = $visitor->walkComparison($comparison);

        // Assert
        $this->assertInstanceOf(OrmComparison::class, $result);
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Unknown comparison operator: invalid
     */
    public function testWalkComparisonWithInvalidOperaotr()
    {
        // Arrange
        $visitor = new QueryBuilderExpressionVisitor('v', $this->queryBuilder);
        $comparison = new Comparison('', 'invalid', '');

        // Act
        $visitor->walkComparison($comparison);

        // Assert
        // ...
    }

    /**
     * @dataProvider multiParamExpressionDataProvider
     */
    public function testWalkMultiParamExpression($lft, $operator, $rgt, $method)
    {
        // Arrange
        $visitor = new QueryBuilderExpressionVisitor('v', $this->queryBuilder);
        $expression = new MultiParameterExpression($lft, $operator, $rgt);
        $this->expressionBuilder->expects($this->once())->method($method);

        // Act
        $visitor->walkMultiParamExpression($expression);

        // Assert
        // ...
    }

    public function multiParamExpressionDataProvider()
    {
        return array(
            array(
                'lft' => '',
                'operator' => MultiParameterExpression::BETWEEN,
                'rgt' => array(1, 2, 3),
                'method' => 'between',
            ),
        );
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Unknown MultiParameterExpression operator: NOT_BETWEEN
     */
    public function testWalkMultiParamExpressionWithInvalidOperator()
    {
        // Arrange
        $visitor = new QueryBuilderExpressionVisitor('v', $this->queryBuilder);
        $expression = new MultiParameterExpression('', MultiParameterExpression::NOT_BETWEEN, array(1, 2, 3));

        // Act
        $visitor->walkMultiParamExpression($expression);

        // Assert
        // ...
    }

    /**
     * @dataProvider dateTimeExpressionDataProvider
     */
    public function testWalkDateTimeExpression($operator, $method)
    {
        // Arrange
        $visitor = new QueryBuilderExpressionVisitor('v', $this->queryBuilder);
        $expression = new DateTimeExpression('field', $operator);
        $this->expressionBuilder->expects($this->once())->method($method);

        // Act
        $visitor->walkDateTimeExpression($expression);

        // Assert
        // ...
    }

    public function dateTimeExpressionDataProvider()
    {
        return array(
            array(
                'operator' => DateTimeExpression::CURRENT_MONTH,
                'method' => 'between',
            ),
            array(
                'operator' => DateTimeExpression::CURRENT_YEAR,
                'method' => 'between',
            ),
            array(
                'operator' => DateTimeExpression::PREVIOUS_MONTH,
                'method' => 'between',
            ),
            array(
                'operator' => DateTimeExpression::PREVIOUS_YEAR,
                'method' => 'between',
            ),
        );
    }

    public function testWalkValue()
    {
        // Arrange
        $visitor = new QueryBuilderExpressionVisitor('v', $this->queryBuilder);
        $value = new Value('value');

        // Act
        $result = $visitor->walkValue($value);

        // Assert
        $this->assertEquals('value', $result);
    }

    public function testDispatchWithMultiParameterExpression()
    {
        // Arrange
        $visitor = new QueryBuilderExpressionVisitor('v', $this->queryBuilder);
        $expression = new MultiParameterExpression('', MultiParameterExpression::BETWEEN, array(1, 2, 3));

        // Act
        $result = $visitor->dispatch($expression);

        // Assert
        $this->assertNull($result);
    }

    public function testDispatchWithDateTimeExpression()
    {
        // Arrange
        $visitor = new QueryBuilderExpressionVisitor('v', $this->queryBuilder);
        $expression = new DateTimeExpression('', DateTimeExpression::CURRENT_MONTH);

        // Act
        $result = $visitor->dispatch($expression);

        // Assert
        $this->assertNull($result);
    }
}
