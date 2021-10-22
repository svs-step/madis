<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Model;

class AnalyseScenarioMenace extends AbstractScenarioMenace
{
    private AnalyseImpact $analyseImpact;

    private AnalyseMesureProtection $scenarioMenace;

    public function getAnalyseImpact(): AnalyseImpact
    {
        return $this->analyseImpact;
    }

    public function setAnalyseImpact(AnalyseImpact $analyseImpact): void
    {
        $this->analyseImpact = $analyseImpact;
    }
}
