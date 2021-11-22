<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Model;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("none")
 */
class ModeleScenarioMenace extends AbstractScenarioMenace
{
    /**
     * @Serializer\Exclude
     */
    private ModeleAnalyse $modeleAnalyse;

    public function getModeleAnalyse(): ModeleAnalyse
    {
        return $this->modeleAnalyse;
    }

    public function setModeleAnalyse(ModeleAnalyse $modeleAnalyse): void
    {
        $this->modeleAnalyse = $modeleAnalyse;
    }
}
