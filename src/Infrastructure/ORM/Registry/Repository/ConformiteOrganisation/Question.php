<?php

namespace App\Infrastructure\ORM\Registry\Repository\ConformiteOrganisation;

use App\Application\Doctrine\Repository\CRUDRepository;
use App\Domain\Registry\Model\ConformiteOrganisation as Model;
use App\Domain\Registry\Repository;

class Question extends CRUDRepository implements Repository\ConformiteOrganisation\Question
{
    protected function getModelClass(): string
    {
        return Model\Question::class;
    }

    public function findAllByProcessus(Model\Processus $processus)
    {
        return $this->createQueryBuilder()
            ->orderBy('o.processus')
            ->andWhere('o.processus = :processus')
            ->setParameter('processus', $processus->getId())
            ->addOrderBy('o.position', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}
