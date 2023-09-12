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

use App\Application\Form\Extension\SanitizeTextFormType;
use App\Domain\Registry\Model\Embeddable\RequestConcernedPeople;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RequestConcernedPeopleType extends AbstractType
{
    /**
     * Build type form.
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('civility', DictionaryType::class, [
                'label'    => 'registry.request_concerned_people.form.civility',
                'required' => false,
                'name'     => 'registry_request_civility',
            ])
            ->add('firstName', SanitizeTextFormType::class, [
                'label'    => 'registry.request_concerned_people.form.first_name',
                'required' => false,
                'attr'     => [
                    'maxlength' => 255,
                ],
            ])
            ->add('lastName', SanitizeTextFormType::class, [
                'label'    => 'registry.request_concerned_people.form.last_name',
                'required' => false,
                'attr'     => [
                    'maxlength' => 255,
                ],
            ])
            ->add('address', SanitizeTextFormType::class, [
                'label'    => 'registry.request_concerned_people.form.address',
                'required' => false,
                'attr'     => [
                    'maxlength' => 255,
                ],
            ])
            ->add('mail', EmailType::class, [
                'label'    => 'registry.request_concerned_people.form.mail',
                'required' => false,
            ])
            ->add('phoneNumber', SanitizeTextFormType::class, [
                'label'    => 'registry.request_concerned_people.form.phone_number',
                'required' => false,
            ])
            ->add('linkWithApplicant', SanitizeTextFormType::class, [
                'label'    => 'registry.request_concerned_people.form.link_with_applicant',
                'required' => false,
                'attr'     => [
                    'maxlength' => 255,
                ],
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
                'data_class'        => RequestConcernedPeople::class,
                'validation_groups' => [
                    'default',
                ],
            ]);
    }
}
