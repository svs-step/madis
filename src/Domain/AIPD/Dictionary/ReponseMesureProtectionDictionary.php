<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Dictionary;

use App\Application\Dictionary\SimpleDictionary;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

    public static function getPoidsIndexFromReponse(string $reponse): float
    {
        if (!array_key_exists($reponse, self::getReponses())) {
            throw new NotFoundHttpException('Key ' . $reponse . ' not found in ReponseMesureProtectionDictionary');
        }

        if (self::SATISFAISANT === $reponse) {
            return 1;
        } elseif (self::BESOIN_AMELIORATION === $reponse) {
            return 0.5;
        }

        return 0;
    }
}
