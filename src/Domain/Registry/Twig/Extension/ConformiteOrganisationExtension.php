<?php

namespace App\Domain\Registry\Twig\Extension;

use App\Domain\Registry\Model\ConformiteOrganisation\Conformite;
use App\Domain\Registry\Model\ConformiteOrganisation\Reponse;
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
    public function getFunctions(): array
    {
        return [
            new TwigFunction('getConformitesWithProcessusAndQuestions', [$this, 'getConformitesWithProcessusAndQuestions']),
            new TwigFunction('getQuestionsOrderedByPosition', [$this, 'getQuestionsOrderedByPosition']),
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

    public function getQuestionsOrderedByPosition(FormView $formView)
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
