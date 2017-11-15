<?php

namespace PolderKnowledge\EntityService\Repository;

use Doctrine\Common\Collections\Criteria;

final class Util
{
    public static function normalizeCriteria($criteria): Criteria
    {
        if ($criteria instanceof Criteria) {
            return $criteria;
        } elseif (is_array($criteria)) {
            return self::createCriteriaFromArray($criteria);
        }

        throw new \InvalidArgumentException(
            'criteria must be a Criteria object or an array, found '
            . (is_object($criteria) ? get_class($criteria) : gettype($criteria))
        );
    }

    public static function createCriteriaFromArray(array $params): Criteria
    {
        $criteria = Criteria::create();
        foreach ($params as $name => $value) {
            $criteria->andWhere(Criteria::expr()->eq($name, $value));
        }

        return $criteria;
    }
}
