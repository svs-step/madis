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
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    private RequestStack $requestStack;
    private bool $activeNotifications;

    public function __construct(RequestStack $requestStack, bool $activeNotifications)
    {
        $this->requestStack        = $requestStack;
        $this->activeNotifications = $activeNotifications;
    }

    /**
     * Build type form.
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $request = $this->requestStack->getCurrentRequest();

        $intersectIsEmpty = empty(\array_intersect(
            [
                'collectivity_legal_manager',
                'collectivity_referent',
                'collectivity_comite_il_contact',
            ],
            $options['validation_groups'] ?? []
        ));

        $required = $intersectIsEmpty ? false : true;

        $isComiteIl = in_array('collectivity_comite_il_contact', $options['validation_groups'] ?? []);

        $builder
            ->add('civility', DictionaryType::class, [
                'label'    => 'global.label.contact.civility',
                'required' => $required,
                'name'     => 'user_contact_civility',
            ])
            ->add('firstName', TextType::class, [
                'label'    => 'global.label.contact.first_name',
                'required' => $required,
                'attr'     => [
                    'maxlength' => 255,
                ],
                'purify_html' => true,
            ])
            ->add('lastName', TextType::class, [
                'label'    => 'global.label.contact.last_name',
                'required' => $required,
                'attr'     => [
                    'maxlength' => 255,
                ],
                'purify_html' => true,
            ])
            ->add('job', TextType::class, [
                'label'    => 'global.label.contact.job',
                'required' => $required,
                'attr'     => [
                    'maxlength' => 255,
                ],
                'purify_html' => true,
            ])
            ->add('mail', EmailType::class, [
                'label'    => 'global.label.contact.email',
                'required' => $isComiteIl ? false : $required,
                'attr'     => [
                    'maxlength' => 255,
                ],
                'purify_html' => true,
            ])
        ;

        // Email notification only available on collectivity page for responsable traitement and referent RGPD
        if ($this->activeNotifications
            && (
                in_array('collectivity_legal_manager', $options['validation_groups'] ?? [])
                || in_array('collectivity_referent', $options['validation_groups'] ?? [])
                || in_array('collectivity_dpo', $options['validation_groups'] ?? [])
            )
        ) {
            $builder->add('notification', CheckboxType::class, [
                'label'    => 'notifications.label.activate_notification_email',
                'required' => false,
            ]);
        }

        $builder->add('phoneNumber', TextType::class, [
            'label'    => 'global.label.contact.phone_number',
            'required' => $isComiteIl ? false : $required,
            'attr'     => [
                'maxlength' => 10,
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
                'data_class'        => Contact::class,
                'validation_groups' => 'default',
            ]);
    }
}
