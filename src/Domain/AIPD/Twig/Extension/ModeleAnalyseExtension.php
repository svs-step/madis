<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Twig\Extension;

use App\Domain\AIPD\Model\CriterePrincipeFondamental;
use Symfony\Component\Form\FormView;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ModeleAnalyseExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('getCriteres', [$this, 'getCriteres']),
        ];
    }

    public function getCriteres(FormView $formView)
    {
        $criteres = [];
        foreach ($formView->children  as $formViewCritere) {
            $critere = $formViewCritere->vars['value'];
            if (!$critere instanceof CriterePrincipeFondamental) {
                continue;
            }

            $criteres[] = $formViewCritere;
        }

        return $criteres;
    }
}
