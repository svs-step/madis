<?php

declare(strict_types=1);

namespace App\Infrastructure\ORM\Reporting\Repository;

use App\Application\Doctrine\Repository\CRUDRepository;
use App\Domain\Reporting\Model;
use App\Domain\Reporting\Model\LoggableSubject;
use App\Domain\Reporting\Repository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class LogJournal extends CRUDRepository implements Repository\LogJournal
{
    /**
     * {@inheritdoc}
     */
    protected function getModelClass(): string
    {
        return Model\LogJournal::class;
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
                    'lastName' => $subject->__toString() . ' - ' . $subject->getId()->toString(),
                    'uuid'     => $subject->getId()->toString(),
                ]
            )
        ;

        $qb->getQuery()->execute();
    }

    // TODO Implements order & filter
    public function findPaginated($firstResult, $maxResults)
    {
        $query = $this->createQueryBuilder()
            ->orderBy('o.date', 'DESC')
            ->getQuery();

        $query->setFirstResult($firstResult);
        $query->setMaxResults($maxResults);

        $paginator = new Paginator($query);

        return $paginator;
    }

    public function countLogs()
    {
        return $this
            ->createQueryBuilder()
            ->select('count(o.id)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
