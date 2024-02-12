<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Form\Type;

use App\Domain\AIPD\Model\AnalyseAvis;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnalyseSingleAvisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', DateType::class, [
                'required' => true,
                'label'    => false,
                'widget'   => 'single_text',
                'format'   => 'dd/MM/yyyy',
                'html5'    => false,
                'attr'     => [
                    'class' => 'datepicker',
                ],
            ])
            ->add('reponse', DictionaryType::class, [
                'name'     => 'reponse_avis',
                'required' => true,
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
        $resolver->setDefaults([
           'data_class' => AnalyseAvis::class,
        ]);
    }
}
