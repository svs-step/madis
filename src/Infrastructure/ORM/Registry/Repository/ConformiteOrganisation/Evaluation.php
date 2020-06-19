<?php

namespace App\Infrastructure\ORM\Registry\Repository\ConformiteOrganisation;

use App\Application\Doctrine\Repository\CRUDRepository;
use App\Domain\Registry\Model;
use App\Domain\Registry\Repository;

class Evaluation extends CRUDRepository implements Repository\ConformiteOrganisation\Evaluation
{
    protected function getModelClass(): string
    {
        return Model\ConformiteOrganisation\Evaluation::class;
    }

    public function findAllByOrganisationOrderedByDate(string $idOrganisation = null)
    {
        $qBuilder = $this
            ->createQueryBuilder()
        ;
        if (null !== $idOrganisation) {
            $qBuilder
                ->andWhere('o.collectivity = :organisation_id')
                ->setParameter('organisation_id', $idOrganisation);
        } else {
            $qBuilder
                ->addSelect('c')
                ->leftJoin('o.collectivity', 'c');
        }

        $qBuilder
            ->addSelect('conformites')
            ->leftJoin('o.conformites', 'conformites')
            ->orderBy('o.date')
            ;

        return $qBuilder
            ->getQuery()
            ->getResult()
        ;
    }
}
