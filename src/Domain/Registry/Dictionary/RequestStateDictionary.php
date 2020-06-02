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

namespace App\Domain\Registry\Dictionary;

use App\Application\Dictionary\SimpleDictionary;

class RequestStateDictionary extends SimpleDictionary
{
    const STATE_TO_TREAT              = 'to_treat';
    const STATE_AWAITING_CONFIRMATION = 'awaiting_confirmation';
    const STATE_ON_REQUEST            = 'on_request';
    const STATE_AWAITING_SERVICE      = 'awaiting_service';
    const STATE_COMPLETED_CLOSED      = 'completed_closed';
    const STATE_DENIED                = 'denied';

    public function __construct()
    {
        parent::__construct('registry_request_state', self::getStates());
    }

    /**
     * Get an array of Objects.
     *
     * @return array
     */
    public static function getStates()
    {
        return [
            self::STATE_TO_TREAT                   => 'À traiter',
            self::STATE_AWAITING_CONFIRMATION      => 'En attente confirmation identité de la personne',
            self::STATE_ON_REQUEST                 => 'En demande de précision sur la demande',
            self::STATE_AWAITING_SERVICE           => 'En attente de réponse d\'un service',
            self::STATE_COMPLETED_CLOSED           => 'Demande traitée et clôturée',
            self::STATE_DENIED                     => 'Demande refusée',
        ];
    }

    /**
     * Get keys of the Objects array.
     *
     * @return array
     */
    public static function getStatesKeys()
    {
        return \array_keys(self::getStates());
    }
}
