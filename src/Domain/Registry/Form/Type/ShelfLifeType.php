<?php

namespace App\Domain\Registry\Form\Type;

use App\Application\Form\Extension\SanitizeTextFormType;
use App\Domain\Registry\Model\ShelfLife;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShelfLifeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', SanitizeTextFormType::class, [
                'label'    => 'registry.treatment.form.shelflife_name',
                'required' => true,
                'attr'     => [
                    'maxlength' => 255,
                ],
            ])
            ->add('duration', SanitizeTextFormType::class, [
                'label'    => 'registry.treatment.form.shelflife_duration',
                'required' => true,
                'attr'     => [
                    'maxlength' => 255,
                ],
            ])
            ->add('ultimate_fate', DictionaryType::class, [
                'label'    => 'registry.treatment.form.shelflife_ultimate_fate',
                'required' => true,
                'name'     => 'registry_treatment_ultimate_fate',
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
                'data_class'        => ShelfLife::class,
                'validation_groups' => [
                    'default',
                ],
            ]);
    }
}
