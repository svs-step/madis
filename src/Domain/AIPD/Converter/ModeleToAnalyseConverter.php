<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Converter;

use App\Domain\AIPD\Dictionary\ReponseCritereFondamentalDictionary;
use App\Domain\AIPD\Model\AnalyseImpact;
use App\Domain\AIPD\Model\AnalyseMesureProtection;
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
        $mesuresProtections = self::convertDistinctMesureProtections($modeleAnalyse, $analyseImpact);
        $analyseImpact->setMesureProtections($mesuresProtections);
        $analyseImpact->setScenarioMenaces(self::convertScenariosMenace($analyseImpact, $modeleAnalyse->getScenarioMenaces(), $mesuresProtections));

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
            $clone->setCanBeModified(true);
            if (ReponseCritereFondamentalDictionary::REPONSE_NON_RENSEIGNE !== $clone->getReponse()) {
                $clone->setCanBeModified(false);
            }
            $res[] = $clone;
        }

        return $res;
    }

    private static function convertQuestionsConformite(AnalyseImpact $analyseImpact, $questionsConformite): array
    {
        $res = [];
        /** @var ModeleQuestionConformite $questionModele */
        foreach ($questionsConformite as $questionModele) {
            $question = new AnalyseQuestionConformite($questionModele->getQuestion(), $questionModele->getPosition());
            $question->setAnalyseImpact($analyseImpact);
            $question->setTexteConformite($questionModele->getTexteConformite());
            $question->setTexteNonConformiteMajeure($questionModele->getTexteNonConformiteMajeure());
            $question->setTexteNonConformiteMineure($questionModele->getTexteNonConformiteMineure());
            $question->setIsJustificationObligatoire($questionModele->isJustificationObligatoire());
            $res[] = $question;
        }

        return $res;
    }

    private static function convertScenariosMenace(AnalyseImpact $analyseImpact, $scenariosMenaces, array $mesuresProtections): array
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
            foreach ($scenarioModele->getMesuresProtections() as $scenarioMesures) {
                /** @var AnalyseMesureProtection $mesuresProtection */
                foreach ($mesuresProtections as $mesuresProtection) {
                    if ($scenarioMesures->getId()->toString() === $mesuresProtection->getOriginId()) {
                        $mesuresProtection->addScenarioMenace($scenario);
                        $scenario->addMesureProtection($mesuresProtection);
                        continue;
                    }
                }
            }
//            $scenario->setMesuresProtections(self::convertMesuresProtections($scenarioModele, $scenario, $analyseImpact));
            if ($scenarioModele->isDisponibilite() || $scenarioModele->isIntegrite() || $scenarioModele->isConfidentialite()) {
                $scenario->setCanDicBeModified(false);
            } else {
                $scenario->setCanDicBeModified(true);
            }
            $res[] = $scenario;
        }

        return $res;
    }

    private static function convertMesuresProtections(ModeleScenarioMenace $modeleScenarioMenace, AnalyseScenarioMenace $analyseScenarioMenace, AnalyseImpact $analyseImpact): array
    {
        $res = [];
        foreach ($modeleScenarioMenace->getMesuresProtections() as $mesureProtection) {
            $analyseMesure = new AnalyseMesureProtection();
            $analyseMesure->setNom($mesureProtection->getNom());
            $analyseMesure->setNomCourt($mesureProtection->getNomCourt());
            $analyseMesure->setLabelLivrable($mesureProtection->getLabelLivrable());
            $analyseMesure->setPhrasePreconisation($mesureProtection->getPhrasePreconisation());
            $analyseMesure->setDetail($mesureProtection->getDetail());
            $analyseMesure->setPoidsVraisemblance($mesureProtection->getPoidsVraisemblance());
            $analyseMesure->setPoidsGravite($mesureProtection->getPoidsGravite());
//            $analyseMesure->setScenarioMenace($analyseScenarioMenace);
            $analyseMesure->setOriginId($mesureProtection->getId()->toString());
            $analyseMesure->setAnalyseImpact($analyseImpact);
            $res[] = $analyseMesure;
        }

        return $res;
    }

    private static function convertDistinctMesureProtections(ModeleAnalyse $modeleAnalyse, AnalyseImpact $analyseImpact)
    {
        $res = [];
        foreach ($modeleAnalyse->getScenarioMenaces() as $scenario) {
            foreach ($scenario->getMesuresProtections() as $mesureProtection) {
                if (!array_key_exists((string) $mesureProtection->getId(), $res)) {
                    $analyseMesure = new AnalyseMesureProtection();
                    $analyseMesure->setNom($mesureProtection->getNom());
                    $analyseMesure->setNomCourt($mesureProtection->getNomCourt());
                    $analyseMesure->setLabelLivrable($mesureProtection->getLabelLivrable());
                    $analyseMesure->setPhrasePreconisation($mesureProtection->getPhrasePreconisation());
                    $analyseMesure->setDetail($mesureProtection->getDetail());
                    $analyseMesure->setPoidsVraisemblance($mesureProtection->getPoidsVraisemblance());
                    $analyseMesure->setPoidsGravite($mesureProtection->getPoidsGravite());
                    $analyseMesure->setOriginId($mesureProtection->getId()->toString());
                    $analyseMesure->setAnalyseImpact($analyseImpact);
                    $res[$mesureProtection->getId()->toString()] = $analyseMesure;
                }
            }
        }

        //Set d'abord le tableau de mesure protection, puis set les scenario avec leur mesure et les faire pointer sur celles du tableau

        return array_values($res);
    }
}
