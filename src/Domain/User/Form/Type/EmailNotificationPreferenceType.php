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
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
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

        ;
        $hours = [];
        for ($i = 0; $i < 24; ++$i) {
            $hours[(string) $i] = $i;
        }

        $modules = [];
        foreach (EmailNotificationPreference::MODULES as $k => $module) {
            $modules['user.notifications.form.modules.'.$k] = $module;
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
            ->add('day', DailyNotificationType::class, ['mapped' => false])
            ->add('week', WeeklyNotificationType::class, ['mapped' => false])

            ->add('month', MonthlyNotificationType::class, ['mapped' => false])

            ->add('modules',ChoiceType::class, [
                'mapped' => false,
                'label'        => false,
                'required'     => false,
                'choices'      => $modules,
                'expanded'     => true,
                'multiple'     => true,
                'block_prefix' => 'wrapped_choice',
            ])
            ->addEventListener(
                FormEvents::PRE_SUBMIT,
                [$this, 'onPreSubmit']
            )
            ->addEventListener(
                FormEvents::POST_SUBMIT,
                [$this, 'onPostSubmit']
            )
            ;
    }

    public function onPostSubmit(FormEvent $event): void
    {
        /**
         * @var EmailNotificationPreference $data
         */
        $data = $event->getData();
        $form = $event->getForm();
        $mod = $form->get('modules');
        if (isset($mod)) {
            $notificationMask = array_reduce($mod->getNormData(), function($car, $el) {
                return $car | (int)$el;
            }, 0);

            $data->setNotificationMask($notificationMask);
        }

        $event->setData($data);
    }
    public function onPreSubmit(FormEvent $event): void
    {
        $data = $event->getData();

        if ($data['frequency'] === 'month') {
            $data['hour'] = $data['month']['hour'];
            $data['day'] = $data['month']['day'];
            $data['week'] = $data['month']['week'];
            unset($data['month']);
        } else if ($data['frequency'] === 'week') {
            $data['hour'] = $data['week']['hour'];
            $data['day'] = $data['week']['day'];
            unset($data['week']);
            unset($data['month']);
        }else if ($data['frequency'] === 'day') {
            $data['hour'] = (int)$data['day']['hour'];
            unset($data['week']);
            unset($data['month']);
            unset($data['day']);
        }else if ($data['frequency'] === 'hour') {
            unset($data['week']);
            unset($data['month']);
            unset($data['day']);
        } else {
            unset($data['week']);
            unset($data['month']);
            unset($data['day']);
            unset($data['hour']);
        }



        $event->setData($data);
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
