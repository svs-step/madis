<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Model;

class ModeleMesureProtection extends AbstractMesureProtection
{
    /**
     * @var array|AbstractScenarioMenace
     */
    private $scenariosMenaces;

    public function getScenariosMenaces()
    {
        return $this->scenariosMenaces;
    }

    public function setScenariosMenaces($scenariosMenaces): void
    {
        $this->scenariosMenaces = $scenariosMenaces;
    }
}
