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
}
