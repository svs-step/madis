<?php

namespace App\Domain\Registry\Form\Type\ConformiteOrganisation;

use App\Domain\Registry\Model\ConformiteOrganisation\Evaluation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EvaluationType extends AbstractType
{
    /**
     * Build type form.
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', DateType::class, [
                'label'    => 'registry.conformite_organisation.label.date',
                'required' => true,
                'widget'   => 'single_text',
                'format'   => 'dd/MM/yyyy',
                'html5'    => false,
                'attr'     => [
                    'class' => 'datepicker',
                ],
            ])
            ->add('participants', CollectionType::class, [
                'entry_type'   => ParticipantType::class,
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->add('conformites', CollectionType::class, [
                    'entry_type'   => ConformiteType::class,
                    'by_reference' => false,
                ]
            )
            ->add('save', SubmitType::class, [
                'row_attr' => ['class' => 'btn'],
            ])
            ->add('saveDraft', SubmitType::class, [
                'validation_groups' => false,
            ])
        ;
    }

    /**
     * Provide type options.
     */
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
