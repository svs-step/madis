<?php

namespace App\Domain\Registry\Form\Type\ConformiteOrganisation;

use App\Domain\Registry\Model\ConformiteOrganisation\Conformite;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PiloteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pilote', TextType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class'        => Conformite::class,
                'validation_groups' => [
                    'default',
                ],
            ]);
    }
}
