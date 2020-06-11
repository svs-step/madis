<?php

namespace App\Domain\Registry\Twig\Extension;

use App\Domain\Registry\Model\ConformiteOrganisation\Conformite;
use App\Domain\Registry\Model\ConformiteOrganisation\Evaluation;
use App\Domain\Registry\Repository\ConformiteOrganisation\Processus;
use Symfony\Component\Form\FormView;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ConformiteOrganisationExtension extends AbstractExtension
{
    /**
     * @var Processus
     */
    private $processusRepository;

    public function __construct(Processus $processusRepository)
    {
        $this->processusRepository = $processusRepository;
    }

    /**
     * @return array|TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('getConformitesWithProcessusAndQuestions', [$this, 'getConformitesWithProcessusAndQuestions']),
            new TwigFunction('getConformitesOrderedByPosition', [$this, 'getConformitesOrderedByPosition']),
        ];
    }

    public function getConformitesWithProcessusAndQuestions(FormView $formView)
    {
        $ordered = [];
        foreach ($formView->children as $formViewReponse) {
            $conformite = $formViewReponse->vars['value'];
            if (!$conformite instanceof Conformite) {
                continue;
            }

            $ordered[$conformite->getProcessus()->getPosition()][] = $formViewReponse;
        }

        \ksort($ordered);

        return $ordered;
    }

    /**
     * Return conformites ordered by their processus position.
     */
    public function getConformitesOrderedByPosition(Evaluation $evaluation): array
    {
        $orderedConformites = [];
        foreach ($evaluation->getConformites() as $conformite) {
            $orderedConformites[$conformite->getProcessus()->getPosition()] = $conformite;
        }
        \ksort($orderedConformites);

        return $orderedConformites;
    }
}
