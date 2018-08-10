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

class RequestObjectDictionary extends SimpleDictionary
{
    const OBJECT_CORRECT          = 'correct';
    const OBJECT_DELETE           = 'delete';
    const OBJECT_WITHDRAW_CONSENT = 'withdraw_consent';
    const OBJECT_ACCESS           = 'access';
    const OBJECT_DATA_PORTABILITY = 'data_portability';
    const OBJECT_LIMIT_TREATMENT  = 'limit_treatment';

    public function __construct()
    {
        parent::__construct('registry_request_object', self::getObjects());
    }

    /**
     * Get an array of Objects.
     *
     * @return array
     */
    public static function getObjects()
    {
        return [
            self::OBJECT_CORRECT          => 'Rectifier des données',
            self::OBJECT_DELETE           => 'Supprimer des données',
            self::OBJECT_WITHDRAW_CONSENT => 'Retirer le consentement',
            self::OBJECT_ACCESS           => 'Accéder à des données',
            self::OBJECT_DATA_PORTABILITY => 'Portabilité des données',
            self::OBJECT_LIMIT_TREATMENT  => 'Limiter le traitement',
        ];
    }

    /**
     * Get keys of the Objects array.
     *
     * @return array
     */
    public static function getObjectsKeys()
    {
        return \array_keys(self::getObjects());
    }
}
