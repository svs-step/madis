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
            ->add('nom', TextType::class)
            ->add('nomCourt', TextType::class)
            ->add('labelLivrable', TextType::class)
            ->add('phrasePreconisation', TextType::class)
            ->add('detail', TextType::class)
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
