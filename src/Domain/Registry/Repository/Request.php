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

use App\Application\DDD\Repository\RepositoryInterface;
use App\Application\Doctrine\Repository\DataTablesRepository;
use App\Domain\User\Model\Collectivity;

interface Request extends RepositoryInterface, DataTablesRepository
{
    /**
     * Insert an object.
     *
     * @param mixed $object
     */
    public function insert($object): void;

    /**
     * Update an object.
     *
     * @param mixed $object
     */
    public function update($object): void;

    /**
     * Create an object.
     *
     * @return mixed
     */
    public function create();

    /**
     * Remove an object.
     *
     * @param mixed $object
     */
    public function remove($object): void;

    /**
     * Get all objects.
     *
     * @return mixed[]
     */
    public function findAll(bool $deleted = false): array;

    /**
     * Get an object by ID.
     *
     * @param string $id The ID to find
     *
     * @return mixed[]
     */
    public function findOneById(string $id);

    /**
     * Find all requests by associated collectivity.
     *
     * @param Collectivity $collectivity The collectivity to search with
     * @param bool         $deleted      Get deleted rows or not
     * @param array        $order        Order results
     *
     * @return array The array of requests given by the collectivity
     */
    public function findAllByCollectivity(Collectivity $collectivity, bool $deleted = false, array $order = []);

    /**
     * Find all requests by criteria.
     *
     * @param array $criteria List of criteria
     *
     * @return array The array of requests given by criteria
     */
    public function findBy(array $criteria = []);

    /**
     * Find all requests.
     *
     * @param bool  $archived Get all archived or not
     * @param array $order    Order results
     *
     * @return array The array of requests
     */
    public function findAllArchived(bool $archived = false, array $order = []);

    /**
     * Find all active requests by associated collectivity.
     *
     * @param Collectivity $collectivity The collectivity to search with
     * @param bool         $archived     Get all archived or not
     * @param array        $order        Order results
     *
     * @return array The array of requests given by the collectivity
     */
    public function findAllArchivedByCollectivity(Collectivity $collectivity, bool $archived = false, array $order = []);

    /**
     * Count all by collectivity.
     */
    public function countAllByCollectivity(Collectivity $collectivity);

    /**
     * Get the last updated treatment by collectivity.
     */
    public function findOneOrNullLastUpdateByCollectivity(Collectivity $collectivity): ?\App\Domain\Registry\Model\Request;

    /**
     * Get the requests that haven't been updated for more than a month
     * @return array
     */
    public function findAllLate(): array;
}
