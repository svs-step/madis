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

namespace App\Domain\Maturity\Twig\Extension;

use App\Domain\Maturity\Model\Answer;
use Symfony\Component\Form\FormView;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SurveyExtension extends AbstractExtension
{
    /**
     * @return array|TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('orderByDomain', [$this, 'orderByDomain']),
            new TwigFunction('orderAnswersByQuestionNameAsc', [$this, 'orderAnswersByQuestionNameAsc']),
        ];
    }

    /**
     * Order formView answers by domain.
     * Then, every domains must be ordered by position.
     *
     * @param FormView $formView
     *
     * @return array
     */
    public function orderByDomain(FormView $formView): array
    {
        $answersByDomain = [];
        foreach ($formView as $answer) {
            $object = $answer->vars['value'];
            if (!\array_key_exists($object->getQuestion()->getDomain()->getPosition(), $answersByDomain)) {
                $answersByDomain[$object->getQuestion()->getDomain()->getPosition()]['name']  = $object->getQuestion()->getDomain()->getName();
                $answersByDomain[$object->getQuestion()->getDomain()->getPosition()]['color'] = $object->getQuestion()->getDomain()->getColor();
            }
            $answersByDomain[$object->getQuestion()->getDomain()->getPosition()]['answers'][] = $answer;
        }

        \ksort($answersByDomain);

        return $answersByDomain;
    }

    /**
     * Order answers by question name asc.
     *
     * @param FormView[] $formViews
     *
     * @return array
     */
    public function orderAnswersByQuestionNameAsc(array $formViews): array
    {
        $ordered = [];
        foreach ($formViews as $formView) {
            $answer = $formView->vars['value'];
            if (!$answer instanceof Answer) {
                continue;
            }

            $ordered[$answer->getQuestion()->getName()] = $formView;
        }

        \ksort($ordered);

        return $ordered;
    }
}
