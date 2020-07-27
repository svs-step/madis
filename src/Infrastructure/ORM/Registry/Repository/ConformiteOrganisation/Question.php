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
            ->andWhere('o.processus = :processus')
            ->setParameter('processus', $processus->getId())
            ->addOrderBy('o.position', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findNewNotUsedByGivenConformite(Model\Conformite $conformite)
    {
        $qb = $this->createQueryBuilder();
        $qb->andWhere('o.processus = :processus');
        $qb->setParameter('processus', $conformite->getProcessus());
        if (!empty($conformite->getReponses())) {
            $qb->andWhere($qb->expr()->notIn('o.id', ':questions'))
                ->setParameter(
                    'questions',
                    array_map(function (Model\Reponse $reponse) {
                        return $reponse->getQuestion()->getId()->toString();
                    }, \iterable_to_array($conformite->getReponses()))
                )
            ;
        }

        return $qb
            ->getQuery()
            ->getResult()
            ;
    }
}
