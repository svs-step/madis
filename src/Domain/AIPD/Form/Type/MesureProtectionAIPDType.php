<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Form\Type;

use App\Domain\AIPD\Model\AbstractMesureProtection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MesureProtectionAIPDType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'attr' => [
                    'maxlength' => 255,
                ],
                'purify_html' => true,
            ])
            ->add('nomCourt', TextType::class, [
                'attr' => [
                    'maxlength' => 255,
                ],
                'purify_html' => true,
            ])
            ->add('labelLivrable', TextType::class, [
                'attr' => [
                    'maxlength' => 255,
                ],
                'purify_html' => true,
            ])
            ->add('phrasePreconisation', TextType::class, [
                'attr' => [
                    'maxlength' => 255,
                ],
                'purify_html' => true,
            ])
            ->add('detail', TextType::class, [
                'attr' => [
                    'maxlength' => 255,
                ],
                'purify_html' => true,
            ])
            ->add('poidsVraisemblance', IntegerType::class, [
                'attr' => [
                    'min' => 1,
                ],
            ])
            ->add('poidsGravite', IntegerType::class, [
                'attr' => [
                    'min' => 1,
                ],
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
