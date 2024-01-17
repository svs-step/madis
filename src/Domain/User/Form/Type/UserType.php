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

use App\Domain\User\Form\DataTransformer\MoreInfoTransformer;
use App\Domain\User\Form\DataTransformer\RoleTransformer;
use App\Domain\User\Model\Collectivity;
use App\Domain\User\Model\Service;
use App\Domain\User\Model\User;
use Doctrine\ORM\EntityRepository;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Security;

class UserType extends AbstractType
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;

    /**
     * @var Security
     */
    private $security;

    private bool $activeNotifications;

    /**
     * UserType constructor.
     */
    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        EncoderFactoryInterface $encoderFactory,
        Security $security,
        bool $activeNotifications
    ) {
        $this->authorizationChecker = $authorizationChecker;
        $this->encoderFactory       = $encoderFactory;
        $this->security             = $security;
        $this->activeNotifications  = $activeNotifications;
    }

    /**
     * Build type form.
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (array_key_exists('data', $options)) {
            // Role est mono-valué dans le form, j'enleve ROLE_API
            $options['data']->setRoles(array_diff($options['data']->getRoles(), ['ROLE_API']));
        }

        $collectivity    = $options['data']->getCollectivity();
        $serviceDisabled = !$collectivity->getIsServicesEnabled();
        /** @var User $authenticatedUser */
        $authenticatedUser = $this->security->getUser();

        $encoderFactory = $this->encoderFactory;

        // Add collectivity general information only for admins
        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $builder
                ->add('collectivity', EntityType::class, [
                    'class'         => Collectivity::class,
                    'label'         => 'global.label.organization',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                            ->orderBy('c.name', 'ASC');
                    },
                    'required' => true,
                    'attr'     => [
                        'autocomplete' => 'organization',
                    ],
                ])
                ->add('roles', DictionaryType::class, [
                    'label'    => 'user.user.label.roles',
                    'required' => true,
                    'name'     => 'user_user_role',
                    'multiple' => false,
                    'expanded' => true,
                ])
                ->add('apiAuthorized', CheckboxType::class, [
                    'label'    => 'user.user.label.apiAuthorized',
                    'required' => false,
                ])

                ->add('enabled', CheckboxType::class, [
                    'label'    => 'user.user.label.enabled',
                    'required' => false,
                ])
                ->add('collectivitesReferees', EntityType::class, [
                    'class'         => Collectivity::class,
                    'label'         => 'user.user.label.collectivitesReferees',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                            ->orderBy('c.name', 'ASC');
                    },
                    'required' => false,
                    'multiple' => true,
                    'expanded' => false,
                    'attr'     => [
                        'class'            => 'selectpicker',
                        'title'            => 'global.placeholder.multiple_select',
                        'data-live-search' => true,
                        'aria-label'       => 'Structures rattachées',
                        'autocomplete'     => 'organization',
                    ],
                ])
                ->add('ssoKey', TextType::class, [
                    'label'    => 'user.user.label.sso_key',
                    'required' => false,
                    'attr'     => [
                        'maxlength' => 255,
                    ],
                    'purify_html' => true,
                ]);

            $builder
                ->get('roles')
                ->addModelTransformer(new RoleTransformer());
        }

        $formModifier = function (FormInterface $form, Collectivity $collectivity) use ($serviceDisabled, $authenticatedUser) {
            $queryBuilder = function (EntityRepository $er) use ($serviceDisabled, $authenticatedUser, $collectivity) {
                if ($serviceDisabled) {
                    return $er->createQueryBuilder('s')
                        ->where(':user MEMBER OF s.users')
                        ->setParameter(':user', $authenticatedUser)
                        ->orderBy('s.name', 'ASC');
                }

                return $er->createQueryBuilder('s')
                    ->where('s.collectivity = :collectivity')
                    ->setParameter(':collectivity', $collectivity)
                    ->orderBy('s.name', 'ASC');
            };

            if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
                $form->add('services', EntityType::class, [
                    'class'         => Service::class,
                    'label'         => 'user.user.label.services',
                    'disabled'      => $serviceDisabled,
                    'required'      => false,
                    'multiple'      => true,
                    'expanded'      => false,
                    'query_builder' => $queryBuilder,
                ]);
            } else {
                $form->add('services', EntityType::class, [
                    'class'         => Service::class,
                    'label'         => 'user.user.label.services',
                    'disabled'      => true,
                    'required'      => false,
                    'multiple'      => true,
                    'expanded'      => false,
                    'query_builder' => $queryBuilder,
                    'attr'          => [
                        'readonly' => true, ],
                ]);
            }
        };

        if ($this->authorizationChecker->isGranted('ROLE_PREVIEW') && !$serviceDisabled) {
            $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($formModifier) {
                $formModifier($event->getForm(), $event->getData()->getCollectivity());
            });
            $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) use ($formModifier) {
                $formModifier($event->getForm(), $event->getData()->getCollectivity());
            });
        }

        // Now add standard information
        $builder
            ->add('firstName', TextType::class, [
                'label'    => 'user.user.label.first_name',
                'required' => true,
                'attr'     => [
                    'maxlength' => 255,
                    'autocomplete' => 'given-name',
                ],
                'purify_html' => true,
            ])
            ->add('lastName', TextType::class, [
                'label'    => 'user.user.label.last_name',
                'required' => true,
                'attr'     => [
                    'maxlength' => 255,
                    'autocomplete' => 'family-name',
                ],
                'purify_html' => true,
            ])
            ->add('email', EmailType::class, [
                'label'    => 'user.user.label.email',
                'required' => true,
                'attr'     => [
                    'maxlength' => 255,
                    'autocomplete' => 'email',
                ],
            ])
            ->add('moreInfos', DictionaryType::class, [
                'label'       => 'user.user.label.moreInfos',
                'required'    => false,
                'name'        => 'user_user_moreInfo',
                'multiple'    => false,
                'expanded'    => true,
                'placeholder' => 'Aucune information',
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type'          => PasswordType::class,
                'first_options' => [
                    'label' => 'user.user.label.password',
                    'attr'  => [
                        'maxlength' => 255,
                    ],
                ],
                'second_options' => [
                    'label' => 'user.user.label.password_repeat',
                    'attr'  => [
                        'maxlength' => 255,
                    ],
                ],
                'required' => false,
            ])
        ;

        if ($this->activeNotifications) {
            $builder->add('emailNotificationPreference', EmailNotificationPreferenceType::class);
            if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
                $builder->add('notGeneratesNotifications', CheckboxType::class, [
                    'label'    => 'user.user.label.not_generates_notifications',
                    'required' => false,
                ]);
            }
        }

        $builder
            ->get('moreInfos')
            ->addModelTransformer(new MoreInfoTransformer())
        ;

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($encoderFactory) {
            $user = $event->getData();
            if (null === $user->getPlainPassword() || !$event->getForm()->isValid()) {
                return;
            }

            $encoder = $encoderFactory->getEncoder($user);
            $user->setPassword($encoder->encodePassword($user->getPlainPassword(), '')); // No salt with bcrypt

            $user->eraseCredentials();
        });
    }

    /**
     * Provide type options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class'        => User::class,
                'validation_groups' => [
                    'default',
                    'user',
                ],
            ]);
    }
}
