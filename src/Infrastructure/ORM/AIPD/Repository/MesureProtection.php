<?php

declare(strict_types=1);

namespace App\Infrastructure\ORM\AIPD\Repository;

use App\Application\Doctrine\Repository\CRUDRepository;
use App\Application\Traits\RepositoryUtils;
use App\Domain\AIPD\Model;
use App\Domain\AIPD\Repository;
use Doctrine\ORM\QueryBuilder;
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
        $qb = $this->createQueryBuilder();
        $qb->addSelect('o.id');

        foreach ($criteria as $key => $value) {
            $this->addWhereClause($qb, $key, $value);
        }

        $this->addTableOrder($qb, $orderColumn, $orderDir);
        $this->addTableSearches($qb, $searches);

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
            case 'nomCourt':
                $queryBuilder->addOrderBy('o.nomCourt', $orderDir);
                break;
            case 'detail':
                $queryBuilder->addOrderBy('o.detail', $orderDir);
                break;
            case 'poidsVraisemblance':
                $queryBuilder->addOrderBy('o.poidsVraisemblance', $orderDir);
                break;
            case 'poidsGravite':
                $queryBuilder->addOrderBy('o.poidsGravite', $orderDir);
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
                case 'nomCourt':
                    $this->addWhereClause($queryBuilder, 'nomCourt', '%' . $search . '%', 'LIKE');
                    break;
                case 'detail':
                    $this->addWhereClause($queryBuilder, 'detail', '%' . $search . '%', 'LIKE');
                    break;
                case 'poidsVraisemblance':
                    $this->addWhereClause($queryBuilder, 'poidsVraisemblance', '%' . $search . '%', 'LIKE');
                    break;
                case 'poidsGravite':
                    $this->addWhereClause($queryBuilder, 'poidsGravite', '%' . $search . '%', 'LIKE');
                    break;
            }
        }
    }
}
