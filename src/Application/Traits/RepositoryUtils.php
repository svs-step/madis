<?php

namespace App\Application\Traits;

use Doctrine\ORM\QueryBuilder;

trait RepositoryUtils
{
    /**
     * Add a where clause to query.
     *
     * @param mixed $value
     */
    protected function addWhereClause(QueryBuilder $qb, string $key, $value, $operator = '='): QueryBuilder
    {
        return $qb
            ->andWhere("o.{$key} $operator :{$key}_value")
            ->setParameter("{$key}_value", $value)
            ;
    }
}
