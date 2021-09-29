<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Dictionary;

use App\Application\Dictionary\SimpleDictionary;

class StatutAnalyseImpactDictionary extends SimpleDictionary
{
    const NON_REALISEE            = 'non_realisee';
    const FAVORABLE               = 'favorable';
    const FAVORABLE_AVEC_RESERVES = 'favorable_avec_reserves';
    const NON_FAVORABLE           = 'non_favorable';
    const EN_COURS                = 'en_cours';

    public function __construct()
    {
        parent::__construct('statut_analyse_impact', $this->getStatuts());
    }

    public static function getStatuts()
    {
        return [
            self::NON_REALISEE            => 'Non réalisée',
            self::FAVORABLE               => 'Favorable',
            self::FAVORABLE_AVEC_RESERVES => 'Favorable avec réserves',
            self::NON_FAVORABLE           => 'Non favorable',
            self::EN_COURS                => 'En cours',
        ];
    }
}
