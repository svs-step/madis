<?php

namespace App\Domain\Registry\Service;

use App\Domain\Registry\Model\ConformiteOrganisation\Conformite;
use App\Domain\Registry\Model\ConformiteOrganisation\Evaluation;

class ConformiteOrganisationService
{
    public static function getOrderedConformites(Evaluation $evaluation): array
    {
        $conformites = \iterable_to_array($evaluation->getConformites());
        usort($conformites, function ($a, $b) {
            return $a->getProcessus()->getPosition() > $b->getProcessus()->getPosition() ? 1 : -1;
        });

        return $conformites;
    }

    public static function getOrderedReponse(Conformite $conformite)
    {
        $reponses = \iterable_to_array($conformite->getReponses());
        usort($reponses, function ($a, $b) {
            return $a->getQuestion()->getPosition() > $b->getQuestion()->getPosition() ? 1 : -1;
        });

        return $reponses;
    }
}
