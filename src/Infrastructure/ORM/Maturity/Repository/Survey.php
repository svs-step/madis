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
}
