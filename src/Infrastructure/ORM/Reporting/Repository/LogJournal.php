<?php

declare(strict_types=1);

namespace App\Infrastructure\ORM\Reporting\Repository;

use App\Application\Doctrine\Repository\CRUDRepository;
use App\Domain\Reporting\Model;
use App\Domain\Reporting\Model\LoggableSubject;
use App\Domain\Reporting\Repository;

class LogJournal extends CRUDRepository implements Repository\LogJournal
{
    /**
     * {@inheritdoc}
     */
    protected function getModelClass(): string
    {
        return Model\LogJournal::class;
    }

    public function updateSubjectIdWithGivenUuid(\App\Domain\Reporting\Model\LogJournal $logJournal, Model\LoggableSubject $subject)
    {
        $qb = $this->getManager()->createQueryBuilder();
        $qb
            ->update($this->getModelClass(), 'o')
            ->set('o.subject', ':uuid')
            ->andWhere($qb->expr()->eq('o.id', ':logJournal'))
            ->setParameters(
                [
                    'uuid'       => $subject->getId()->toString(),
                    'logJournal' => $logJournal,
                ]
            )
        ;

        $qb->getQuery()->execute();
    }

    public function updateLastKnownNameEntriesForGivenSubject(LoggableSubject $subject)
    {
        $qb = $this->getManager()->createQueryBuilder();
        $qb
            ->update($this->getModelClass(), 'o')
            ->set('o.lastKnownName', ':lastName')
            ->andWhere($qb->expr()->eq('o.subject', ':uuid'))
            ->setParameters(
                [
                    'lastName' => $subject->__toString(),
                    'uuid'     => $subject->getId()->toString(),
                ]
            )
        ;

        $qb->getQuery()->execute();
    }
}
