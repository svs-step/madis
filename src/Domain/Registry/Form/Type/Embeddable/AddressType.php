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

namespace App\Domain\Registry\Form\Type\Embeddable;

use App\Domain\Registry\Model\Embeddable\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    /**
     * Build type form.
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $required = \in_array('contractor', $options['validation_groups']);

        $builder
            ->add('lineOne', TextType::class, [
                'label'    => 'global.label.address.line_one',
                'required' => $required,
                'attr'     => [
                    'maxlength' => 255,
                ],
                'purify_html' => true,
            ])
            ->add('lineTwo', TextType::class, [
                'label'    => 'global.label.address.line_two',
                'required' => false,
                'attr'     => [
                    'maxlength' => 255,
                ],
                'purify_html' => true,
            ])
            ->add('city', TextType::class, [
                'label'    => 'global.label.address.city',
                'required' => $required,
                'attr'     => [
                    'maxlength' => 255,
                ],
                'purify_html' => true,
            ])
            ->add('zipCode', TextType::class, [
                'label'    => 'global.label.address.zip_code',
                'required' => $required,
                'attr'     => [
                    'maxlength' => 5,
                ],
                'purify_html' => true,
            ])
            ->add('mail', EmailType::class, [
                'label'    => 'global.label.contact.email',
                'required' => $required,
                'attr'     => [
                    'maxlength' => 255,
                ],
            ])
            ->add('phoneNumber', TextType::class, [
                'label'       => 'global.label.contact.phone_number',
                'required'    => $required,
                'purify_html' => true,
            ])
            ->add('country', TextType::class, [
            'label'    => 'global.label.address.country',
            'required' => $required,
            'attr'     => [
                'maxlength' => 255,
            ],
            'purify_html' => true,
        ]);
    }

    /**
     * Provide type options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class'        => Address::class,
                'validation_groups' => [
                    'default',
                ],
            ]);
    }
}
