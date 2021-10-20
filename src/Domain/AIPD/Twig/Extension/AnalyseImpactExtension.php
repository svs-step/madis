<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Twig\Extension;

use App\Domain\AIPD\Calculator\AnalyseEvaluationCalculator;
use App\Domain\AIPD\Dictionary\VraisemblanceGraviteDictionary;
use App\Domain\AIPD\Model\AnalyseQuestionConformite;
use App\Domain\AIPD\Model\AnalyseScenarioMenace;
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
            new TwigFunction('getScenarioMenaceImpactLabel', [$this, 'getScenarioMenaceImpactLabel']),
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

    public function getScenarioMenaceImpactLabel(AnalyseScenarioMenace $scenarioMenace)
    {
        $impact = AnalyseEvaluationCalculator::calculateRisque($scenarioMenace);

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
}
