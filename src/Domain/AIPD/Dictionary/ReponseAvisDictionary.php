<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Dictionary;

use App\Application\Dictionary\SimpleDictionary;

class ReponseAvisDictionary extends SimpleDictionary
{
    public const REPONSE_NONE              = 'pas_de_reponse';
    public const REPONSE_FAVORABLE         = 'favorable';
    public const REPONSE_FAVORABLE_RESERVE = 'favorable_reserve';
    public const REPONSE_DEFAVORABLE       = 'defavorable';

    public function __construct()
    {
        parent::__construct('reponse_avis', self::getReponseAvis());
    }

    public static function getReponseAvis()
    {
        return [
            self::REPONSE_NONE              => 'Pas de réponse',
            self::REPONSE_FAVORABLE         => 'Favorable',
            self::REPONSE_FAVORABLE_RESERVE => 'Favorable avec réserve(s)',
            self::REPONSE_DEFAVORABLE       => 'Défavorable',
        ];
    }
}
