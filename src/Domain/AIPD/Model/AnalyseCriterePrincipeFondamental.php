<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Model;

class AnalyseCriterePrincipeFondamental extends CriterePrincipeFondamental
{
    private AnalyseImpact $analyseImpact;

    public function getAnalyseImpact(): AnalyseImpact
    {
        return $this->analyseImpact;
    }

    public function setAnalyseImpact(AnalyseImpact $analyseImpact): void
    {
        $this->analyseImpact = $analyseImpact;
    }
}
