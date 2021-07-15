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

use App\Domain\User\Form\DataTransformer\RoleTransformer;
use App\Domain\User\Model\Collectivity;
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
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

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
     * UserType constructor.
     */
    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        EncoderFactoryInterface $encoderFactory
    ) {
        $this->authorizationChecker = $authorizationChecker;
        $this->encoderFactory       = $encoderFactory;
    }

    /**
     * Build type form.
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Role est mono-valué dans le form, j'enleve ROLE_API
        if (array_key_exists('data', $options)) {
            $options['data']->setRoles(array_diff($options['data']->getRoles(), ['ROLE_API']));
        }

        $encoderFactory = $this->encoderFactory;

        // Add collectivity general information only for admins
        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $builder
                ->add('collectivity', EntityType::class, [
                    'class'         => Collectivity::class,
                    'label'         => 'user.user.form.collectivity',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                            ->orderBy('c.name', 'ASC');
                    },
                    'required' => true,
                ])
                ->add('roles', DictionaryType::class, [
                    'label'    => 'user.user.form.roles',
                    'required' => true,
                    'name'     => 'user_user_role',
                    'multiple' => false,
                    'expanded' => true,
                ])
                ->add('apiAuthorized', CheckboxType::class, [
                    'label'    => 'user.user.form.apiAuthorized',
                    'required' => false,
                ])
                ->add('enabled', CheckboxType::class, [
                    'label'    => 'user.user.form.enabled',
                    'required' => false,
                ])
                ->add('collectivitesReferees', EntityType::class, [
                    'class'         => Collectivity::class,
                    'label'         => 'user.user.form.collectivitesReferees',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                            ->orderBy('c.name', 'ASC');
                    },
                    'required' => false,
                    'multiple' => true,
                    'expanded' => false,
                    'attr'     => [
                        'class'            => 'selectpicker',
                        'title'            => 'placeholder.multiple_select',
                        'data-live-search' => true,
                        'data-width'       => '450px',
                    ],
                ])
            ;

            $builder
                ->get('roles')
                ->addModelTransformer(new RoleTransformer())
            ;
        }

        // Now add standard information
        $builder
            ->add('firstName', TextType::class, [
                'label'    => 'user.user.form.first_name',
                'required' => true,
                'attr'     => [
                    'maxlength' => 255,
                ],
            ])
            ->add('lastName', TextType::class, [
                'label'    => 'user.user.form.last_name',
                'required' => true,
                'attr'     => [
                    'maxlength' => 255,
                ],
            ])
            ->add('email', EmailType::class, [
                'label'    => 'user.user.form.email',
                'required' => true,
                'attr'     => [
                    'maxlength' => 255,
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type'          => PasswordType::class,
                'first_options' => [
                    'label' => 'user.user.form.password',
                    'attr'  => [
                        'maxlength' => 255,
                    ],
                ],
                'second_options' => [
                    'label' => 'user.user.form.password_repeat',
                    'attr'  => [
                        'maxlength' => 255,
                    ],
                ],
                'required' => false,
            ])
        ;

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($encoderFactory) {
            $user = $event->getData();
            if (null === $user->getPlainPassword()) {
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
