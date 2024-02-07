<?php

namespace App\Domain\Registry\Form\Type;

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
            ->add('name', TextType::class, [
                'label'      => 'registry.treatment.label.shelflife_name',
                'required'   => true,
                'empty_data' => '',
                'attr'       => [
                    'maxlength' => 255,
                ],
                'purify_html' => true,
            ])
            ->add('duration', TextType::class, [
                'label'      => 'registry.treatment.label.shelflife_duration',
                'required'   => true,
                'empty_data' => '',
                'attr'       => [
                    'maxlength' => 255,
                ],
                'purify_html' => true,
            ])
            ->add('ultimate_fate', DictionaryType::class, [
                'label'    => 'registry.treatment.label.shelflife_ultimate_fate',
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
