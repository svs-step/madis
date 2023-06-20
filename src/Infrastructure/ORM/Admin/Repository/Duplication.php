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

namespace App\Infrastructure\ORM\Admin\Repository;

use App\Application\Doctrine\Repository\CRUDRepository;
use App\Domain\Admin\Model;
use App\Domain\Admin\Repository;

class Duplication extends CRUDRepository implements Repository\Duplication
{
    protected function getModelClass(): string
    {
        return Model\Duplication::class;
    }

    //    /**
    //     * Get an object by ID.
    //     *
    //     * @param string $id The ID to find
    //     *
    //     * @return object|null
    //     */
    //    public function findOneById(string $id)
    //    {
    //        $qb = $this->createQueryBuilder();
    //        $qb
    //            ->andWhere("o.id = :id")
    //            ->setParameter('id', $id)
    //            ->leftJoin("")
    //        ;
    //        dump($id);
    //        return $this->registry
    //            ->getManager()
    //            ->getRepository($this->getModelClass())
    //            ->find($id)
    //            ->
    //            ;
    //
    //
    //        $qb = $this->createQueryBuilder();
    //
    //        foreach ($criteria as $key => $value) {
    //            $this->addWhereClause($qb, $key, $value);
    //        }
    //
    //        return $qb
    //            ->getQuery()
    //            ->getResult()
    //            ;
    //    }
}
