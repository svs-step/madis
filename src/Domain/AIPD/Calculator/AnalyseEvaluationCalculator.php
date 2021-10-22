<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Calculator;

use App\Domain\AIPD\Dictionary\VraisemblanceGraviteDictionary;
use App\Domain\AIPD\Model\AnalyseScenarioMenace;

class AnalyseEvaluationCalculator
{
    public static function calculateImpactPotentiel(AnalyseScenarioMenace $scenarioMenace)
    {
        $vraisemblance = VraisemblanceGraviteDictionary::getPoidsFromImpact($scenarioMenace->getVraisemblance());
        $gravite       = VraisemblanceGraviteDictionary::getPoidsFromImpact($scenarioMenace->getGravite());
        $value         = 1;
        if ($gravite >= 3 && $vraisemblance >= 3) {
            $value = 4;
        } elseif ($gravite >= 3 && $vraisemblance < 3) {
            $value = 3;
        } elseif ($gravite < 3 && $vraisemblance >= 3) {
            $value = 2;
        }

        return VraisemblanceGraviteDictionary::getImpact($value);
    }

    public static function calculateImpactResiduel(AnalyseScenarioMenace $scenarioMenace)
    {
        $value = 0;

        return VraisemblanceGraviteDictionary::getImpact($value);
    }
}
