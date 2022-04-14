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

namespace App\Domain\Notification\Form\Type;

use App\Domain\Notification\Model\NotificationMailParameters;
use App\Domain\User\Model\Embeddable\Address;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NotificationMailParametersType extends AbstractType
{
    /**
     * Build type form.
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('is_notified', CheckboxType::class, [
                'label'    => 'notification.notificationMailParameters.form.is_notified',
            ])
            ->add('frequency', TextType::class, [
                'label'    => 'notification.notificationMailParameters.form.frequency',
                'required' => false,
            ])
            ->add('interval_hours', TextType::class, [
                'label'    => 'notification.notificationMailParameters.form.interval_hours',
                'required' => false,
            ])
            ->add('start_week', TextType::class, [
                'label'    => 'notification.notificationMailParameters.form.start_week',
                'required' => false,
            ])
            ->add('start_day', TextType::class, [
                'label'    => 'notification.notificationMailParameters.form.start_day',
                'required' => false,
            ])
            ->add('start_hour', NumberType::class, [
                'label'    => 'notification.notificationMailParameters.form.start_hour',
                'required' => false,
            ])
            ->add('is_treatment', CheckboxType::class, [
                'label'    => 'notification.notificationMailParameters.form.is_treatment',
            ])

            ->add('is_subcontract', CheckboxType::class, [
                'label'    => 'notification.notificationMailParameters.form.is_subcontract',
            ])
            ->add('is_request', CheckboxType::class, [
                'label'    => 'notification.notificationMailParameters.form.is_request',
            ])
            ->add('is_violation', CheckboxType::class, [
                'label'    => 'notification.notificationMailParameters.form.is_violation',
            ])
            ->add('is_proof', CheckboxType::class, [
                'label'    => 'notification.notificationMailParameters.form.is_proof',
            ])
            ->add('is_protectAction', CheckboxType::class, [
                'label'    => 'notification.notificationMailParameters.form.is_protectAction',
            ])
            ->add('is_maturity', CheckboxType::class, [
                'label'    => 'notification.notificationMailParameters.form.is_maturity',
            ])
            ->add('is_treatmenConformity', CheckboxType::class, [
                'label'    => 'notification.notificationMailParameters.form.is_treatmenConformity',
            ])
            ->add('is_organizationConformity', CheckboxType::class, [
                'label'    => 'notification.notificationMailParameters.form.is_organizationConformity',
            ])
            ->add('is_AIPD', CheckboxType::class, [
                'label'    => 'notification.notificationMailParameters.form.is_AIPD',
            ])
            ->add('is_document', CheckboxType::class, [
                'label'    => 'notification.notificationMailParameters.form.is_document',
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
                'data_class'        => NotificationMailParameters::class,
                'validation_groups' => 'default',
            ]);
    }
}
