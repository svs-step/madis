<?php

declare(strict_types=1);

namespace App\Infrastructure\ORM\AIPD\Repository;

use App\Application\Doctrine\Repository\CRUDRepository;
use App\Domain\AIPD\Model as Model;
use Doctrine\ORM\Tools\Pagination\Paginator;

class AnalyseImpact extends CRUDRepository implements \App\Domain\AIPD\Repository\AnalyseImpact
{
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

        $qb = $qb->getQuery();
        $qb->setFirstResult($firstResult);
        $qb->setMaxResults($maxResults);

        return new Paginator($this->createQueryBuilder());
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
            ->andWhere('o.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
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
