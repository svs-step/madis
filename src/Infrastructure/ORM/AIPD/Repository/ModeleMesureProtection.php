<?php

declare(strict_types=1);

namespace App\Infrastructure\ORM\AIPD\Repository;

use App\Application\Doctrine\Repository\CRUDRepository;
use App\Application\Traits\RepositoryUtils;
use App\Domain\AIPD\Model;
use App\Domain\AIPD\Repository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

class ModeleMesureProtection extends CRUDRepository implements Repository\ModeleMesureProtection
{
    use RepositoryUtils;

    protected function getModelClass(): string
    {
        return Model\ModeleMesureProtection::class;
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
            case 'updatedAt':
                $queryBuilder->addOrderBy('o.updatedAt', $orderDir);
                break;
            case 'createdAt':
                $queryBuilder->addOrderBy('o.createdAt', $orderDir);
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
                case 'createdAt':
                    $queryBuilder->andWhere('o.createdAt BETWEEN :created_start_date AND :created_finish_date')
                        ->setParameter('created_start_date', date_create_from_format('d/m/y', substr($search, 0, 8))->format('Y-m-d 00:00:00'))
                        ->setParameter('created_finish_date', date_create_from_format('d/m/y', substr($search, 11, 8))->format('Y-m-d 23:59:59'));
                    break;
                case 'updatedAt':
                    $queryBuilder->andWhere('o.updatedAt BETWEEN :updated_start_date AND :updated_finish_date')
                        ->setParameter('updated_start_date', date_create_from_format('d/m/y', substr($search, 0, 8))->format('Y-m-d 00:00:00'))
                        ->setParameter('updated_finish_date', date_create_from_format('d/m/y', substr($search, 11, 8))->format('Y-m-d 23:59:59'));
                    break;
            }
        }
    }

    public function findToDelete(Model\ModeleAnalyse $modele)
    {
        $qb    = $this->createQueryBuilder();
        $smIds = [];
        foreach ($modele->getScenarioMenaces() as $sm) {
            $smIds[] = $sm->getId()->toString();
        }

        // get measure ids that are joined to another ModeleScenarionMenace
        $qb->select('o.id')->join('o.scenariosMenaces', 'sm', 'sm.modele_analyse_id = o.id')
            ->where('sm.id NOT IN (:ids)')
            ->setParameter('ids', $smIds)
        ;
        // Keep only measure ids
        $toNotDelete = array_map(function ($r) {return $r['id']->toString(); }, $qb->getQuery()->getResult());

        // Get measures to delete:
        // They are in the scenarioMenaces for the current ModeleAnalyse
        // And are not part of the measures linked to other scenarios.
        $toDelete = $this->createQueryBuilder()
            ->select('o')
            ->join('o.scenariosMenaces', 'sm', 'sm.modele_analyse_id = o.id')
            ->where('sm.id IN (:ids)')
            ->andWhere('o.id NOT IN (:mesureids)')
            ->setParameter('ids', $smIds)
            ->setParameter('mesureids', $toNotDelete)
        ;

        return $toDelete->getQuery()->getResult();
    }
}
