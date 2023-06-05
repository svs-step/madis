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

interface Treatment extends CRUDRepositoryInterface, DataTablesRepository
{
    /**
     * Find all treatments.
     *
     * @param bool  $active Get active / inactive treatments
     * @param array $order  Order results
     *
     * @return array The array of treatments
     */
    public function findAllActive(bool $active = true, array $order = []);

    /**
     * Find all treatments by associated collectivity.
     *
     * @param Collectivity $collectivity The collectivity to search with
     * @param array        $order        Order results
     *
     * @return array The array of treatments given by the collectivity
     */
    public function findAllByCollectivity(Collectivity $collectivity, array $order = []);

    /**
     * Find all active treatments by associated collectivity.
     *
     * @param Collectivity $collectivity The collectivity to search with
     * @param bool         $active       Get active / inactive treatment
     * @param array        $order        Order results
     *
     * @return array The array of treatments given by the collectivity
     */
    public function findAllActiveByCollectivity(Collectivity $collectivity = null, bool $active = true, array $order = []);

    /**
     * Find all active treatments by associated collectivity and hasModuleConformiteTraitement active.
     *
     * @param Collectivity|array $collectivity The collectivity to search with
     * @param bool               $active       Get active / inactive treatment
     * @param array              $order        Order results
     *
     * @return array The array of treatments given by the collectivity
     */
    public function findAllActiveByCollectivityWithHasModuleConformiteTraitement($collectivity = null, bool $active = true, array $order = []);

    /**
     * Count all by collectivity.
     */
    public function countAllByCollectivity(Collectivity $collectivity);

    /**
     * Get the last updated treatment by collectivity.
     */
    public function findOneOrNullLastUpdateByCollectivity(Collectivity $collectivity): ?\App\Domain\Registry\Model\Treatment;

    /**
     * Count all by collectivity.
     */
    public function countAllActiveByCollectivity(Collectivity $collectivity);

    /**
     * Return all treatment from active collectivity.
     *
     * @return array
     */
    public function findAllByActiveCollectivity(bool $active = true, User $user = null);

    /**
     * Count all with no conformite traitement by collectivity.
     */
    public function countAllWithNoConformiteTraitementByCollectivity(?Collectivity $collectivity);

    /**
     * Find all by collectivity of their clonedFrom.
     */
    public function findAllByClonedFromCollectivity(Collectivity $collectivity);

    /**
     * Set clonedFrom to null by collectivity.
     */
    public function resetClonedFromCollectivity(Collectivity $collectivity);
}
