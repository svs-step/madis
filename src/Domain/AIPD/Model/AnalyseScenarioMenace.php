<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Model;

class AnalyseScenarioMenace extends AbstractScenarioMenace
{
    private AnalyseImpact $analyseImpact;

    private bool $canDicBeModified           = true;
    private bool $canVraisemblanceBeModified = false;
    private bool $canGraviteBeModified       = false;

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

    public function addMesureProtection(AnalyseMesureProtection $mesureProtection): void
    {
        $this->mesuresProtections[] = $mesureProtection;
    }

    public function isCanVraisemblanceBeModified(): bool
    {
        return $this->canVraisemblanceBeModified;
    }

    public function setCanVraisemblanceBeModified(bool $canVraisemblanceBeModified): void
    {
        $this->canVraisemblanceBeModified = $canVraisemblanceBeModified;
    }

    public function isCanGraviteBeModified(): bool
    {
        return $this->canGraviteBeModified;
    }

    public function setCanGraviteBeModified(bool $canGraviteBeModified): void
    {
        $this->canGraviteBeModified = $canGraviteBeModified;
    }
}
