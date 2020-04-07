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

use App\Domain\User\Model\Embeddable\Contact;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    /**
     * Build type form.
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $intersectIsEmpty = empty(\array_intersect(
            [
                'collectivity_legal_manager',
                'collectivity_referent',
                'collectivity_comite_il_contact',
            ],
            $options['validation_groups'] ?? []
        ));

        $required = $intersectIsEmpty ? false : true;

        $builder
            ->add('civility', DictionaryType::class, [
                'label'    => 'user.contact.form.civility',
                'required' => $required,
                'name'     => 'user_contact_civility',
            ])
            ->add('firstName', TextType::class, [
                'label'    => 'user.contact.form.first_name',
                'required' => $required,
                'attr'     => [
                    'maxlength' => 255,
                ],
            ])
            ->add('lastName', TextType::class, [
                'label'    => 'user.contact.form.last_name',
                'required' => $required,
                'attr'     => [
                    'maxlength' => 255,
                ],
            ])
            ->add('job', TextType::class, [
                'label'    => 'user.contact.form.job',
                'required' => $required,
                'attr'     => [
                    'maxlength' => 255,
                ],
            ])
            ->add('mail', EmailType::class, [
                'label'    => 'user.contact.form.mail',
                'required' => $required,
                'attr'     => [
                    'maxlength' => 255,
                ],
            ])
            ->add('phoneNumber', TextType::class, [
                'label'    => 'user.contact.form.phone_number',
                'required' => $required,
                'attr'     => [
                    'maxlength' => 255,
                ],
            ]);
    }

    /**
     * Provide type options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class'        => Contact::class,
                'validation_groups' => 'default',
            ]);
    }
}
