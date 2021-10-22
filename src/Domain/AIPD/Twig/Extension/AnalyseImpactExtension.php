<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Twig\Extension;

use App\Domain\AIPD\Calculator\AnalyseEvaluationCalculator;
use App\Domain\AIPD\Dictionary\ReponseCritereFondamentalDictionary;
use App\Domain\AIPD\Dictionary\VraisemblanceGraviteDictionary;
use App\Domain\AIPD\Model\AnalyseImpact;
use App\Domain\AIPD\Model\AnalyseQuestionConformite;
use App\Domain\AIPD\Model\AnalyseScenarioMenace;
use App\Domain\AIPD\Model\CriterePrincipeFondamental;
use App\Domain\Registry\Model\ConformiteTraitement\ConformiteTraitement;
use App\Domain\Registry\Model\Mesurement;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AnalyseImpactExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('getFormattedActionProtectionsFromQuestion', [$this, 'getFormattedActionProtectionsFromQuestion']),
            new TwigFunction('getConformiteLabel', [$this, 'getConformiteLabel']),
            new TwigFunction('getCritereLabel', [$this, 'getCritereLabel']),
            new TwigFunction('getScenarioMenaceImpactPotentielLabel', [$this, 'getScenarioMenaceImpactPotentielLabel']),
            new TwigFunction('getScenarioMenaceImpactResiduelLabel', [$this, 'getScenarioMenaceImpactResiduelLabel']),
            new TwigFunction('getLastAnalyseImpact', [$this, 'getLastAnalyseImpact']),
        ];
    }

    public function getFormattedActionProtectionsFromQuestion(AnalyseQuestionConformite $questionAnalyse)
    {
        $reponseConformite = $questionAnalyse->getAnalyseImpact()->getConformiteTraitement()->getReponseOfPosition($questionAnalyse->getPosition());
        $formattedString   = '';
        /** @var Mesurement $actionProtection */
        foreach ($reponseConformite->getActionProtections() as $actionProtection) {
            $formattedString .= $actionProtection->getName() . ' ';
        }

        return $formattedString;
    }

    public function getConformiteLabel(AnalyseQuestionConformite $questionAnalyse): string
    {
        $reponseConformite = $questionAnalyse->getAnalyseImpact()->getConformiteTraitement()->getReponseOfPosition($questionAnalyse->getPosition());
        if ($reponseConformite->isConforme()) {
            return '<span class="label label-success" style="min-width: 100%; display: inline-block;">Conforme</span>';
        } elseif (!$reponseConformite->getActionProtections()->isEmpty()) {
            return '<span class="label label-warning" style="min-width: 100%; display: inline-block;">Non-conforme mineure</span>';
        }

        return '<span class="label label-danger" style="min-width: 100%; display: inline-block;">Non-conforme majeure</span>';
    }

    public function getScenarioMenaceImpactPotentielLabel(AnalyseScenarioMenace $scenarioMenace)
    {
        $impact = AnalyseEvaluationCalculator::calculateImpactPotentiel($scenarioMenace);

        $labelColor = null;
        switch ($impact) {
            case VraisemblanceGraviteDictionary::NEGLIGEABLE:
                $labelColor = 'success';
                break;
            case VraisemblanceGraviteDictionary::LIMITEE:
                $labelColor = 'warning';
                break;
            case VraisemblanceGraviteDictionary::IMPORTANTE:
                $labelColor = 'danger';
                break;
            default:
                $labelColor = 'danger';
                break;
        }

        return '<span class="label label-' . $labelColor . '" style="min-width: 100%; display: inline-block;">' . VraisemblanceGraviteDictionary::getMasculineValues()[$impact] . '</span>';
    }

    public function getScenarioMenaceImpactResiduelLabel(AnalyseScenarioMenace $scenarioMenace)
    {
        $mesures = $scenarioMenace->getMesuresProtections();

//        Gr = max[(Gi - (Gi * <mG/<pG)), 0.01];
//        Vr = max[(Vi - (Vi * <mV/<pV)), 0.01];
    }

    private function getCompleteLabel($impact)
    {
        //TODO Factoriser les 2 méthodes ci-dessus
    }

    public function getCritereLabel(CriterePrincipeFondamental $critere)
    {
        $labelColor = null;
        switch ($critere->getReponse()) {
            case ReponseCritereFondamentalDictionary::REPONSE_CONFORME:
                $labelColor = 'success';
                break;
            case ReponseCritereFondamentalDictionary::REPONSE_NON_CONFORME:
                $labelColor = 'danger';
                break;
            default:
                $labelColor = 'default';
                break;
        }

        return '<span class="label label-' . $labelColor . '" style="min-width: 100%; display: inline-block;">' . ReponseCritereFondamentalDictionary::getLabelReponse($critere->getReponse()) . '</span>';
    }

    public function getLastAnalyseImpact(ConformiteTraitement $conformiteTraitement): ?AnalyseImpact
    {
        if (empty($conformiteTraitement->getAnalyseImpacts())) {
            return null;
        }
        $lastAnalyse = null;

        foreach ($conformiteTraitement->getAnalyseImpacts() as $analyseImpact) {
            if (is_null($analyseImpact->getDateValidation())) {
                return $analyseImpact;
            }
            if (is_null($lastAnalyse)) {
                $lastAnalyse = $analyseImpact;
                continue;
            }
            if ($analyseImpact->getDateValidation() > $lastAnalyse->getDateValidation()) {
                $lastAnalyse = $analyseImpact;
            }
        }

        return $lastAnalyse;
    }
}
