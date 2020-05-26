<?php

namespace App\Infrastructure\ORM\Registry\Repository\ConformiteOrganisation;

use App\Application\Doctrine\Repository\CRUDRepository;
use App\Domain\Registry\Model\ConformiteOrganisation as Model;
use App\Domain\Registry\Repository\ConformiteOrganisation as Repository;

class Processus extends CRUDRepository implements Repository\Processus
{
    protected function getModelClass(): string
    {
        return Model\Processus::class;
    }

    public function findAllWithQuestions()
    {
        return $this->getManager()
            ->createQueryBuilder()
            ->select('p')
            ->addSelect('q')
            ->from($this->getModelClass(), 'p')
            ->orderBy('p.position')
            ->leftJoin('p.questions', 'q')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findOneByPosition(int $position)
    {
        return $this->getManager()
            ->createQueryBuilder()
            ->select('p')
            ->from($this->getModelClass(), 'p')
            ->andWhere('p.position = :position')
            ->setParameter('position', $position)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}
