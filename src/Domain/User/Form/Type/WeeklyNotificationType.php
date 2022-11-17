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

namespace App\Domain\User\Form\Type;

use App\Domain\User\Model\EmailNotificationPreference;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmailNotificationPreferenceType extends AbstractType
{
    /**
     * Build type form.
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('frequency', ChoiceType::class, [
            'label'    => 'user.notifications.form.frequency.label',
            'required' => true,
            'choices'  => [
                'user.notifications.form.frequency.none'  => 'none',
                'user.notifications.form.frequency.each'  => 'each',
                'user.notifications.form.frequency.hourly'  => 'hour',
                'user.notifications.form.frequency.dayly'   => 'day',
                'user.notifications.form.frequency.weekly'  => 'week',
                'user.notifications.form.frequency.monthly' => 'month',
            ],
            'expanded' => true,
            'multiple' => false,
        ])
            ->add('each', HiddenType::class)
            ->add('none', HiddenType::class)
        ;
        $hours = [];
        for ($i = 0; $i < 24; ++$i) {
            $hours[(string) $i] = $i;
        }
        $builder
            ->add('hour', ChoiceType::class, [
                'label'        => 'heures',
                'required'     => true,
                'choices'      => $hours,
                'expanded'     => false,
                'multiple'     => false,
                'block_prefix' => 'wrapped_choice',
            ])
            ->add('day', ChoiceType::class, [
                'label'        => 'h',
                'required'     => true,
                'choices'      => $hours,
                'expanded'     => false,
                'multiple'     => false,
                'block_prefix' => 'wrapped_choice',
            ])
            ->add('week', ChoiceType::class, [
                'label'        => 'h',
                'required'     => true,
                'choices'      => ['Lundi' => 1, 'Mardi' => 2],
                'expanded'     => false,
                'multiple'     => false,
                'block_prefix' => 'wrapped_choice',
            ])

            ->add('month', HiddenType::class)

            ;
    }

    /**
     * Provide type options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class'        => EmailNotificationPreference::class,
                'validation_groups' => [
                    'default',
                    'user',
                ],
            ]);
    }
}
