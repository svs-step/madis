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

namespace App\Infrastructure\ORM\Maturity\Repository;

use App\Application\Doctrine\Repository\CRUDRepository;
use App\Domain\Maturity\Model;
use App\Domain\Maturity\Repository;
use App\Domain\User\Model\Collectivity;

class Survey extends CRUDRepository implements Repository\Survey
{
    /**
     * {@inheritdoc}
     */
    protected function getModelClass(): string
    {
        return Model\Survey::class;
    }

    /**
     * {@inheritdoc}
     */
    public function findAllByCollectivity(Collectivity $collectivity, array $order = [], int $limit = null): iterable
    {
        $qb = $this->createQueryBuilder()
            ->andWhere('o.collectivity = :collectivity')
            ->setParameter('collectivity', $collectivity)
        ;

        foreach ($order as $key => $dir) {
            $qb->addOrderBy("o.{$key}", $dir);
        }

        if (!\is_null($limit)) {
            $qb
                ->setFirstResult(0)
                ->setMaxResults($limit);
        }

        return $qb
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * {@inheritdoc}
     */
    public function findPreviousById(string $id, int $limit = 1): iterable
    {
        return $this->createQueryBuilder()
            ->select('s')
            ->from(Model\Survey::class, 's')
            ->andWhere('o.id = :id')
            ->andWhere('o.collectivity = s.collectivity')
            ->andWhere('o.createdAt > s.createdAt')
            ->orderBy('s.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function averageSurveyDuringLastYear(array $collectivities = [])
    {
        $sql = 'SELECT AVG(a.rcount) FROM (
            SELECT IF(COUNT(ms.id) > 0, 1, 0) as rcount
            FROM user_collectivity uc
            LEFT OUTER JOIN maturity_survey ms ON (uc.id = ms.collectivity_id AND ms.created_at >= NOW() - INTERVAL 1 YEAR)
            WHERE uc.active = 1';

        if (!empty($collectivities)) {
            $sql .= ' AND uc.id IN (';
            $sql .= \implode(',', \array_map(function ($collectivity) {
                return '\'' . $collectivity->getId() . '\'';
            }, $collectivities));
            $sql .= ') ';
        }

        $sql .= ' 
            GROUP BY uc.id
        ) a';

        $stmt = $this->getManager()->getConnection()->prepare($sql);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * {@inheritdoc}
     */
    public function findAllByCollectivities(array $collectivities, array $order = [], int $limit = null): iterable
    {
        $qb = $this->createQueryBuilder();

        $qb
            ->andWhere(
                $qb->expr()->in('o.collectivity', ':collectivities')
            )
            ->setParameter('collectivities', $collectivities)
        ;

        foreach ($order as $key => $dir) {
            $qb->addOrderBy("o.{$key}", $dir);
        }

        if (!\is_null($limit)) {
            $qb
                ->setFirstResult(0)
                ->setMaxResults($limit);
        }

        return $qb
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * {@inheritdoc}
     */
    public function findAllLate(): array
    {
        $now       = new \DateTime();
        $monthsAgo = $now->sub(\DateInterval::createFromDateString('1 month'));

        return $this->createQueryBuilder()
            ->andWhere('o.updatedAt < :lastmonth')
            ->setParameter('lastmonth', $monthsAgo->format('Y-m-d'))
            ->getQuery()
            ->getResult();
    }
}
