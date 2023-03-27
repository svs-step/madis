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

namespace App\Domain\Admin\Dictionary;

use App\Application\Dictionary\SimpleDictionary;

class DuplicationTargetOptionDictionary extends SimpleDictionary
{
    public const NAME = 'admin_duplication_target_option';

    public const KEY_PER_TYPE         = 'per_type';
    public const KEY_PER_COLLECTIVITY = 'per_collectivity';

    public function __construct()
    {
        parent::__construct(self::NAME, self::getData());
    }

    /**
     * Get an array of Types.
     *
     * @return array
     */
    public static function getData()
    {
        return [
            self::KEY_PER_TYPE         => 'Par type',
            self::KEY_PER_COLLECTIVITY => 'Par liste de choix',
        ];
    }

    /**
     * Get keys of the Types array.
     *
     * @return array
     */
    public static function getDataKeys()
    {
        return \array_keys(self::getData());
    }
}
