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
            new \Twig_SimpleFunction('getDomainsColor', [$this, 'getDomainsColor']),
        ];
    }

    public function orderByDomain(FormView $formView): array
    {
        $answersByDomain = [];
        foreach ($formView as $answer) {
            $object                                                            = $answer->vars['value'];
            $answersByDomain[$object->getQuestion()->getDomain()->getName()][] = $answer;
        }

        return $answersByDomain;
    }

    public function getDomainsColor(FormView $formView): array
    {
        $colorByDomain = [];
        foreach ($formView as $answer) {
            $object                            = $answer->vars['value'];
            $domain                            = $object->getQuestion()->getDomain();
            $colorByDomain[$domain->getName()] = $domain->getColor();
        }

        return $colorByDomain;
    }
}
