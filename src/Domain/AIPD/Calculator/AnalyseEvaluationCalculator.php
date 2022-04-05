<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Calculator;

use App\Domain\AIPD\Dictionary\ReponseMesureProtectionDictionary;
use App\Domain\AIPD\Dictionary\VraisemblanceGraviteDictionary;
use App\Domain\AIPD\Model\AnalyseScenarioMenace;

class AnalyseEvaluationCalculator
{
    public static function calculateImpactPotentiel(AnalyseScenarioMenace $scenarioMenace)
    {
        $vraisemblance = VraisemblanceGraviteDictionary::getPoidsFromImpact($scenarioMenace->getVraisemblance());
        $gravite       = VraisemblanceGraviteDictionary::getPoidsFromImpact($scenarioMenace->getGravite());

        return self::getImpactFromGraviteAndVraisemblance($gravite, $vraisemblance);
    }

    public static function calculateImpactResiduel(AnalyseScenarioMenace $scenarioMenace)
    {
        $vraisemblanceResiduel = self::calculateIndicateurResiduel($scenarioMenace, VraisemblanceGraviteDictionary::getPoidsFromImpact($scenarioMenace->getVraisemblance()), 'vraisemblance');
        $graviteResiduel       = self::calculateIndicateurResiduel($scenarioMenace, VraisemblanceGraviteDictionary::getPoidsFromImpact($scenarioMenace->getGravite()));

        return self::getImpactFromGraviteAndVraisemblance($graviteResiduel, $vraisemblanceResiduel);
    }

    private static function getImpactFromGraviteAndVraisemblance($gravite, $vraisemblance)
    {
        $value = 1;
        if ($gravite >= 2 && $vraisemblance >= 2) {
            $value = 4;
        } elseif ($gravite >= 2 && $vraisemblance < 2) {
            $value = 3;
        } elseif ($gravite < 2 && $vraisemblance >= 2) {
            $value = 2;
        }

        return $value;
    }

    public static function calculateIndicateurResiduel(AnalyseScenarioMenace $scenarioMenace, $indicateurPotentiel, string $poidsType = 'gravite'): float
    {
        //Gr = max[Gi - (Gi * <mG/<pG); 0,01]

        /* S'il n'y a pas de mesures de protection, il n'y a rien à pondérer car le risque n'est pas traité.
        On retourne alors le risque potentiel (= initial) */
        if ($scenarioMenace->getMesuresProtections()->isEmpty()) {
            return $indicateurPotentiel;
        }
        $s = array_map(function ($value) use ($poidsType) {
            if ('gravite' === $poidsType) {
                return $value->getPoidsGravite();
            }

            return $value->getPoidsVraisemblance();
        }, $scenarioMenace->getMesuresProtections()->toArray());
        $sommePoids         = array_sum($s);
        $sommePoidsPonderes = array_sum(array_map(function ($value) use ($poidsType) {
            if ('gravite' === $poidsType) {
                return $value->getPoidsGravite() * ReponseMesureProtectionDictionary::getPoidsIndexFromReponse($value->getReponse());
            }

            return $value->getPoidsVraisemblance() * ReponseMesureProtectionDictionary::getPoidsIndexFromReponse($value->getReponse());
        }, $scenarioMenace->getMesuresProtections()->toArray()));

        $indicateurResiduel = max($indicateurPotentiel - ($indicateurPotentiel * ($sommePoidsPonderes / $sommePoids)), 0.25);

        return $indicateurResiduel;
    }
}
