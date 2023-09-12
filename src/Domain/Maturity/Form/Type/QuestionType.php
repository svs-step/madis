<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2019 Soluris - Solutions Numériques Territoriales Innovantes
 * @author ANODE <contact@agence-anode.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace App\Domain\Maturity\Form\Type;

use App\Domain\Maturity\Model\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionType extends AbstractType
{
    /**
     * Build type form.
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /* @var Question $question */
        $builder
            ->add('name', TextType::class, [
                'label'    => 'maturity.referentiel.form.question_name',
                'required' => true,
                'attr'     => [
                    'maxlength' => 1000,
                ],
                'purify_html' => true,
            ])
            ->add('weight', ChoiceType::class, [
                'label'    => 'maturity.referentiel.form.weight',
                'required' => true,
                'multiple' => false,
                'expanded' => false,
                'choices'  => [0, 1, 2, 3, 4, 5],
            ])
            ->add('position', HiddenType::class, [
                'required' => false,
                'attr'     => [
                    'class' => 'question-position',
                ],
            ])
            ->add('optional', CheckboxType::class, [
                'label'    => "Activer l'option : Non concerné",
                'required' => false,
            ])
            ->add('optionReason', TextType::class, [
                'label'    => false,
                'required' => false,
                'attr'     => [
                    'placeholder' => 'Précisez',
                    'maxlength'   => 1000,
                ],
                'purify_html' => true,
            ])
            ->add('answers', CollectionType::class, [
                    'label'          => 'maturity.referentiel.form.answer',
                    'entry_type'     => AnswerType::class,
                    'required'       => false,
                    'allow_add'      => true,
                    'allow_delete'   => true,
                    'by_reference'   => false,
                    'prototype_name' => '__answer_name__',
                ]
            )
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            if (null !== $data = $event->getData()) {
                $form->add('addAnswer', ButtonType::class, [
                    'label'      => '<i class="fa fa-plus"></i> Ajouter une réponse',
                    'label_html' => true,
                    'attr'       => [
                        'class'                        => 'add_answer btn btn-primary',
                        'data-question'                => ($data ? $data->getId() : ''),
                        'data-collection-holder-class' => 'referentielAnswers',
                    ],
                ]);
            } else {
                $form->add('addAnswer', ButtonType::class, [
                    'label'      => '<i class="fa fa-plus"></i> Ajouter une réponse',
                    'label_html' => true,
                    'attr'       => [
                        'class'                        => 'add_answer btn btn-primary',
                        'data-question'                => '9999',
                        'data-collection-holder-class' => 'referentielAnswers',
                    ],
                ]);
            }
        })
        ;
    }

    /**
     * Provide type options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class'        => Question::class,
                'validation_groups' => [
                    'default',
                ],
            ]);
    }
}
