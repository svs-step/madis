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

use App\Domain\Maturity\Model\Domain;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DomainType extends AbstractType
{
    /**
     * Build type form.
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label'    => 'maturity.referentiel.label.section',
                'required' => true,
                'attr'     => [
                    'maxlength' => 1000,
                ],
                'purify_html' => true,
            ])

            ->add('description', TextareaType::class, [
                'label'    => 'maturity.referentiel.label.description',
                'required' => false,
                'attr'     => [
                    'rows' => 3,
                ],
                'purify_html' => true,
            ])

            ->add('questions', CollectionType::class, [
                'entry_type'     => QuestionType::class,
                'allow_add'      => true,
                'allow_delete'   => true,
                'by_reference'   => false,
                'prototype_name' => '__question_name__',
            ])

            ->add('position', HiddenType::class, [
                'required' => true,
                'attr'     => [
                    'class' => 'domain-position',
                ],
            ])
            ->add('color', HiddenType::class, [
                'required' => false,
                'attr'     => [
                    'class' => 'domain-color',
                ],
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            if (null !== $data = $event->getData()) {
                $form->add('addQuestion', ButtonType::class, [
                    'label'      => 'maturity.referentiel.action.add_question',
                    'label_html' => true,
                    'attr'       => [
                        'class'                        => 'add_question btn btn-primary',
                        'data-section'                 => ($data ? $data->getId() : ''),
                        'data-collection-holder-class' => 'referentielQuestions',
                    ],
                ]);
            } else {
                $form->add('addQuestion', ButtonType::class, [
                    'label'      => 'maturity.referentiel.action.add_question',
                    'label_html' => true,
                    'attr'       => [
                        'class'                        => 'add_question btn btn-primary',
                        'data-section'                 => '__section_name__',
                        'data-collection-holder-class' => 'referentielQuestions',
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
                'data_class'        => Domain::class,
                'validation_groups' => [
                    'default',
                ],
            ]);
    }
}
