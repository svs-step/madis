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
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
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
                'required' => false,
            ])
            ->add('frequencies', DictionaryType::class, [
                'label'    => 'notification.notificationMailParameters.form.frequency',
                'required' => true,
                'name'     => 'notificationMailParametersFrequency',
                'multiple' => false,
                'expanded' => true,
            ])

            ->add('is_treatment', CheckboxType::class, [
                'label'    => 'notification.notificationMailParameters.form.is_treatment',
                'required' => false,
            ])

            ->add('is_subcontract', CheckboxType::class, [
                'label'    => 'notification.notificationMailParameters.form.is_subcontract',
                'required' => false,
            ])
            ->add('is_request', CheckboxType::class, [
                'label'    => 'notification.notificationMailParameters.form.is_request',
                'required' => false,
            ])
            ->add('is_violation', CheckboxType::class, [
                'label'    => 'notification.notificationMailParameters.form.is_violation',
                'required' => false,
            ])
            ->add('is_proof', CheckboxType::class, [
                'label'    => 'notification.notificationMailParameters.form.is_proof',
                'required' => false,
            ])
            ->add('is_protectAction', CheckboxType::class, [
                'label'    => 'notification.notificationMailParameters.form.is_protectAction',
                'required' => false,
            ])
            ->add('is_maturity', CheckboxType::class, [
                'label'    => 'notification.notificationMailParameters.form.is_maturity',
                'required' => false,
            ])
            ->add('is_treatmenConformity', CheckboxType::class, [
                'label'    => 'notification.notificationMailParameters.form.is_treatmenConformity',
                'required' => false,
            ])
            ->add('is_organizationConformity', CheckboxType::class, [
                'label'    => 'notification.notificationMailParameters.form.is_organizationConformity',
                'required' => false,
            ])
            ->add('is_AIPD', CheckboxType::class, [
                'label'    => 'notification.notificationMailParameters.form.is_AIPD',
                'required' => false,
            ])
            ->add('is_document', CheckboxType::class, [
                'label'    => 'notification.notificationMailParameters.form.is_document',
                'required' => false,
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
