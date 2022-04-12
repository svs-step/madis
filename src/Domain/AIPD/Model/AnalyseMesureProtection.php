<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Model;

use Doctrine\Common\Collections\ArrayCollection;

class AnalyseMesureProtection extends AbstractMesureProtection
{
    private AnalyseImpact $analyseImpact;

    private ?string $reponse;

    private iterable $scenariosMenaces;

    private string $originId;

    public function __construct()
    {
        parent::__construct();
        $this->scenariosMenaces = new ArrayCollection();
    }

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

    public function getScenariosMenaces(): iterable
    {
        return $this->scenariosMenaces;
    }

    public function setScenariosMenaces(iterable $scenariosMenaces): void
    {
        $this->scenariosMenaces = $scenariosMenaces;
    }

    public function addScenarioMenace(AnalyseScenarioMenace $scenarioMenace)
    {
        $this->scenariosMenaces[] = $scenarioMenace;
    }

    public function getOriginId(): string
    {
        return $this->originId;
    }

    public function setOriginId(string $originId): void
    {
        $this->originId = $originId;
    }
}
