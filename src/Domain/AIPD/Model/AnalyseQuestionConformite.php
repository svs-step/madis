<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Model;

class AnalyseQuestionConformite extends AbstractQuestionConformite
{
    protected AnalyseImpact $analyseImpact;

    protected string $justificatif;

    public function getAnalyseImpact(): AnalyseImpact
    {
        return $this->analyseImpact;
    }

    public function setAnalyseImpact(AnalyseImpact $analyseImpact): void
    {
        $this->analyseImpact = $analyseImpact;
    }

    public function getJustificatif(): string
    {
        return $this->justificatif;
    }

    public function setJustificatif(string $justificatif): void
    {
        $this->justificatif = $justificatif;
    }
}
