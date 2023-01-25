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
use App\Domain\Maturity\Model\ReferentielSection;
use App\Domain\Registry\Form\Type\ConformiteTraitement\ReponseType;
use App\Domain\Registry\Form\Type\TreatmentType;
use App\Domain\Registry\Model\ConformiteTraitement\ConformiteTraitement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReferentielSectionType extends AbstractType
{
    /**
     * Build type form.
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //** @var ReferentielSection $referentielSection */
        //$referentielSection = $options['data'];
        $builder
            ->add('name', TextType::class, [
                'label'    => 'maturity.referentiel.form.name',
                'required' => true,
                'attr'     => [
                    'maxlength' => 255,
                ],
            ])
            ->add('description', TextareaType::class, [
                'label'    => 'maturity.referentiel.form.description',
                'required' => false,
                'attr'     => [
                    'rows' => 3,
                ],
            ])

            ->add('referentielQuestions', CollectionType::class, [
                    'entry_type' => ReferentielQuestionType::class,
                ]
            )
        ;
    }

    /**
     * Provide type options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class'        => ReferentielSection::class,
                'validation_groups' => [
                    'default',
                ],
            ]);
    }
}
