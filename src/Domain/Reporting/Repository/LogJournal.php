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

namespace App\Domain\Reporting\Repository;

use App\Application\DDD\Repository\CRUDRepositoryInterface;
use App\Domain\Reporting\Model\LoggableSubject;
use App\Domain\User\Model\Collectivity;

interface LogJournal extends CRUDRepositoryInterface
{
    public function updateLastKnownNameEntriesForGivenSubject(LoggableSubject $subject);

    public function findPaginated($firstResult, $maxResults, $orderColumn, $orderDir, $searches);

    public function countLogs();

    public function findAllByCollectivity(Collectivity $collectivity, $limit = 15);

    public function deleteAllAnteriorToDate(\DateTime $date);
}
