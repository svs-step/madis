<?php

namespace App\Infrastructure\ORM\Registry\Repository\ConformiteOrganisation;

use App\Application\Doctrine\Repository\CRUDRepository;
use App\Domain\Registry\Model;
use App\Domain\Registry\Repository;
use App\Domain\User\Model\Collectivity;

class Evaluation extends CRUDRepository implements Repository\ConformiteOrganisation\Evaluation
{
    protected function getModelClass(): string
    {
        return Model\ConformiteOrganisation\Evaluation::class;
    }

    public function findAllByOrganisationOrderedByDate(Collectivity $organisation = null)
    {
        $qBuilder = $this
            ->createQueryBuilder()
        ;
        if (null !== $organisation) {
            $qBuilder
                ->andWhere('o.collectivity = :organisation_id')
                ->setParameter('organisation_id', $organisation->getId());
        } else {
            $qBuilder
                ->addSelect('c')
                ->leftJoin('o.collectivity', 'c');
        }

        $qBuilder
            ->addSelect('conformites')
            ->leftJoin('o.conformites', 'conformites')
            ->orderBy('o.date', 'DESC')
            ;

        return $qBuilder
            ->getQuery()
            ->getResult()
        ;
    }

    public function findLastByOrganisation(Collectivity $organisation): ?Model\ConformiteOrganisation\Evaluation
    {
        $results =  $this->createQueryBuilder()
            ->addSelect('conformites, processus, reponses, questions, actionProtections')
            ->andWhere('o.collectivity = :organisation')
            ->setParameter('organisation', $organisation)
            ->andWhere('o.isDraft = 0')
            ->leftJoin('o.conformites', 'conformites')
            ->leftJoin('conformites.processus', 'processus')
            ->leftJoin('conformites.reponses', 'reponses')
            ->leftJoin('conformites.actionProtections', 'actionProtections')
            ->leftJoin('processus.questions', 'questions')
            ->orderBy('o.date', 'DESC')
            ->getQuery()
            ->getResult()
        ;

        return isset($results[0]) ? $results[0] : null;
    }
}
