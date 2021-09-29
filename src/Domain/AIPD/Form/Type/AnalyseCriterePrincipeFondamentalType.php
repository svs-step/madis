<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Form\Type;

use App\Domain\AIPD\Model\CriterePrincipeFondamental;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnalyseCriterePrincipeFondamentalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('reponse', DictionaryType::class, [
            'name' => 'reponse_critere_fondamental',
        ])
            ->add('justification', TextType::class, [
                'required' => false,
            ])
//            ->add('fichier', FileType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => CriterePrincipeFondamental::class,
            ]);
    }
}
