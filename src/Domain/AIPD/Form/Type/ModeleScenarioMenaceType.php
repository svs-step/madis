<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Form\Type;

use App\Domain\AIPD\Model\MesureProtection;
use App\Domain\AIPD\Model\ModeleScenarioMenace;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModeleScenarioMenaceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => false,
            ])
            ->add('mesuresProtections', EntityType::class, [
                'required' => false,
                'label'    => false,
                'multiple' => true,
                'expanded' => false,
                'class'    => MesureProtection::class,
                'attr'     => [
                    'class' => 'selectpicker',
                    'title' => 'placeholder.multiple_select',
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
                'name'     => 'vraisemblance_gravite',
                'label'    => false,
            ])
            ->add('gravite', DictionaryType::class, [
                'required' => true,
                'name'     => 'vraisemblance_gravite',
                'label'    => false,
            ])
            ->add('precisions', TextType::class, [
                'required' => false,
                'label'    => false,
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
