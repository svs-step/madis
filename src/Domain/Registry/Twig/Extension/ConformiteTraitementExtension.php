<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2019 Soluris - Solutions Numériques Territoriales Innovantes
 * @author Donovan Bourlard <donovan@awkan.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace App\Domain\Registry\Twig\Extension;

use App\Domain\Registry\Calculator\Completion\ConformiteTraitementCompletion;
use App\Domain\Registry\Dictionary\ConformiteTraitementLevelDictionary;
use App\Domain\Registry\Model\ConformiteTraitement\ConformiteTraitement;
use App\Domain\Registry\Model\ConformiteTraitement\Reponse;
use Symfony\Component\Form\FormView;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ConformiteTraitementExtension extends AbstractExtension
{
    /**
     * @return array|TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('orderReponseByQuestionPositionAsc', [$this, 'orderReponseByQuestionPositionAsc']),
            new TwigFunction('getPlanifiedMesurements', [$this, 'getPlanifiedMesurements']),
            new TwigFunction('getConformiteLevelWeight', [$this, 'getConformiteLevelWeight']),
            new TwigFunction('getConformiteTraitementLabel', [$this, 'getConformiteTraitementLabel']),
        ];
    }

    public function orderReponseByQuestionPositionAsc(FormView $formView): array
    {
        $ordered = [];
        foreach ($formView->children as $formViewReponse) {
            $reponse = $formViewReponse->vars['value'];
            if (!$reponse instanceof Reponse) {
                continue;
            }

            $ordered[$reponse->getQuestion()->getPosition()] = $formViewReponse;
        }

        \ksort($ordered);

        return $ordered;
    }

    public function getPlanifiedMesurements(ConformiteTraitement $conformiteTraitement): array
    {
        $planifiedMesurementsToBeNotified = [];
        foreach ($conformiteTraitement->getReponses() as $reponse) {
            $mesurements = \iterable_to_array($reponse->getActionProtectionsPlanifiedNotSeens());
            foreach ($mesurements as $mesurement) {
                if (!\in_array($mesurement, $planifiedMesurementsToBeNotified)) {
                    \array_push($planifiedMesurementsToBeNotified, $mesurement);
                }
            }
        }

        return $planifiedMesurementsToBeNotified;
    }

    public function getConformiteLevelWeight(ConformiteTraitement $conformiteTraitement): int
    {
        $level = ConformiteTraitementCompletion::getConformiteTraitementLevel($conformiteTraitement);

        return ConformiteTraitementLevelDictionary::getConformitesWeight()[$level];
    }

    public function getConformiteTraitementLabel(ConformiteTraitement $conformiteTraitement): string
    {
        $level = $this->getConformiteLevelWeight($conformiteTraitement);
        $label = ConformiteTraitementLevelDictionary::getConformites()[array_flip(ConformiteTraitementLevelDictionary::getConformitesWeight())[$level]];
        switch ($level) {
            case 1:
                $color = 'green';
                break;
            case 2:
                $color = 'orange';
                break;
            default:
                $color = 'red';
        }

        return '<span class="badge bg-' . $color . '">' . $label . '</span>';
    }
}
