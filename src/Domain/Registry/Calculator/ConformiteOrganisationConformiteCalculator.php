<?php

namespace App\Domain\Registry\Calculator;

use App\Domain\Registry\Dictionary\ConformiteOrganisation\ReponseDictionary;
use App\Domain\Registry\Model\ConformiteOrganisation\Conformite;
use App\Domain\Registry\Model\ConformiteOrganisation\Evaluation;

class ConformiteOrganisationConformiteCalculator
{
    public function calculEvaluationConformites(Evaluation $evaluation)
    {
        foreach ($evaluation->getConformites() as $conformite) {
            $conformite->setConformite($this->calculConformite($conformite));
        }
    }

    private function calculConformite(Conformite $conformite): float
    {
        $nbReponse = 0;
        $score     = 0;
        foreach ($conformite->getReponses() as $reponse) {
            if (ReponseDictionary::NON_CONCERNE === $reponse->getReponse()) {
                continue;
            }
            $score += $reponse->getReponse();
            ++$nbReponse;
        }

        return 0 !== $nbReponse ? round($score / $nbReponse, 2) : 0;
    }
}
