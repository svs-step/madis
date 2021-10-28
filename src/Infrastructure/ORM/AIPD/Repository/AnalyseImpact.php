<?php

declare(strict_types=1);

namespace App\Infrastructure\ORM\AIPD\Repository;

use App\Application\Doctrine\Repository\CRUDRepository;
use App\Application\Traits\RepositoryUtils;
use App\Domain\AIPD\Model as Model;
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
            ->leftJoin('o.avisReferent', 'avisRepresentant')
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
}
