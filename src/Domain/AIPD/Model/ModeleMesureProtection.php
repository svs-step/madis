<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Model;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("none")
 */
class ModeleMesureProtection extends AbstractMesureProtection
{
    /**
     * @var array|ModeleScenarioMenace[]
     * @Serializer\Exclude
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

    public function addScenarioMenace($scenariosMenace): void
    {
        $this->scenariosMenaces[] = $scenariosMenace;
    }
}
