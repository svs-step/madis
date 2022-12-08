<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Form\Type;

use App\Domain\AIPD\Dictionary\BaseCriterePrincipeFondamental;
use App\Domain\AIPD\Model\CriterePrincipeFondamental;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class AnalyseImpactType extends AbstractType
{
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        switch ($options['flow_step']) {
            case 1:

                usort($view['criterePrincipeFondamentaux']->children, function (FormView $a, FormView $b) {
                    /** @var CriterePrincipeFondamental $objectA */
                    $objectA = $a->vars['data'];
                    /** @var CriterePrincipeFondamental $objectB */
                    $objectB = $b->vars['data'];

                    $order = \array_flip(array_keys(BaseCriterePrincipeFondamental::getBaseCritere()));

                    $posA = $objectA->getCode() && in_array($objectA->getCode(), $order) ? $order[$objectA->getCode()] : 0;
                    $posB = $objectB->getCode() && in_array($objectB->getCode(), $order) ? $order[$objectB->getCode()] : 0;

                    if ($posA == $posB) {
                        return 0;
                    }

                    return ($posA < $posB) ? -1 : 1;
                });
                break;
        }
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        switch ($options['flow_step']) {
            case 1:
                $builder
                    ->add('criterePrincipeFondamentaux', CollectionType::class, [
                        'entry_type' => AnalyseCriterePrincipeFondamentalType::class,
                        'required'   => true,
                    ]);
                break;
            case 2:
                $builder
                    ->add('questionConformites', CollectionType::class, [
                        'entry_type' => AnalyseQuestionConformiteType::class,
                        'required'   => true,
                    ]);
                break;
            case 3:
                $builder
                    ->add('scenarioMenaces', CollectionType::class, [
                        'entry_type' => AnalyseScenarioMenaceType::class,
                    ]);
                break;
            case 4:
                $builder
                    ->add('mesureProtections', CollectionType::class, [
                        'entry_type'    => AnalyseMesureProtectionType::class,
                        'entry_options' => [
                            'aipd' => $options['data'],
                        ],
                    ]);
                break;
        }
    }
}
