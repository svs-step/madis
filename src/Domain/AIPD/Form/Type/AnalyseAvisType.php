<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Form\Type;

use App\Domain\AIPD\Model\AnalyseImpact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnalyseAvisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('avisReferent', AnalyseSingleAvisType::class)
            ->add('avisDpd', AnalyseSingleAvisType::class)
            ->add('avisRepresentant', AnalyseSingleAvisType::class)
            ->add('avisResponsable', AnalyseSingleAvisType::class)
            ->add('saveDraft', SubmitType::class, [
                'label' => 'Enregistrer un brouillon',
                'attr'  => [
                    'class' => 'btn btn-info',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           'data_class' => AnalyseImpact::class,
        ]);
    }
}
