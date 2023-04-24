<?php

namespace App\Domain\AIPD\Form\Type;

use App\Domain\AIPD\Model\AbstractMesureProtection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
            ->add('detail', TextType::class, [
                'required' => true,
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
