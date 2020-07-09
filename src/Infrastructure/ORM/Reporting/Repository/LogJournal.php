<?php

declare(strict_types=1);

namespace App\Infrastructure\ORM\Reporting\Repository;

use App\Application\Doctrine\Repository\CRUDRepository;
use App\Domain\Reporting\Dictionary\LogJournalActionDictionary;
use App\Domain\Reporting\Dictionary\LogJournalSubjectDictionary;
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
    public function findPaginated($firstResult, $maxResults, $orderColumn, $orderDir)
    {
        $query = $this->createQueryBuilder()
            ->addSelect('subject', 'user', 'collectivite')
            ->leftJoin('o.subject', 'subject')
            ->leftJoin('o.user', 'user')
            ->leftJoin('o.collectivity', 'collectivite')
        ;

        $this->addOrder($query, $orderColumn, $orderDir);

        $query = $query->getQuery();
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

    private function addOrder(&$queryBuilder, $orderColumn, $orderDir)
    {
        switch ($orderColumn) {
            case 'date':
                $queryBuilder->addOrderBy('o.date', $orderDir);
                break;
            case 'collectivite':
                $queryBuilder
                    ->addOrderBy('collectivite.name', $orderDir);
                break;
            case 'subject':
                $queryBuilder->addSelect('(case 
                WHEN o.subjectType = \'' . LogJournalSubjectDictionary::REGISTRY_MESUREMENT . '\' THEN 1 
                WHEN o.subjectType = \'' . LogJournalSubjectDictionary::REGISTRY_CONFORMITE_TRAITEMENT . '\' THEN 2 
                WHEN o.subjectType = \'' . LogJournalSubjectDictionary::REGISTRY_COLLECTIVITY . '\' THEN 3  
                WHEN o.subjectType = \'' . LogJournalSubjectDictionary::REGISTRY_REQUEST . '\' THEN 4 
                WHEN o.subjectType = \'' . LogJournalSubjectDictionary::USER_EMAIL . '\' THEN 5 
                WHEN o.subjectType = \'' . LogJournalSubjectDictionary::REGISTRY_CONFORMITE_ORGANISATION_EVALUATION . '\' THEN 6
                WHEN o.subjectType = \'' . LogJournalSubjectDictionary::MATURITY_SURVEY . '\' THEN 7
                WHEN o.subjectType = \'' . LogJournalSubjectDictionary::USER_PASSWORD . '\' THEN 8
                WHEN o.subjectType = \'' . LogJournalSubjectDictionary::USER_LASTNAME . '\' THEN 9
                WHEN o.subjectType = \'' . LogJournalSubjectDictionary::USER_FIRSTNAME . '\' THEN 10
                WHEN o.subjectType = \'' . LogJournalSubjectDictionary::REGISTRY_PROOF . '\' THEN 11
                WHEN o.subjectType = \'' . LogJournalSubjectDictionary::REGISTRY_CONTRACTOR . '\' THEN 12
                WHEN o.subjectType = \'' . LogJournalSubjectDictionary::REGISTRY_TREATMENT . '\' THEN 13
                WHEN o.subjectType = \'' . LogJournalSubjectDictionary::USER_USER . '\' THEN 14
                WHEN o.subjectType = \'' . LogJournalSubjectDictionary::REGISTRY_VIOLATION . '\' THEN 15
                ELSE 16 END) AS HIDDEN subject_order')
                    ->addOrderBy('subject_order', $orderDir);
                break;
            case 'action':
                $queryBuilder->addSelect('(case when o.action = \'' . LogJournalActionDictionary::LOGIN . '\' THEN 1 WHEN o.action = \'' . LogJournalActionDictionary::CREATE . '\' THEN 2 WHEN o.action = \'' . LogJournalActionDictionary::UPDATE . '\' THEN 3 ELSE 4 END) AS HIDDEN action_order')
                    ->addOrderBy('action_order', $orderDir);
                break;
            case 'subjectId':
                $queryBuilder->addOrderBy('o.lastKnownName', $orderDir);
                break;
            case 'utilisateur':
                $queryBuilder->addOrderBy('user.firstName', $orderDir)
                ->addOrderBy('user.lastName', $orderDir);
                break;
        }
    }
}