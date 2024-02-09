<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Form\Type;

use App\Domain\AIPD\Model\ModeleQuestionConformite;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModeleQuestionConformiteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('isJustificationObligatoire', CheckboxType::class, [
                'required' => false,
                'label'    => false,
            ])
            ->add('texteConformite', TextareaType::class, [
                'required' => false,
                'attr'     => [
                    'maxlength' => 1000,
                    'rows'      => 1,
                    'class'     => 'textareaheight',
                ],
                'purify_html' => true,
            ])
            ->add('texteNonConformiteMineure', TextareaType::class, [
                'required' => false,
                'attr'     => [
                    'maxlength' => 1000,
                    'rows'      => 1,
                    'class'     => 'textareaheight',
                ],
                'purify_html' => true,
            ])
            ->add('texteNonConformiteMajeure', TextareaType::class, [
                'required' => false,
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
            'data_class' => ModeleQuestionConformite::class,
        ]);
    }
}
