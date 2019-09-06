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

namespace App\Domain\Admin\Cloner;

use App\Domain\Admin\Model;
use App\Domain\User\Model as UserModel;

interface ClonerInterface
{
    /**
     * Clone data thanks to information contained in Duplication model.
     *
     * @param Model\Duplication $duplication
     */
    public function clone(Model\Duplication $duplication): void;

    /**
     * Clone data only for specified target collectivity.
     *
     * @param Model\Duplication      $duplication
     * @param UserModel\Collectivity $targetCollectivity
     */
    public function cloneToSpecifiedTarget(Model\Duplication $duplication, UserModel\Collectivity $targetCollectivity): void;
}
