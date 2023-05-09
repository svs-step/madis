<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Form\Type;

use App\Domain\AIPD\Model\AbstractMesureProtection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class MesureProtectionAIPDType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'attr'     => [
                    'maxlength' => 255,
                ],
            ])
            ->add('nomCourt', TextType::class, [
                'attr'     => [
                    'maxlength' => 255,
                ],
            ])
            ->add('labelLivrable', TextType::class, [
                'attr'     => [
                    'maxlength' => 255,
                ],
            ])
            ->add('phrasePreconisation', TextType::class, [
                'attr'     => [
                    'maxlength' => 255,
                ],
            ])
            ->add('detail', TextType::class, [
                'attr'     => [
                    'maxlength' => 255,
                ],
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
