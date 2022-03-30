<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Dictionary;

class ModeleVraisemblanceGraviteDictionary extends VraisemblanceGraviteDictionary
{
    const VIDE        = 'vide';

    public function __construct()
    {
        parent::__construct('modele_vraisemblance_gravite', self::getVraisemblanceGravite());
    }

    public static function getVraisemblanceGravite(): array
    {
        $array             = parent::getVraisemblanceGravite();
        $array[self::VIDE] = 'Pas de réponse';

        return $array;
    }
}
