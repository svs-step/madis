<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Model;

class AnalyseMesureProtection extends MesureProtection
{
    private AnalyseImpact $analyseImpact;

    private ?string $reponse;

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
}
