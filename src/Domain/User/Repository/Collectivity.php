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

namespace App\Domain\User\Repository;

use App\Application\DDD\Repository\CRUDRepositoryInterface;
use App\Domain\User\Model;

interface Collectivity extends CRUDRepositoryInterface
{
    /**
     * {@inheritdoc}
     *
     * @return Model\Collectivity
     */
    public function findOneById(string $id);

    /**
     * Find collectivities thanks to an ids list.
     *
     * @param string[] $ids
     *
     * @return Model\Collectivity[]
     */
    public function findByIds(array $ids): array;

    /**
     * Find every collectivities which belong to one of the provided types.
     *
     * @param array                   $types
     * @param Model\Collectivity|null $excludedCollectivity
     *
     * @return Model\Collectivity[]
     */
    public function findByTypes(array $types, ?Model\Collectivity $excludedCollectivity = null): array;
}
