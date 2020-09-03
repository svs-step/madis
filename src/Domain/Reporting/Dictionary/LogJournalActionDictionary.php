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

namespace App\Domain\Reporting\Dictionary;

use App\Application\Dictionary\SimpleDictionary;

class LogJournalActionDictionary extends SimpleDictionary
{
    const CREATE             = 'create';
    const UPDATE             = 'update';
    const DELETE             = 'delete';
    const LOGIN              = 'login';
    const SOFT_DELETE        = 'soft_delete';
    const SOFT_DELETE_REVOKE = 'soft_delete_revoke';

    public function __construct()
    {
        parent::__construct('reporting_log_journal_action', self::getActions());
    }

    /**
     * @return array
     */
    public static function getActions()
    {
        return [
            self::CREATE             => 'Création',
            self::UPDATE             => 'Mise à jour',
            self::DELETE             => 'Suppression',
            self::LOGIN              => 'Connexion',
            self::SOFT_DELETE        => 'Archivage',
            self::SOFT_DELETE_REVOKE => 'Désarchivage',
        ];
    }

    /**
     * @return array
     */
    public static function getActionsKeys()
    {
        return \array_keys(self::getActions());
    }
}
