<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Dictionary;

class ModeleVraisemblanceGraviteDictionary extends VraisemblanceGraviteDictionary
{
    public const VIDE = 'vide';

    public function __construct()
    {
        parent::__construct('modele_vraisemblance_gravite', self::getVraisemblanceGravite());
    }

    public static function getVraisemblanceGravite(): array
    {
        $array[self::VIDE] = 'Pas de réponse';
        $result            = array_merge($array, parent::getVraisemblanceGravite());

        return $result;
    }
}
