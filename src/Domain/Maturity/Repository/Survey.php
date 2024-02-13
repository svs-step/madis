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

namespace App\Domain\Maturity\Repository;

use App\Application\DDD\Repository\CRUDRepositoryInterface;
use App\Application\Doctrine\Repository\DataTablesRepository;
use App\Domain\User\Model\Collectivity;

interface Survey extends CRUDRepositoryInterface, DataTablesRepository
{
    /**
     * Find all survey by associated collectivity.
     *
     * @param Collectivity $collectivity The collectivity to search with
     * @param array        $order        Order the data
     *
     * @return array The array of survey given by the collectivity
     */
    public function findAllByCollectivity(Collectivity $collectivity, array $order = [], ?int $limit = null, array $where = []): iterable;

    /**
     * Find previous survey by created_at date.
     */
    public function findPreviousById(string $id, int $limit = 1): iterable;

    /**
     * Average survey during the last year.
     *
     * @return string The count of mesurements
     */
    public function averageSurveyDuringLastYear(array $collectivites = []);

    /**
     * Find all survey by associated collectivities.
     *
     * @param array $collectivities The array of collectivity to search with
     * @param array $order          Order the data
     *
     * @return array The array of survey given by the collectivity
     */
    public function findAllByCollectivities(array $collectivities, array $order = [], ?int $limit = null): iterable;

    /**
     * Find all surveys that have not been updated for more than 18 months.
     */
    public function findAllLate(): array;
}
