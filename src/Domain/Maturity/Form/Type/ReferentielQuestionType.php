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
use App\Domain\Maturity\Model\ReferentielAnswer;
use App\Domain\Maturity\Model\ReferentielQuestion;
use App\Domain\Maturity\Model\ReferentielSection;
use App\Domain\Registry\Form\Type\ConformiteTraitement\ReponseType;
use App\Domain\Registry\Form\Type\TreatmentType;
use App\Domain\Registry\Model\ConformiteTraitement\ConformiteTraitement;
use Doctrine\DBAL\Types\IntegerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReferentielQuestionType extends AbstractType
{
    /**
     * Build type form.
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var ReferentielQuestion $referentielQuestion */
        $builder
            ->add('name', TextType::class, [
                'required' => false,
                'attr'     => [
                    'maxlength' => 255,
                ],
            ])
            ->add('weight', ChoiceType::class, [
                'label'    => 'maturity.referentiel.form.weight',
                'required' => true,
                'multiple' => false,
                'expanded' => false,
                'choices'  => [0,1,2,3,4,5],
            ])
            ->add('referentielAnswers', CollectionType::class, [
                    'label' => 'maturity.referentiel.form.answer',
                    'entry_type' => ReferentielAnswerType::class,
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
                'data_class'        => ReferentielQuestion::class,
                'validation_groups' => [
                    'default',
                ],
            ]);
    }
}
