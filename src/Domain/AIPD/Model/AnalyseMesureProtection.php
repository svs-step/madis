<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Model;

class AnalyseMesureProtection extends AbstractMesureProtection
{
    private AnalyseImpact $analyseImpact;

    private ?string $reponse;

    private AnalyseScenarioMenace $scenarioMenace;

    public function getAnalyseImpact(): AnalyseImpact
    {
        return $this->analyseImpact;
    }

    public function setAnalyseImpact(AnalyseImpact $analyseImpact): void
    {
        $this->analyseImpact = $analyseImpact;
    }

    public function getReponse(): ?string
    {
        return $this->reponse;
    }

    public function setReponse(?string $reponse): void
    {
        $this->reponse = $reponse;
    }

    public function getScenarioMenace(): AnalyseScenarioMenace
    {
        return $this->scenarioMenace;
    }

    public function setScenarioMenace(AnalyseScenarioMenace $scenarioMenace): void
    {
        $this->scenarioMenace = $scenarioMenace;
    }
}
