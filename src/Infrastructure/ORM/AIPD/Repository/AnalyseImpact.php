<?php

declare(strict_types=1);

namespace App\Infrastructure\ORM\AIPD\Repository;

use App\Application\Doctrine\Repository\CRUDRepository;
use App\Application\Traits\RepositoryUtils;
use App\Domain\AIPD\Model;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

class AnalyseImpact extends CRUDRepository implements \App\Domain\AIPD\Repository\AnalyseImpact
{
    use RepositoryUtils;

    protected function getModelClass(): string
    {
        return Model\AnalyseImpact::class;
    }

    public function count(array $criteria = [])
    {
        $qb = $this->createQueryBuilder();

        $qb->select('COUNT(o.id)');

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function findPaginated($firstResult, $maxResults, $orderColumn, $orderDir, $searches, $criteria = [])
    {
        $qb = $this->createQueryBuilder();

        $qb
            ->leftJoin('o.conformiteTraitement', 'conformiteTraitement')
            ->addSelect('conformiteTraitement')
            ->leftJoin('conformiteTraitement.traitement', 'traitement')
            ->addSelect('traitement')
            ->leftJoin('traitement.collectivity', 'collectivity')
            ->addSelect('collectivity')
            ->leftJoin('o.avisReferent', 'avisReferent')
            ->addSelect('avisReferent')
            ->leftJoin('o.avisDpd', 'avisDpd')
            ->addSelect('avisDpd')
            ->leftJoin('o.avisRepresentant', 'avisRepresentant')
            ->addSelect('avisRepresentant')
            ->leftJoin('o.avisResponsable', 'avisResponsable')
            ->addSelect('avisResponsable')
        ;

        if (\array_key_exists('collectivity', $criteria)) {
            $qb
                ->andWhere('traitement.collectivity = :collectivity')
                ->setParameter('collectivity', $criteria['collectivity'])
            ;
            unset($criteria['collectivity']);
        }

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

    public function findOneById(string $id)
    {
        return $this->createQueryBuilder()
            ->leftJoin('o.criterePrincipeFondamentaux', 'c')
            ->addSelect('c')
            ->leftJoin('o.questionConformites', 'q')
            ->addSelect('q')
            ->leftJoin('o.scenarioMenaces', 's')
            ->addSelect('s')
            ->leftJoin('o.avisReferent', 'avisReferent')
            ->addSelect('avisReferent')
            ->leftJoin('o.avisDpd', 'avisDpd')
            ->addSelect('avisDpd')
            ->leftJoin('o.avisRepresentant', 'avisRepresentant')
            ->addSelect('avisRepresentant')
            ->leftJoin('o.avisResponsable', 'avisResponsable')
            ->addSelect('avisResponsable')
            ->andWhere('o.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $qb;
    }

    public function findOneByIdWithoutInvisibleScenarios(string $id)
    {
        return $this->createQueryBuilder()
            ->leftJoin('o.criterePrincipeFondamentaux', 'c', 'WITH', 'c.isVisible = 1')
            ->addSelect('c')
            ->leftJoin('o.questionConformites', 'q')
            ->addSelect('q')
            ->leftJoin('o.scenarioMenaces', 's', 'WITH', 's.isVisible = 1')
            ->addSelect('s')
            ->andWhere('o.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    private function addTableSearches(QueryBuilder $queryBuilder, $searches)
    {
        foreach ($searches as $columnName => $search) {
            switch ($columnName) {
                case 'traitement':
                    $queryBuilder->andWhere('traitement.name LIKE :traitement_name')
                        ->setParameter('traitement_name', '%' . $search . '%');
                    break;
                case 'dateDeCreation':
                    $queryBuilder->andWhere('o.createdAt BETWEEN :created_start_date AND :created_finish_date')
                        ->setParameter('created_start_date', date_create_from_format('d/m/y', substr($search, 0, 8))->format('Y-m-d 00:00:00'))
                        ->setParameter('created_finish_date', date_create_from_format('d/m/y', substr($search, 11, 8))->format('Y-m-d 23:59:59'));
                    break;
                case 'dateDeValidation':
                    $queryBuilder->andWhere('o.dateValidation BETWEEN :validation_start_date AND :validation_finish_date')
                        ->setParameter('validation_start_date', date_create_from_format('d/m/y', substr($search, 0, 8))->format('Y-m-d 00:00:00'))
                        ->setParameter('validation_finish_date', date_create_from_format('d/m/y', substr($search, 11, 8))->format('Y-m-d 23:59:59'));
                    break;
                case 'modele':
                    $this->addWhereClause($queryBuilder, 'modeleAnalyse', '%' . $search . '%', 'LIKE');
                    break;
            }
        }
    }

    private function addTableOrder(QueryBuilder $queryBuilder, $orderColumn, $orderDir)
    {
        switch ($orderColumn) {
            case 'traitement':
                $queryBuilder->addOrderBy('traitement.name', $orderDir);
                break;
            case 'dateDeCreation':
                $queryBuilder->addOrderBy('o.createdAt', $orderDir);
                break;
            case 'dateDeValidation':
                $queryBuilder->addOrderBy('o.dateValidation', $orderDir);
                break;
            case 'modele':
                $queryBuilder->addOrderBy('o.modeleAnalyse', $orderDir);
                break;
            case 'collectivite':
                $queryBuilder->addOrderBy('collectivity.name', $orderDir);
                break;
            case 'avisReferent':
                $queryBuilder->addOrderBy('avisReferent.reponse', $orderDir);
                break;
            case 'avisDpd':
                $queryBuilder->addOrderBy('avisDpd.reponse', $orderDir);
                break;
            case 'avisRepresentant':
                $queryBuilder->addOrderBy('avisRepresentant.reponse', $orderDir);
                break;
            case 'avisResponsable':
                $queryBuilder->addOrderBy('avisResponsable.reponse', $orderDir);
                break;
        }
    }
}
