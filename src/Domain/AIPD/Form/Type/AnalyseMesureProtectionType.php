<?php

namespace App\Domain\AIPD\Form\Type;

use App\Domain\AIPD\Model\AbstractMesureProtection;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnalyseMesureProtectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reponse', DictionaryType::class, [
                'name'        => 'reponse_mesure_protection',
                'expanded'    => false,
                'placeholder' => 'Pas de réponse',
            ])
            ->add('detail', TextType::class, [
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AbstractMesureProtection::class,
        ]);
    }
}
