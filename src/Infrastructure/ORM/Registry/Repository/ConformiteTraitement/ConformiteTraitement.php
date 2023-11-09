<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2019 Soluris - Solutions Numériques Territoriales Innovantes
 * @author ANODE <contact@agence-anode.fr>
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

namespace App\Infrastructure\ORM\Registry\Repository\ConformiteTraitement;

use App\Application\Doctrine\Repository\CRUDRepository;
use App\Domain\Registry\Model;
use App\Domain\Registry\Repository;
use App\Domain\User\Model\Collectivity;

class ConformiteTraitement extends CRUDRepository implements Repository\ConformiteTraitement\ConformiteTraitement
{
    protected function getModelClass(): string
    {
        return Model\ConformiteTraitement\ConformiteTraitement::class;
    }

    public function findAllByCollectivity(Collectivity $collectivity)
    {
        $qb = $this->createQueryBuilder();

        $qb
            ->leftJoin('o.traitement', 't')
            ->andWhere($qb->expr()->eq('t.collectivity', ':collectivity'))
            ->setParameter('collectivity', $collectivity)
            ->orderBy('t.name')
        ;

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }

    public function findActiveByCollectivity(Collectivity $collectivity)
    {
        $qb = $this->createQueryBuilder();

        $qb
            ->leftJoin('o.traitement', 't')
            ->andWhere($qb->expr()->eq('t.collectivity', ':collectivity'))
            ->andWhere($qb->expr()->eq('t.active', ':active'))
            ->setParameter('collectivity', $collectivity)
            ->setParameter('active', true)
            ->orderBy('t.name')
        ;

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }
}
