<?php

/**
 * This file is part of the SOLURIS - RGPD Management application.
 *
 * (c) Donovan Bourlard <donovan@awkan.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Domain\Maturity\Twig\Extension;

use Symfony\Component\Form\FormView;

class SurveyExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('orderByDomain', [$this, 'orderByDomain']),
        ];
    }

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
}
