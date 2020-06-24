<?php

namespace App\Domain\Registry\Dictionary\ConformiteOrganisation;

use App\Application\Dictionary\SimpleDictionary;

class ReponseDictionary extends SimpleDictionary
{
    const NON_CONCERNE   = -1;
    const INEXISTANTE    = 0;
    const TRES_ELOIGNEE  = 1;
    const PARTIELLE      = 2;
    const QUASI_CONFORME = 3;
    const MESURABLE      = 4;
    const REVISEE        = 5;

    public function __construct()
    {
        parent::__construct('registry_conformite_organisation_reponse', self::getReponses());
    }

    public static function getReponses()
    {
        return [
            self::NON_CONCERNE   => 'Non concerné',
            self::INEXISTANTE    => 'Inexistante',
            self::TRES_ELOIGNEE  => 'Très éloignée',
            self::PARTIELLE      => 'Partielle',
            self::QUASI_CONFORME => 'Quasi conforme',
            self::MESURABLE      => 'Mesurable',
            self::REVISEE        => 'Révisée',
        ];
    }

    public static function getReponseLabelFromKey(string $key)
    {
        return self::getReponses()[$key];
    }
}
