<?php

declare(strict_types=1);

namespace App\Infrastructure\ORM\Reporting\Repository;

use App\Application\Doctrine\Repository\CRUDRepository;
use App\Domain\Reporting\Dictionary\LogJournalActionDictionary;
use App\Domain\Reporting\Dictionary\LogJournalSubjectDictionary;
use App\Domain\Reporting\Model;
use App\Domain\Reporting\Model\LoggableSubject;
use App\Domain\Reporting\Repository;
use App\Domain\User\Model\Collectivity;
use Doctrine\ORM\QueryBuilder;
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

    public function updateDeletedLog(LoggableSubject $subject)
    {
        $qb = $this->getManager()->createQueryBuilder();
        $qb
            ->update($this->getModelClass(), 'o')
            ->set('o.isDeleted', ':true')
            ->andWhere($qb->expr()->eq('o.subjectId', ':uuid'))
            ->setParameters(
                [
                    'true' => true,
                    'uuid' => $subject->getId()->toString(),
                ]
            )
        ;

        $qb->getQuery()->execute();
    }

    public function findPaginated($firstResult, $maxResults, $orderColumn, $orderDir, $searches, $criteria = [])
    {
        $query = $this->createQueryBuilder()
            ->addSelect('collectivite')
            ->leftJoin('o.collectivity', 'collectivite')
        ;

        $this->addOrder($query, $orderColumn, $orderDir);
        $this->addSearches($query, $searches);

        $query = $query->getQuery();
        $query->setFirstResult($firstResult);
        $query->setMaxResults($maxResults);

        return new Paginator($query);
    }

    public function count(array $criteria = [])
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
            case 'subjectId':
                $queryBuilder->addOrderBy('o.subjectId', $orderDir);
                break;
            case 'userFullName':
                $queryBuilder->addOrderBy('o.userFullName', $orderDir);
                break;
            case 'userEmail':
                $queryBuilder->addOrderBy('o.userEmail', $orderDir);
                break;
            case 'collectivite':
                $queryBuilder
                    ->addOrderBy('collectivite.name', $orderDir);
                break;
            case 'date':
                $queryBuilder->addOrderBy('o.date', $orderDir);
                break;
            case 'subject':
                $queryBuilder->addSelect('(case 
                WHEN o.subjectType = \'' . LogJournalSubjectDictionary::REGISTRY_MESUREMENT . '\' THEN 1 
                WHEN o.subjectType = \'' . LogJournalSubjectDictionary::REGISTRY_CONFORMITE_TRAITEMENT . '\' THEN 2 
                WHEN o.subjectType = \'' . LogJournalSubjectDictionary::USER_COLLECTIVITY . '\' THEN 3  
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
            case 'subjectName':
                $queryBuilder->addOrderBy('o.subjectName', $orderDir);
                break;
        }
    }

    private function addSearches(QueryBuilder $queryBuilder, array $searches)
    {
        foreach ($searches as $columnName => $search) {
            switch ($columnName) {
                case 'subjectId':
                    $queryBuilder->andWhere('o.subjectId LIKE :id')
                        ->setParameter('id', '%' . $search . '%');
                    break;
                case 'userFullName':
                    $queryBuilder->andWhere('o.userFullName LIKE :name')
                        ->setParameter('name', '%' . $search . '%');
                    break;
                case 'userEmail':
                    $queryBuilder->andWhere('o.userEmail LIKE :email')
                        ->setParameter('email', '%' . $search . '%');
                    break;
                case 'collectivite':
                    $queryBuilder->andWhere('collectivite.name LIKE :collectivite')
                        ->setParameter('collectivite', '%' . $search . '%');
                    break;
                case 'date':
                    $queryBuilder->andWhere('o.date BETWEEN :start_date AND :finish_date')
                        ->setParameter('start_date', date_create_from_format('d/m/y', substr($search, 0, 8))->format('Y-m-d 00:00:00'))
                        ->setParameter('finish_date', date_create_from_format('d/m/y', substr($search, 11, 8))->format('Y-m-d 23:59:59'));
                    break;
                case 'subject':
                    $queryBuilder->andWhere('o.subjectType = :subject')
                    ->setParameter('subject', $search);
                    break;
                case 'action':
                    $queryBuilder->andWhere('o.action = :action')
                        ->setParameter('action', $search);
                    break;
                case 'subjectName':
                    $queryBuilder->andWhere('o.subjectName LIKE :subjectName')
                        ->setParameter('subjectName', '%' . $search . '%');
                    break;
            }
        }
    }

    public function findAllByCollectivityWithoutSubjects(Collectivity $collectivity, $limit, array $subjects = [])
    {
        $qb = $this->createQueryBuilder();
        $qb->andWhere($qb->expr()->eq('o.collectivity', ':collectivity'))
            ->andWhere($qb->expr()->notIn('o.subjectType', ':subjects'))
            ->setParameters([
                'collectivity' => $collectivity,
                'subjects'     => $subjects,
            ])
            ->addOrderBy('o.date', 'DESC')
            ->setMaxResults($limit)
        ;

        return $qb->getQuery()->getResult();
    }

    public function deleteAllAnteriorToDate(\DateTime $date)
    {
        $query = $this->getManager()->createQuery("DELETE FROM App\Domain\Reporting\Model\LogJournal o WHERE o.date < :dateP")
            ->setParameter('dateP', $date)
        ;

        return $query->execute();
    }
}
