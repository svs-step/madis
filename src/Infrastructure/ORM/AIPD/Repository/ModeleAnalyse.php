<?php

declare(strict_types=1);

namespace App\Infrastructure\ORM\AIPD\Repository;

use App\Application\Doctrine\Repository\CRUDRepository;
use App\Application\Traits\RepositoryUtils;
use App\Domain\AIPD\Model;
use App\Domain\AIPD\Repository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

class ModeleAnalyse extends CRUDRepository implements Repository\ModeleAnalyse
{
    use RepositoryUtils;

    protected function getModelClass(): string
    {
        return Model\ModeleAnalyse::class;
    }

    public function count(array $criteria = [])
    {
        $qb = $this->createQueryBuilder();

        $qb->select('COUNT(o.id)');

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function findPaginated($firstResult, $maxResults, $orderColumn, $orderDir, $searches, $criteria = [])
    {
        $qb = $this->createQueryBuilder()
        ->addSelect('c')
        ->leftJoin('o.authorizedCollectivities', 'c');

        $this->addTableSearches($qb, $searches);

        $qb = $qb->getQuery();
        $qb->setFirstResult($firstResult);
        $qb->setMaxResults($maxResults);

        return new Paginator($qb);
    }

    private function addTableSearches(QueryBuilder $queryBuilder, $searches)
    {
        foreach ($searches as $columnName => $search) {
            switch ($columnName) {
                case 'nom':
                    $this->addWhereClause($queryBuilder, 'nom', '%' . $search . '%', 'LIKE');
                    break;
            }
        }
    }
}
