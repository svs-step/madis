<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Calculator;

use App\Domain\AIPD\Dictionary\VraisemblanceGraviteDictionary;
use App\Domain\AIPD\Model\AnalyseScenarioMenace;

class AnalyseEvaluationCalculator
{
    public static function calculateRisque(AnalyseScenarioMenace $scenarioMenace)
    {
        $vraisemblance = VraisemblanceGraviteDictionary::getPoidsFromImpact($scenarioMenace->getVraisemblance());
        $gravite       = VraisemblanceGraviteDictionary::getPoidsFromImpact($scenarioMenace->getGravite());
        $max           = $vraisemblance > $gravite ? $vraisemblance : $gravite;

        return VraisemblanceGraviteDictionary::getImpact($max);
    }
}
