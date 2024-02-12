<?php

namespace App\Domain\AIPD\Form\Type;

use App\Domain\AIPD\Model\AbstractMesureProtection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnalyseMesureProtectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reponse', ReponseDictionaryType::class, [
                'aipd'        => $options['aipd'],
                'expanded'    => false,
                'placeholder' => 'Pas de réponse',
            ])
            ->add('detail', TextareaType::class, [
                'required' => true,
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
        $resolver->setRequired('aipd');

        $resolver->setDefaults([
            'data_class' => AbstractMesureProtection::class,
        ]);
    }
}
