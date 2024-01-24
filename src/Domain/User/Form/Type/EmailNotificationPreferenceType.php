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
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
            'label'    => false,
            'required' => true,
            'choices'  => [
                'notifications.label.frequency.none'    => EmailNotificationPreference::FREQUENCY_NONE,
                'notifications.label.frequency.each'    => EmailNotificationPreference::FREQUENCY_EACH,
                'notifications.label.frequency.hourly'  => EmailNotificationPreference::FREQUENCY_HOUR,
                'notifications.label.frequency.dayly'   => EmailNotificationPreference::FREQUENCY_DAY,
                'notifications.label.frequency.weekly'  => EmailNotificationPreference::FREQUENCY_WEEK,
                'notifications.label.frequency.monthly' => EmailNotificationPreference::FREQUENCY_MONTH,
            ],
            'choice_attr' => [
                'notifications.label.frequency.none'    => ['class' => 'select-frequency'],
                'notifications.label.frequency.each'    => ['class' => 'select-frequency'],
                'notifications.label.frequency.hourly'  => ['class' => 'select-frequency'],
                'notifications.label.frequency.dayly'   => ['class' => 'select-frequency'],
                'notifications.label.frequency.weekly'  => ['class' => 'select-frequency'],
                'notifications.label.frequency.monthly' => ['class' => 'select-frequency'],
            ],
            'expanded' => true,
            'multiple' => false,
        ])

        ;
        $hours = [];
        for ($i = 0; $i < 24; ++$i) {
            $hours[(string) $i] = $i;
        }

        $modules = [];
        foreach (EmailNotificationPreference::MODULES as $k => $module) {
            $modules['notifications.label.modules.' . $k] = $module;
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
                'label'    => 'à',
                'required' => true,
                'choices'  => [
                    'Lundi'    => 1,
                    'Mardi'    => 2,
                    'Mercredi' => 3,
                    'Jeudi'    => 4,
                    'Vendredi' => 5,
                    'Samedi'   => 6,
                    'Dimanche' => 7,
                ],
                'expanded'     => false,
                'multiple'     => false,
                'block_prefix' => 'wrapped_choice',
            ])
            ->add('week', ChoiceType::class, [
                'label'    => '',
                'required' => true,
                'choices'  => [
                    'Premier'   => 1,
                    'Second'    => 2,
                    'Troisième' => 3,
                    'Quatrième' => 4,
                ],
                'expanded'     => false,
                'multiple'     => false,
                'block_prefix' => 'wrapped_choice',
            ])

            ->add('notificationMask', ChoiceType::class, [
                'mapped'       => true,
                'label'        => 'notifications.label.email_modules',
                'required'     => false,
                'choices'      => $modules,
                'expanded'     => true,
                'multiple'     => true,
                'block_prefix' => 'wrapped_choice',
            ])
        ;

        $builder->get('notificationMask')->addModelTransformer(new CallbackTransformer(
            function ($mask) {
                // transform the bitmask to an array
                $modules = EmailNotificationPreference::MODULES;

                $ret = [];
                foreach ($modules as $k => $module) {
                    if ($module & $mask) {
                        $ret[$k] = $module;
                    }
                }

                return $ret;
            },
            function ($modules) {
                // transform the array to a bitmask
                return array_reduce($modules, function ($car, $el) {
                    return $car | (int) $el;
                }, 0);
            }
        ));
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
