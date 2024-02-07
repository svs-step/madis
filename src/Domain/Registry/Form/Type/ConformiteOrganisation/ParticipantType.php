<?php

namespace App\Domain\Registry\Form\Type\ConformiteOrganisation;

use App\Domain\Registry\Model\ConformiteOrganisation\Participant;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('prenom', TextType::class, [
                'label'    => 'global.label.contact.first_name',
                'required' => true,
                'attr'     => [
                    'maxlength' => 255,
                ],
                'purify_html' => true,
            ])
            ->add('nomDeFamille', TextType::class, [
                'label'    => 'global.label.contact.last_name',
                'required' => true,
                'attr'     => [
                    'maxlength' => 255,
                ],
                'purify_html' => true,
            ])
            ->add('civilite', DictionaryType::class, [
                'label'    => 'global.label.contact.civility',
                'required' => true,
                'name'     => 'user_contact_civility',
            ])
            ->add('fonction', TextType::class, [
                'label'    => 'global.label.contact.job',
                'required' => true,
                'attr'     => [
                    'maxlength' => 255,
                ],
                'purify_html' => true,
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
                'data_class'        => Participant::class,
                'validation_groups' => [
                    'default',
                ],
            ]);
    }
}
