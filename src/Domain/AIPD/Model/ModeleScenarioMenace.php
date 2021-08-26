<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Model;

class ModeleScenarioMenace extends AbstractScenarioMenace
{
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
