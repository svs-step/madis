<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Model;

class AnalyseScenarioMenace extends AbstractScenarioMenace
{
    private AnalyseImpact $analyseImpact;

    private bool $canDicBeModified = true;

    public function getAnalyseImpact(): AnalyseImpact
    {
        return $this->analyseImpact;
    }

    public function setAnalyseImpact(AnalyseImpact $analyseImpact): void
    {
        $this->analyseImpact = $analyseImpact;
    }

    public function isCanDicBeModified(): bool
    {
        return $this->canDicBeModified;
    }

    public function setCanDicBeModified(bool $canDicBeModified): void
    {
        $this->canDicBeModified = $canDicBeModified;
    }
}
