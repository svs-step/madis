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

namespace App\Domain\Registry\Dictionary;

use Knp\DictionaryBundle\Dictionary\SimpleDictionary;

class MesurementStatusDictionary extends SimpleDictionary
{
    const STATUS_APPLIED        = 'applied';
    const STATUS_NOT_APPLIED    = 'not-applied';
    const STATUS_NOT_APPLICABLE = 'not-applicable';

    public function __construct()
    {
        parent::__construct('registry_mesurement_status', self::getStatus());
    }

    /**
     * Get an array of Status.
     *
     * @return array
     */
    public static function getStatus()
    {
        return [
            self::STATUS_APPLIED        => 'Appliquée',
            self::STATUS_NOT_APPLIED    => 'Non appliquée',
            self::STATUS_NOT_APPLICABLE => 'Non applicable',
        ];
    }

    /**
     * Get keys of the Status array.
     *
     * @return array
     */
    public static function getStatusKeys()
    {
        return \array_keys(self::getStatus());
    }
}
