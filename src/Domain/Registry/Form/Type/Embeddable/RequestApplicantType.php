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

use App\Domain\Registry\Model\Embeddable\RequestApplicant;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RequestApplicantType extends AbstractType
{
    /**
     * Build type form.
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('civility', DictionaryType::class, [
                'label'    => 'global.label.contact.civility',
                'required' => true,
                'name'     => 'registry_request_civility',
            ])
            ->add('firstName', TextType::class, [
                'label'    => 'global.label.contact.first_name',
                'required' => true,
                'attr'     => [
                    'maxlength' => 255,
                ],
                'purify_html' => true,
            ])
            ->add('lastName', TextType::class, [
                'label'    => 'global.label.contact.last_name',
                'required' => true,
                'attr'     => [
                    'maxlength' => 255,
                ],
                'purify_html' => true,
            ])
            ->add('address', TextType::class, [
                'label'    => 'global.label.address.line_one',
                'required' => false,
                'attr'     => [
                    'maxlength' => 255,
                ],
                'purify_html' => true,
            ])
            ->add('mail', EmailType::class, [
                'label'    => 'global.label.contact.email',
                'required' => false,
            ])
            ->add('phoneNumber', TextType::class, [
                'label'       => 'global.label.contact.phone_number',
                'required'    => false,
                'purify_html' => true,
            ])
            ->add('concernedPeople', CheckboxType::class, [
                'label'    => 'registry.request.label.is_concerned_people',
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
                'data_class'        => RequestApplicant::class,
                'validation_groups' => [
                    'default',
                ],
            ]);
    }
}
