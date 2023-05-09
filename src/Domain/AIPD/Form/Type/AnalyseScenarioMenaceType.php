<?php

namespace App\Domain\AIPD\Form\Type;

use App\Domain\AIPD\Model\AnalyseScenarioMenace;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnalyseScenarioMenaceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $form = $event->getForm();
                if ($event->getData()->isCanDicBeModified()) {
                    $form
                        ->add('isDisponibilite', CheckboxType::class, [
                            'required' => false,
                            'label'    => false,
                        ])
                        ->add('isIntegrite', CheckboxType::class, [
                            'required' => false,
                            'label'    => false,
                        ])
                        ->add('isConfidentialite', CheckboxType::class, [
                            'required' => false,
                            'label'    => false,
                        ])
                    ;
                }
            });
        $builder
            ->add('vraisemblance', DictionaryType::class, [
                'name'     => 'vraisemblance_gravite',
                'required' => true,
                'label'    => false,
                'attr'     => [
                    'class' => 'vraisemblance-dropdown',
                ],
            ])
            ->add('gravite', DictionaryType::class, [
                'name'     => 'vraisemblance_gravite',
                'required' => true,
                'label'    => false,
                'attr'     => [
                    'class' => 'gravite-dropdown',
                ],
            ])
            ->add('precisions', TextType::class, [
                'required' => false,
                'attr'     => [
                    'maxlength' => 255,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AnalyseScenarioMenace::class,
        ]);
    }
}
