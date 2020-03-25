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

use App\Application\Dictionary\SimpleDictionary;

class ViolationOriginDictionary extends SimpleDictionary
{
    const ORIGIN_LOST_STOLEN_EQUIPMENT       = 'lost_stolen_equipement';
    const ORIGIN_LOST_STOLEN_PAPER           = 'lost_stolen_paper';
    const ORIGIN_LOST_OPENED_MAIL            = 'lost_opened_mail';
    const ORIGIN_HACK                        = 'hack';
    const ORIGIN_TRASH_CONFIDENTIAL_DOCUMENT = 'trash_confidential_document';
    const ORIGIN_TRASH_CONFIDENTIAL_DEVICE   = 'trash_confidential_device';
    const ORIGIN_NON_VOLUNTARY_PUBLICATION   = 'non_voluntary_publication';
    const ORIGIN_BAD_PEOPLE_DATA_DISPLAY     = 'bad_people_data_display';
    const ORIGIN_BAD_RECIPIENT_DATA          = 'bad_recipient';
    const ORIGIN_VERBALLY_DISCLOSED          = 'verbally_disclosed';

    public function __construct()
    {
        parent::__construct('registry_violation_origin', self::getOrigins());
    }

    /**
     * Get an array of Origins.
     *
     * @return array
     */
    public static function getOrigins()
    {
        return [
            self::ORIGIN_LOST_STOLEN_EQUIPMENT       => 'Equipement perdu ou volé',
            self::ORIGIN_LOST_STOLEN_PAPER           => 'Papier perdu, volé ou laissé accessible dans un endroit non sécurisé',
            self::ORIGIN_LOST_OPENED_MAIL            => 'Courrier perdu ou ouvert avant d\'être retourné à l\'envoyeur',
            self::ORIGIN_HACK                        => 'Piratage, logiciel malveillant, hameçonnage',
            self::ORIGIN_TRASH_CONFIDENTIAL_DOCUMENT => 'Mise au rebut de documents papier contenant des données personnelles sans destruction physique',
            self::ORIGIN_TRASH_CONFIDENTIAL_DEVICE   => 'Mise au rebut d’appareils numériques contenant des données personnelles sans effacement sécurisé',
            self::ORIGIN_NON_VOLUNTARY_PUBLICATION   => 'Publication non volontaire d\'informations',
            self::ORIGIN_BAD_PEOPLE_DATA_DISPLAY     => 'Données de la mauvaise personne affichées sur le portail du client',
            self::ORIGIN_BAD_RECIPIENT_DATA          => 'Données personnelles envoyées à un mauvais destinataire',
            self::ORIGIN_VERBALLY_DISCLOSED          => 'Informations personnelles divulguées de façon verbale',
        ];
    }

    /**
     * Get keys of the Origins array.
     *
     * @return array
     */
    public static function getOriginsKeys()
    {
        return \array_keys(self::getOrigins());
    }
}
