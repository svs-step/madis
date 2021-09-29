<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Converter;

use App\Domain\AIPD\Model\AnalyseImpact;
use App\Domain\AIPD\Model\AnalyseQuestionConformite;
use App\Domain\AIPD\Model\AnalyseScenarioMenace;
use App\Domain\AIPD\Model\CriterePrincipeFondamental;
use App\Domain\AIPD\Model\ModeleAnalyse;
use App\Domain\AIPD\Model\ModeleQuestionConformite;
use App\Domain\AIPD\Model\ModeleScenarioMenace;

/**
 * This class is meant to create an AnalyseImpact object from a ModeleAnalyse
 * It populates every information designed by the ModeleAnalyse to the AnalyseImpact
 * so that it's complety independant from the ModeleAnalyse at the moment it is created.
 */
class ModeleToAnalyseConverter
{
    public static function createFromModeleAnalyse(ModeleAnalyse $modeleAnalyse): AnalyseImpact
    {
        $analyseImpact = new AnalyseImpact();
        $analyseImpact->setModeleAnalyse($modeleAnalyse->getNom());
        $analyseImpact->setCriterePrincipeFondamentaux(self::convertCriteres($analyseImpact, $modeleAnalyse->getCriterePrincipeFondamentaux()));
        $analyseImpact->setQuestionConformites(self::convertQuestionsConformite($analyseImpact, $modeleAnalyse->getQuestionConformites()));
        $analyseImpact->setScenarioMenaces(self::convertScenariosMenace($analyseImpact, $modeleAnalyse->getScenarioMenaces()));

        return $analyseImpact;
    }

    private static function convertCriteres(AnalyseImpact $analyseImpact, $criteres): array
    {
        $res = [];
        foreach ($criteres as $criterePrincipeFondamental) {
            /** @var CriterePrincipeFondamental $clone */
            $clone = clone $criterePrincipeFondamental;
            $clone->setAnalyseImpact($analyseImpact);
            $clone->setModeleAnalyse(null);
            $res[] = $clone;
        }

        return $res;
    }

    private static function convertQuestionsConformite(AnalyseImpact $analyseImpact, $questionsConformite): array
    {
        $res = [];
        /** @var ModeleQuestionConformite $questionModele */
        foreach ($questionsConformite as $questionModele) {
            $question = new AnalyseQuestionConformite($questionModele->getQuestion());
            $question->setAnalyseImpact($analyseImpact);
            $question->setTexteConformite($questionModele->getTexteConformite());
            $question->setTexteNonConformiteMajeure($questionModele->getTexteNonConformiteMajeure());
            $question->setTexteNonConformiteMineure($questionModele->getTexteNonConformiteMineure());
        }

        return $res;
    }

    private static function convertScenariosMenace(AnalyseImpact $analyseImpact, $scenariosMenaces): array
    {
        $res = [];
        /** @var ModeleScenarioMenace $scenarioModele */
        foreach ($scenariosMenaces as $scenarioModele) {
            $scenario = new AnalyseScenarioMenace();
            $scenario->setNom($scenarioModele->getNom());
            $scenario->setIsVisible($scenarioModele->isVisible());
            $scenario->setIsDisponibilite($scenarioModele->isDisponibilite());
            $scenario->setIsIntegrite($scenarioModele->isIntegrite());
            $scenario->setIsConfidentialite($scenarioModele->isConfidentialite());
            $scenario->setVraisemblance($scenarioModele->getVraisemblance());
            $scenario->setGravite($scenarioModele->getGravite());
            $scenario->setPrecisions($scenarioModele->getPrecisions());
            $scenario->setAnalyseImpact($analyseImpact);
        }

        return $res;
    }
}
