<?php

declare(strict_types=1);

namespace App\Infrastructure\ORM\AIPD\Repository;

use App\Application\Doctrine\Repository\CRUDRepository;
use App\Application\Traits\RepositoryUtils;
use App\Domain\AIPD\Model;
use App\Domain\AIPD\Repository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class MesureProtection extends CRUDRepository implements Repository\MesureProtection
{
    use RepositoryUtils;

    protected function getModelClass(): string
    {
        return Model\MesureProtection::class;
    }

    public function count(array $criteria = [])
    {
        $qb = $this->createQueryBuilder();

        $qb->select('COUNT(o.id)');

        foreach ($criteria as $key => $value) {
            $this->addWhereClause($qb, $key, $value);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function findPaginated($firstResult, $maxResults, $orderColumn, $orderDir, $searches, $criteria = [])
    {
        return new Paginator($this->createQueryBuilder());
//        $qb = $this->createQueryBuilder();
//        ////
////        if (isset($criteria['collectivity']) && $criteria['collectivity'] instanceof Collection) {
////            $this->addInClauseCollectivities($qb, $criteria['collectivity']->toArray());
////            unset($criteria['collectivity']);
////        }
//        ////
////        foreach ($criteria as $key => $value) {
////            $this->addWhereClause($qb, $key, $value);
////        }
//        ////
//        ////        $this->addTableOrder($qb, $orderColumn, $orderDir);
//        //////        $this->addTableSearches($qb, $searches);
//        ////
//        $qb = $qb->getQuery();
//        $qb->setFirstResult($firstResult);
//        $qb->setMaxResults($maxResults);
//
//        ////
//        return new Paginator($qb);
    }
}
