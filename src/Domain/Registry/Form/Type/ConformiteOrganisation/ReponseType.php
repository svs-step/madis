<?php

namespace App\Domain\Registry\Form\Type\ConformiteOrganisation;

use App\Application\Form\Extension\SanitizeTextFormType;
use App\Domain\Registry\Model\ConformiteOrganisation\Reponse;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReponseType extends AbstractType
{
    /**
     * Build type form.
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reponse', DictionaryType::class, [
                'label'       => false,
                'name'        => 'registry_conformite_organisation_reponse',
                'expanded'    => true,
                'required'    => true,
                'placeholder' => false,
                'choice_attr' => function () {
                    return ['required' => 'required'];
                },
            ])
            ->add('reponseRaison', SanitizeTextFormType::class, [
                'label'    => false,
                'required' => false,
                'attr'     => [
                    'placeholder' => 'placeholder.precision',
                    'maxlength'   => 255,
                ],
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
                'data_class'        => Reponse::class,
                'validation_groups' => [
                    'default',
                ],
            ]);
    }
}
