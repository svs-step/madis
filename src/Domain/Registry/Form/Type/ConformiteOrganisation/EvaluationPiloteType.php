<?php

namespace App\Domain\Registry\Form\Type\ConformiteOrganisation;

use App\Domain\Registry\Model\ConformiteOrganisation\Evaluation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EvaluationPiloteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('conformites', CollectionType::class, [
                'entry_type' => PiloteType::class,
                'required'   => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class'        => Evaluation::class,
                'validation_groups' => [
                    'default',
                ],
            ]);
    }
}
