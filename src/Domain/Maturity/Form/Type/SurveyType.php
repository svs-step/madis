<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2019 Soluris - Solutions Numériques Territoriales Innovantes
 * @author Donovan Bourlard <donovan@awkan.fr>
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

use App\Domain\Maturity\Model;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SurveyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Model\Referentiel $ref */
        $ref = $options['data']->getReferentiel();

        $builder
            ->add('referentiel', EntityType::class, [
                'class'      => Model\Referentiel::class,
                'empty_data' => $ref,
            ])
            ->add('answers', EntityType::class, [
                'multiple'      => true,
                'class'         => Model\Answer::class,
                'choice_label'  => 'name',
                'query_builder' => function (EntityRepository $er) use ($ref) {
                    return $er->createQueryBuilder('a')
                        ->leftJoin('a.question', 'q')
                        ->leftJoin('q.domain', 'd')
                        ->where('d.referentiel = :referentiel')
                        ->orderBy('a.position', Criteria::ASC)
                        ->setParameter('referentiel', $ref)
                    ;
                },
            ])

        ;
    }

    /**
     * Provide type options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class'        => Model\Survey::class,
                'validation_groups' => [
                    'default',
                    'survey',
                ],
            ])
        ;
    }
}
