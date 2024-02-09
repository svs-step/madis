<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Form\Type;

use App\Domain\AIPD\Model\ModeleMesureProtection;
use App\Domain\AIPD\Model\ModeleScenarioMenace;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModeleScenarioMenaceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextareaType::class, [
                'label' => false,
                'attr'  => [
                    'maxlength' => 1000,
                    'rows'      => 1,
                    'class'     => 'textareaheight',
                ],
                'purify_html' => true,
            ])
            ->add('mesuresProtections', EntityType::class, [
                'required' => false,
                'label'    => false,
                'multiple' => true,
                'expanded' => false,
                'class'    => ModeleMesureProtection::class,
                'attr'     => [
                    'class'            => 'selectpicker',
                    'data-live-search' => 'true',
                    'title'            => 'global.placeholder.multiple_select',
                ],
            ])
            ->add('isVisible', CheckboxType::class, [
                'required' => false,
                'label'    => false,
            ])
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
            ->add('vraisemblance', DictionaryType::class, [
                'required' => true,
                'name'     => 'modele_vraisemblance_gravite',
                'label'    => false,
            ])
            ->add('gravite', DictionaryType::class, [
                'required' => true,
                'name'     => 'modele_vraisemblance_gravite',
                'label'    => false,
            ])
            ->add('precisions', TextareaType::class, [
                'required' => false,
                'label'    => false,
                'attr'     => [
                    'maxlength' => 1000,
                    'rows'      => 1,
                    'class'     => 'textareaheight',
                ],
                'purify_html' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ModeleScenarioMenace::class,
        ]);
    }
}
