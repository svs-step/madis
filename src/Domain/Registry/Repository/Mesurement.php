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

namespace App\Domain\Registry\Repository;

use App\Application\DDD\Repository\CRUDRepositoryInterface;
use App\Application\Doctrine\Repository\DataTablesRepository;
use App\Domain\User\Model\Collectivity;
use App\Domain\User\Model\User;

interface Mesurement extends CRUDRepositoryInterface, DataTablesRepository
{
    /**
     * Find all mesurements by associated collectivity.
     *
     * @param Collectivity $collectivity The collectivity to search with
     * @param array        $order        Order results
     *
     * @return array The array of mesurements given by the collectivity
     */
    public function findAllByCollectivity(Collectivity $collectivity, array $order = []);

    /**
     * Find all mesurements by criteria.
     *
     * @param array $criteria List of criteria
     *
     * @return array The array of mesurements given by criteria
     */
    public function findBy(array $criteria = []);

    /**
     * Find all planified mesurements by criteria.
     *
     * @param array $criteria List of criteria
     *
     * @return array The array of mesurements given by criteria
     */
    public function findByPlanified(array $criteria = []);

    /**
     * Count all planified and not applied mesurements by collectivity.
     *
     * @return string The count of mesurements
     */
    public function countPlanifiedByCollectivity(Collectivity $collectivity);

    /**
     * Count all applied mesurements by collectivity.
     *
     * @return string The count of mesurements
     */
    public function countAppliedByCollectivity(Collectivity $collectivity);

    /**
     * Mesurement planified average mesurements.
     *
     * @return string The average
     */
    public function planifiedAverageOnAllCollectivity($collectivities);

    /**
     * Return all mesurement from active collectivity.
     *
     * @return array
     */
    public function findAllByActiveCollectivity(bool $active = true, ?User $user = null);

    /**
     * Find all by collectivity of their clonedFrom.
     */
    public function findAllByClonedFromCollectivity(Collectivity $collectivity);

    /**
     * Return all action planified for dashboard.
     *
     * @return array
     */
    public function getPlanifiedActionsDashBoard($limit, Collectivity $collectivity);

    /**
     * Set clonedFrom to null by collectivity.
     */
    public function resetClonedFromCollectivity(Collectivity $collectivity);
}
