<?php

/**
 * This file is part of the SOLURIS - RGPD Management application.
 *
 * (c) Donovan Bourlard <donovan@awkan.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Domain\Registry\Dictionary;

use Knp\DictionaryBundle\Dictionary\SimpleDictionary;

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
