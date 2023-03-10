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
            new TwigFunction('getLastAnalyseImpact', [$this, 'getLastAnalyseImpact']),
            new TwigFunction('getScenarioMenaceImpactPotentielLabel', [$this, 'getScenarioMenaceImpactPotentielLabel']),
            new TwigFunction('getScenarioMenaceImpactPotentiel', [$this, 'getScenarioMenaceImpactPotentiel']),
            new TwigFunction('getScenarioMenaceImpactResiduelLabel', [$this, 'getScenarioMenaceImpactResiduelLabel']),
            new TwigFunction('getScenarioMenaceImpactResiduel', [$this, 'getScenarioMenaceImpactResiduel']),
            new TwigFunction('getScenarioMenaceIndicateurResiduel', [$this, 'getScenarioMenaceIndicateurResiduel']),
            new TwigFunction('getMeasureImpactResiduel', [$this, 'getMeasureImpactResiduel']),
        ];
    }

    public function getFormattedActionProtectionsFromQuestion(AnalyseQuestionConformite $questionAnalyse)
    {
        $reponseConformite = $questionAnalyse->getAnalyseImpact()->getConformiteTraitement()->getReponseOfPosition($questionAnalyse->getPosition());
        $formattedString   = '';
        if (!$reponseConformite) {
            return '<a href="/conformite-traitement/editer/' . $questionAnalyse->getAnalyseImpact()->getConformiteTraitement()->getId()->toString() . '">Veuillez évaluer à nouveau la conformité du traitement</a>';
        }
        /** @var Mesurement $actionProtection */
        foreach ($reponseConformite->getActionProtections() as $actionProtection) {
            $formattedString .= $actionProtection->getName() . '<br/>';
        }

        return $formattedString;
    }

    public function getConformiteLabel(AnalyseQuestionConformite $questionAnalyse): string
    {
        $reponseConformite = $questionAnalyse->getAnalyseImpact()->getConformiteTraitement()->getReponseOfPosition($questionAnalyse->getPosition());
        if (!$reponseConformite) {
            return 'Inconnu';
        }
        if ($reponseConformite->isConforme()) {
            return '<span class="label label-success" style="min-width: 100%; display: inline-block;">Conforme</span>';
        } elseif (!$reponseConformite->getActionProtections()->isEmpty()) {
            return '<span class="label label-warning" style="min-width: 100%; display: inline-block;">Non-conforme mineure</span>';
        }

        return '<span class="label label-danger" style="min-width: 100%; display: inline-block;">Non-conforme majeure</span>';
    }

    public function getScenarioMenaceImpactPotentiel(AnalyseScenarioMenace $scenarioMenace)
    {
        return AnalyseEvaluationCalculator::calculateImpactPotentiel($scenarioMenace);
    }

    public function getScenarioMenaceImpactResiduel(AnalyseScenarioMenace $scenarioMenace)
    {
        return AnalyseEvaluationCalculator::calculateImpactResiduel($scenarioMenace);
    }

    public function getScenarioMenaceImpactPotentielLabel(AnalyseScenarioMenace $scenarioMenace): string
    {
        $impact = VraisemblanceGraviteDictionary::getImpact($this->getScenarioMenaceImpactPotentiel($scenarioMenace));

        return $this->getImpactLabel($impact);
    }

    public function getScenarioMenaceImpactResiduelLabel(AnalyseScenarioMenace $scenarioMenace): string
    {
        $impact = VraisemblanceGraviteDictionary::getImpact($this->getScenarioMenaceImpactResiduel($scenarioMenace));

        return $this->getImpactLabel($impact);
    }

    public function isScenarioMenaceImpactResiduelImpactNotNegligeable(AnalyseScenarioMenace $scenarioMenace): bool
    {
        $impact = VraisemblanceGraviteDictionary::getImpact($this->getScenarioMenaceImpactResiduel($scenarioMenace));

        return $impact !== VraisemblanceGraviteDictionary::NEGLIGEABLE ;
    }

    private function getImpactLabel(string $impact): string
    {
        $labelColor = null;
        switch ($impact) {
            case VraisemblanceGraviteDictionary::NEGLIGEABLE:
                $labelColor = 'success';
                break;
            case VraisemblanceGraviteDictionary::LIMITEE:
                $labelColor = 'warning';
                break;
            case VraisemblanceGraviteDictionary::IMPORTANTE:
                $labelColor = 'default';
                break;
            default:
                $labelColor = 'danger';
                break;
        }

        return '<span class="label label-' . $labelColor . '" style="min-width: 100%; display: inline-block;' . ('default' == $labelColor ? 'background:#605CA8; color:white;' : '') . '">' . VraisemblanceGraviteDictionary::getMasculineValues()[$impact] . '</span>';
    }

    public function getScenarioMenaceIndicateurResiduel(AnalyseScenarioMenace $scenarioMenace, string $poidsType)
    {
        if ('vraisemblance' === $poidsType) {
            $indicateurInitial = VraisemblanceGraviteDictionary::getPoidsFromImpact($scenarioMenace->getVraisemblance());
        } else {
            $indicateurInitial = VraisemblanceGraviteDictionary::getPoidsFromImpact($scenarioMenace->getGravite());
        }

        return AnalyseEvaluationCalculator::calculateIndicateurResiduel($scenarioMenace, $indicateurInitial, $poidsType);
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
            /* Une seule analyse d'impact peut être sans date de validation, car on ne peut en commencer une nouvelle
            tant que l'actuelle n'a pas été validé. S'il n'y a pas de date, cela indique donc que c'est la dernière analyse */
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

    public static function getMeasureImpactResiduel($gravite, $vraisemblance)
    {
        return AnalyseEvaluationCalculator::getImpactFromGraviteAndVraisemblance($gravite, $vraisemblance);
    }
}
