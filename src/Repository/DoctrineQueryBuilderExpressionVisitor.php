<?php
/**
 * Polder Knowledge / Entity Service (http://polderknowledge.nl)
 *
 * @link http://developers.polderknowledge.nl/gitlab/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2015-2015 Polder Knowledge (http://www.polderknowledge.nl)
 * @license http://polderknowledge.nl/license/proprietary proprietary
 */

namespace PolderKnowledge\EntityService\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Expr\CompositeExpression;
use Doctrine\Common\Collections\Expr\Expression;
use Doctrine\Common\Collections\Expr\ExpressionVisitor;
use Doctrine\Common\Collections\Expr\Value;
use Doctrine\ORM\Query\Expr\Andx;
use Doctrine\ORM\Query\Expr\Comparison as OrmComparison;
use Doctrine\ORM\Query\Expr\Orx;
use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\QueryBuilder;
use RuntimeException;
use Youngguns\StdLib\Criterion\DateTimeExpressionVisitorInterface;
use Youngguns\StdLib\Criterion\Expression\DateTimeExpression;
use Youngguns\StdLib\Criterion\Expression\ExpressionInterface;
use Youngguns\StdLib\Criterion\Expression\MultiParameterExpression;
use Youngguns\StdLib\Criterion\MultiParameterExpressionVisitorInterface;

/**
 * The DoctrineQuerybuilderExpressionVisitor class is able to build a query expression.
 */
class DoctrineQueryBuilderExpressionVisitor extends ExpressionVisitor implements
    MultiParameterExpressionVisitorInterface,
    DateTimeExpressionVisitorInterface
{
    /**
     * A mapping between Doctrine Common operators and Doctrine ORM operators.
     *
     * @var array
     */
    private static $operatorMap = array(
        Comparison::GT => OrmComparison::GT,
        Comparison::GTE => OrmComparison::GTE,
        Comparison::LT => OrmComparison::LT,
        Comparison::LTE => OrmComparison::LTE
    );

    /**
     * The name of the alias for the query. Usually a single letter.
     *
     * @var string
     */
    protected $rootAlias;

    /**
     * The query builder used to walk through the expression.
     *
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * A list with parameters that is built by walking through the expression.
     *
     * @var array
     */
    protected $parameters = array();

    /**
     * Initializes a new instance of this class.
     *
     * @param string $rootAlias
     * @param QueryBuilder $queryBuilder
     */
    public function __construct($rootAlias, QueryBuilder $queryBuilder)
    {
        $this->rootAlias = $rootAlias;
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * Gets bound parameters.
     * Filled after {@link dispach()}.
     *
     * @return Collection
     */
    public function getParameters()
    {
        return new ArrayCollection($this->parameters);
    }

    /**
     * Clears parameters.
     *
     * @return void
     */
    public function clearParameters()
    {
        $this->parameters = array();
    }

    /**
     * Converts Criteria expression to Query one based on static map.
     *
     * @param string $criteriaOperator
     *
     * @return string|null
     */
    private static function convertComparisonOperator($criteriaOperator)
    {
        return isset(self::$operatorMap[$criteriaOperator]) ? self::$operatorMap[$criteriaOperator] : null;
    }

    /**
     * {@inheritDoc}
     */
    public function walkCompositeExpression(CompositeExpression $expr)
    {
        $expressionList = array();

        foreach ($expr->getExpressionList() as $child) {
            $expressionList[] = $this->dispatch($child);
        }

        switch ($expr->getType()) {
            case CompositeExpression::TYPE_AND:
                return new Andx($expressionList);

            case CompositeExpression::TYPE_OR:
                return new Orx($expressionList);

            default:
                throw new RuntimeException("Unknown composite " . $expr->getType());
        }
    }

    /**
     *
     * {@inheritDoc}
     *
     */
    public function walkComparison(Comparison $comparison)
    {
        $parameterName = $this->getParameterName($comparison->getField());
        $parameter = new Parameter($parameterName, $this->walkValue($comparison->getValue()));
        $placeholder = ':' . $parameterName;

        switch ($comparison->getOperator()) {
            case Comparison::IN:
                $this->parameters[] = $parameter;

                return $this->queryBuilder->expr()->in($this->rootAlias . '.' . $comparison->getField(), $placeholder);

            case Comparison::NIN:
                $this->parameters[] = $parameter;

                return $this->queryBuilder->expr()->notIn($this->rootAlias . '.' . $comparison->getField(), $placeholder);

            case Comparison::EQ:
            case Comparison::IS:
                if ($this->walkValue($comparison->getValue()) === null) {
                    return $this->queryBuilder->expr()->isNull($this->rootAlias . '.' . $comparison->getField());
                }
                $this->parameters[] = $parameter;

                return $this->queryBuilder->expr()->eq($this->rootAlias . '.' . $comparison->getField(), $placeholder);

            case Comparison::NEQ:
                if ($this->walkValue($comparison->getValue()) === null) {
                    return $this->queryBuilder->expr()->isNotNull($this->rootAlias . '.' . $comparison->getField());
                }
                $this->parameters[] = $parameter;

                return $this->queryBuilder->expr()->neq($this->rootAlias . '.' . $comparison->getField(), $placeholder);

            case Comparison::CONTAINS:
            case ExpressionInterface::NOT_CONTAINS:
            case ExpressionInterface::STARTS_WITH:
            case ExpressionInterface::ENDS_WITH:
                $fieldExpression = sprintf('LOWER(%s.%s)', $this->rootAlias, $comparison->getField());
                //fall through
            case Comparison::CONTAINS:
                $parameter->setValue('%' . strtolower($parameter->getValue()) . '%', $parameter->getType());
                $this->parameters[] = $parameter;

                return $this->queryBuilder->expr()->like($fieldExpression, $placeholder);

            case ExpressionInterface::NOT_CONTAINS:
                $parameter->setValue('%' . strtolower($parameter->getValue()) . '%', $parameter->getType());
                $this->parameters[] = $parameter;

                return $this->queryBuilder->expr()->notLike($fieldExpression, $placeholder);

            case ExpressionInterface::STARTS_WITH:
                $parameter->setValue(strtolower($parameter->getValue()) . '%', $parameter->getType());
                $this->parameters[] = $parameter;

                return $this->queryBuilder->expr()->like($fieldExpression, $placeholder);

            case ExpressionInterface::ENDS_WITH:
                $parameter->setValue('%' . strtolower($parameter->getValue()), $parameter->getType());
                $this->parameters[] = $parameter;

                return $this->queryBuilder->expr()->like($fieldExpression, $placeholder);

            default:
                $operator = self::convertComparisonOperator($comparison->getOperator());
                if ($operator) {
                    $this->parameters[] = $parameter;

                    return new OrmComparison(
                        $this->rootAlias . '.' . $comparison->getField(), $operator, $placeholder
                    );
                }

                throw new RuntimeException("Unknown comparison operator: " . $comparison->getOperator());
        }
    }

    public function walkMultiParamExpression(MultiParameterExpression $expression)
    {
        $placeholders = $this->setParameters($expression);

        switch ($expression->getOperator()) {
            case MultiParameterExpression::BETWEEN:
                return $this->queryBuilder->expr()->between($this->rootAlias . '.' . $expression->getField(), $placeholders[0], $placeholders[1]);
            default:
                throw new RuntimeException("Unknown MultiParameterExpression operator: " . $expression->getOperator());
        }
    }

    public function walkDateTimeExpression(DateTimeExpression $expression)
    {
        $placeholders = $this->setParameters($expression);

        switch ($expression->getOperator()) {
            case DateTimeExpression::CURRENT_MONTH:
            case DateTimeExpression::PREVIOUS_MONTH:
            case DateTimeExpression::CURRENT_YEAR:
            case DateTimeExpression::PREVIOUS_YEAR:
                return $this->queryBuilder->expr()->between($this->rootAlias . '.' . $expression->getField(), $placeholders[0], $placeholders[1]);
            default:
                throw new RuntimeException("Unknown DateTimeExpression operator: " . $expression->getOperator());
        }
    }

    protected function setParameters($expression)
    {
        $placeholders = array();

        foreach ($expression->getValues() as $value) {
                $parameterName = $this->getParameterName($expression->getField ());
                $parameter = new Parameter($parameterName, $this->walkValue($value));
                $placeholders[] = ':' . $parameterName;
                $this->parameters[] = $parameter;
        }

        return $placeholders;
    }

    /**
     * {@inheritDoc}
     */
    public function walkValue(Value $value)
    {
        return $value->getValue();
    }

    public function dispatch(Expression $expr)
    {
        switch (true) {
            case ($expr instanceof MultiParameterExpression):
                return $this->walkMultiParamExpression($expr);
            case ($expr instanceof DateTimeExpression):
                return $this->walkDateTimeExpression($expr);
            default:
                return parent::dispatch($expr);
        }
    }

    protected function getParameterName($fieldName)
    {
        $parameterName = str_replace('.', '_', $fieldName);

        foreach ($this->parameters as $parameter) {
            if ($parameter->getName() === $parameterName) {
                return $parameterName .= '_' . count($this->parameters);
            }
        }

        return $parameterName;
    }
}
