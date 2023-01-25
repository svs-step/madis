<?php

namespace App\Domain\Registry\Dictionary\ConformiteOrganisation;

use App\Application\Dictionary\SimpleDictionary;

class ReponseDictionary extends SimpleDictionary
{
    public const NON_CONCERNE   = -1;
    public const INEXISTANTE    = 0;
    public const TRES_ELOIGNEE  = 1;
    public const PARTIELLE      = 2;
    public const QUASI_CONFORME = 3;
    public const MESURABLE      = 4;
    public const REVISEE        = 5;

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
