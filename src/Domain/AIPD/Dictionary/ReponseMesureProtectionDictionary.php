<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Dictionary;

use App\Application\Dictionary\SimpleDictionary;

class ReponseMesureProtectionDictionary extends SimpleDictionary
{
    const INSATISFAISANT      = 'insatisfaisant';
    const BESOIN_AMELIORATION = 'besoin_amelioration';
    const SATISFAISANT        = 'satisfaisant';

    public function __construct()
    {
        parent::__construct('reponse_mesure_protection', self::getReponses());
    }

    public static function getReponses()
    {
        return [
            self::INSATISFAISANT      => 'Insatisfaisant',
            self::BESOIN_AMELIORATION => 'Doit être amélioré',
            self::SATISFAISANT        => 'Satisfaisant',
        ];
    }
}
