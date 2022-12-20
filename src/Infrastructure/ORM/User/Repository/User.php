<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2019 Soluris - Solutions Numériques Territoriales Innovantes
 * @author Donovan Bourlard <donovan@awkan.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace App\Infrastructure\ORM\User\Repository;

use App\Application\Doctrine\Repository\CRUDRepository;
use App\Application\Traits\RepositoryUtils;
use App\Domain\User\Dictionary\UserRoleDictionary;
use App\Domain\User\Model;
use App\Domain\User\Repository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

class User extends CRUDRepository implements Repository\User
{
    use RepositoryUtils;

    /**
     * {@inheritdoc}
     */
    protected function getModelClass(): string
    {
        return Model\User::class;
    }

    /**
     * Add archive clause to query.
     */
    protected function addArchivedClause(QueryBuilder $qb, bool $archived = false): QueryBuilder
    {
        // Get not archived
        if (!$archived) {
            return $qb->andWhere('o.deletedAt is null');
        }

        // Get archived
        return $qb->andWhere('o.deletedAt is not null');
    }

    /**
     * Add order to query.
     */
    protected function addOrder(QueryBuilder $qb, array $order = []): QueryBuilder
    {
        foreach ($order as $key => $dir) {
            $qb->addOrderBy("o.{$key}", $dir);
        }

        return $qb;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneOrNullByEmail(string $email): ?Model\User
    {
        return $this->createQueryBuilder()
            ->andWhere('o.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneOrNullByForgetPasswordToken(string $token): ?Model\User
    {
        return $this->createQueryBuilder()
            ->andWhere('o.forgetPasswordToken = :forgetPasswordToken')
            ->setParameter('forgetPasswordToken', $token)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneOrNullLastLoginUserByCollectivity(Model\Collectivity $collectivity): ?Model\User
    {
        $qb = $this->createQueryBuilder();

        $qb->andWhere($qb->expr()->eq('o.collectivity', ':collectivity'));
        $qb->andWhere($qb->expr()->isNotNull('o.lastLogin'));
        $qb->setParameter('collectivity', $collectivity);
        $qb->addOrderBy('o.lastLogin', 'DESC');
        $qb->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function count(array $criteria = [])
    {
        $qb = $this
            ->createQueryBuilder()
            ->select('count(o.id)')
        ;

        if (\array_key_exists('archive', $criteria)) {
            $this->addArchivedClause($qb, $criteria['archive']);
            unset($criteria['archive']);
        }
        if (\array_key_exists('collectivitesReferees', $criteria)) {
            $qb->leftJoin('o.collectivity', 'collectivite')
                ->andWhere($qb->expr()->in('collectivite.id', ':collectivitesReferees'))
                ->setParameter('collectivitesReferees', $criteria['collectivitesReferees'])
                ->andWhere('JSON_UNQUOTE(JSON_EXTRACT(o.roles, \'$[0]\')) <> :role_admin')
                ->setParameter('role_admin', UserRoleDictionary::ROLE_ADMIN);
            unset($criteria['collectivitesReferees']);
        }

        foreach ($criteria as $key => $value) {
            $this->addWhereClause($qb, $key, $value);
        }

        return $qb
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function findPaginated($firstResult, $maxResults, $orderColumn, $orderDir, $searches, $criteria = [])
    {
        $qb = $this->createQueryBuilder()
            ->addSelect('collectivite')
            ->leftJoin('o.collectivity', 'collectivite')
            ->leftJoin('o.services', 'services');

        if (\array_key_exists('archive', $criteria)) {
            $this->addArchivedClause($qb, $criteria['archive']);
            unset($criteria['archive']);
        }
        if (\array_key_exists('collectivitesReferees', $criteria)) {
            $qb->andWhere($qb->expr()->in('collectivite.id', ':collectivitesReferees'))
                ->setParameter('collectivitesReferees', $criteria['collectivitesReferees'])
                ->andWhere('JSON_UNQUOTE(JSON_EXTRACT(o.roles, \'$[0]\')) <> :role_admin')
                ->setParameter('role_admin', UserRoleDictionary::ROLE_ADMIN);
            unset($criteria['collectivitesReferees']);
        }

        foreach ($criteria as $key => $value) {
            $this->addWhereClause($qb, $key, $value);
        }

        $this->addTableOrder($qb, $orderColumn, $orderDir);
        $this->addTableWhere($qb, $searches);

        $query = $qb->getQuery();
        $query->setFirstResult($firstResult);
        $query->setMaxResults($maxResults);

        return new Paginator($query);
    }

    private function addTableOrder(QueryBuilder $queryBuilder, $orderColumn, $orderDir)
    {
        switch ($orderColumn) {
            case 'prenom':
                $queryBuilder->addOrderBy('o.firstName', $orderDir);
                break;
            case 'nom':
                $queryBuilder->addOrderBy('o.lastName', $orderDir);
                break;
            case 'email':
                $queryBuilder->addOrderBy('o.email', $orderDir);
                break;
            case 'collectivite':
                $queryBuilder->addOrderBy('collectivite.name', $orderDir);
                break;
            case 'roles':
                $queryBuilder->addSelect('
                CASE
                    WHEN JSON_UNQUOTE(JSON_EXTRACT(o.roles, \'$[0]\')) = :role_admin THEN 1
                    WHEN JSON_UNQUOTE(JSON_EXTRACT(o.roles, \'$[0]\')) = :role_user THEN 2
                    WHEN JSON_UNQUOTE(JSON_EXTRACT(o.roles, \'$[0]\')) = :role_preview THEN 3
                    ELSE 4
                END as HIDDEN json_role');
                $queryBuilder->addOrderBy('json_role', $orderDir);
                $queryBuilder->setParameters(
                    [
                        'role_admin'   => UserRoleDictionary::ROLE_ADMIN,
                        'role_user'    => UserRoleDictionary::ROLE_USER,
                        'role_preview' => UserRoleDictionary::ROLE_PREVIEW,
                    ]
                );
                break;
            case 'connexion':
                $queryBuilder->addOrderBy('o.lastLogin', $orderDir);
                break;
        }
    }

    private function addTableWhere(QueryBuilder $queryBuilder, $searches)
    {
        foreach ($searches as $columnName => $search) {
            switch ($columnName) {
                case 'prenom':
                    $this->addWhereClause($queryBuilder, 'firstName', '%' . $search . '%', 'LIKE');
                    break;
                case 'nom':
                    $this->addWhereClause($queryBuilder, 'lastName', '%' . $search . '%', 'LIKE');
                    break;
                case 'email':
                    $this->addWhereClause($queryBuilder, 'email', '%' . $search . '%', 'LIKE');
                    break;
                case 'collectivite':
                    $queryBuilder->andWhere('collectivite.name LIKE :collectivite_name')
                        ->setParameter('collectivite_name', '%' . $search . '%');
                    break;
                case 'roles':
                    $queryBuilder->andWhere('o.roles LIKE :role')
                        ->setParameter('role', sprintf('"%s"', '%' . $search . '%'));
                    break;
                case 'connexion':
                    $queryBuilder->andWhere('o.lastLogin LIKE :date')
                        ->setParameter('date', date_create_from_format('d/m/Y', $search)->format('Y-m-d') . '%');
                    break;
                case 'services':
                    $queryBuilder->andWhere('services.name LIKE :service_name')
                        ->setParameter('service_name', '%' . $search . '%');
                    break;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function findAllNoLogin()
    {
        $now       = new \DateTime();
        $monthsAgo = $now->sub(\DateInterval::createFromDateString('6 month'));
        $qb        = $this->createQueryBuilder();
        $query     = $qb->where($qb->expr()->isNull('o.lastLogin'))
            ->andWhere($qb->expr()->orX(
                $qb->expr()->isNull('o.createdAt'),
                'o.createdAt < :monthsAgo'
            ))
            ->setParameter('monthsAgo', $monthsAgo->format('Y-m-d H:i:s'))
            ->getQuery();

        return $query->getResult()
        ;
    }

    public function findNonDpoUsers()
    {
        $qb = $this->createQueryBuilder();
        $qb->andWhere('o.roles NOT LIKE :role')
            // TODO add andwhere with "is_dpo"
            ->setParameter('role', sprintf('"%s"', '%ROLE_ADMIN%'));

        return $qb->getQuery()->getResult();
    }

    public function findNonDpoUsersForCollectivity(Model\Collectivity $collectivity)
    {
        $qb = $this->createQueryBuilder();
        $qb->andWhere('o.roles NOT LIKE :role')
            // TODO add andwhere with "is_dpo"
            ->setParameter('role', sprintf('"%s"', '%ROLE_ADMIN%'))
        ->andWhere('o.collectivity = :collectivity')
            ->setParameter('collectivity', $collectivity)
        ;

        return $qb->getQuery()->getResult();
    }
}
