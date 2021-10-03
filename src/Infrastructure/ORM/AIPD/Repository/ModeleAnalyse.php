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
        $this->addTableOrder($qb, $orderColumn, $orderDir);

        $qb = $qb->getQuery();
        $qb->setFirstResult($firstResult);
        $qb->setMaxResults($maxResults);

        return new Paginator($qb);
    }

    private function addTableOrder(QueryBuilder $queryBuilder, $orderColumn, $orderDir)
    {
        switch ($orderColumn) {
            case 'nom':
                $queryBuilder->addOrderBy('o.nom', $orderDir);
                break;
            case 'description':
                $queryBuilder->addOrderBy('o.description', $orderDir);
                break;
            case 'updatedAt':
                $queryBuilder->addOrderBy('o.updatedAt', $orderDir);
                break;
        }
    }

    private function addTableSearches(QueryBuilder $queryBuilder, $searches)
    {
        foreach ($searches as $columnName => $search) {
            switch ($columnName) {
                case 'nom':
                    $this->addWhereClause($queryBuilder, 'nom', '%' . $search . '%', 'LIKE');
                    break;
                case 'description':
                    $this->addWhereClause($queryBuilder, 'description', '%' . $search . '%', 'LIKE');
                    break;
                 case 'updatedAt':
                    $queryBuilder->andWhere('o.updatedAt LIKE :date')
                        ->setParameter('date', date_create_from_format('d/m/Y', $search)->format('Y-m-d') . '%');
                    break;
            }
        }
    }
}
