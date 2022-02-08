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

namespace App\Infrastructure\ORM\Documentation\Repository;

use App\Application\Doctrine\Repository\CRUDRepository;
use App\Domain\Documentation\Model;
use App\Domain\Documentation\Repository;

class Document extends CRUDRepository implements Repository\Document
{
    /**
     * {@inheritdoc}
     */
    protected function getModelClass(): string
    {
        return Model\Document::class;
    }

    public function findOneByName(string $name)
    {
        $docs = $this->registry
            ->getManager()
            ->getRepository($this->getModelClass())
            ->findBy(['file' => $name])
            ;
        if (count($docs) > 0) {
            return $docs[0];
        }
    }

    public function findByCategory(Model\Category $category, $order)
    {
        return $this->createQueryBuilder()
            ->join('o.categories', 'dc')
            ->where('dc = :category')
            ->setParameter('category', $category)
            ->getQuery()
            ->getResult();

        return $this->registry
            ->getManager()
            ->getRepository($this->getModelClass())
            ->findBy([
                'cate',
            ], $order)
            ;
    }
}
