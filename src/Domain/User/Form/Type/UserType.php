<?php

/**
 * This file is part of the SOLURIS - RGPD Management application.
 *
 * (c) Donovan Bourlard <donovan@awkan.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class UserType extends AbstractType
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Add collectivity general information only for admins
        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $builder
                ->add('plainPassword', RepeatedType::class, [
                    'type'          => PasswordType::class,
                    'first_options' => [
                        'label' => 'user.user.form.password',
                    ],
                    'second_options' => [
                        'label' => 'user.user.form.password_repeat',
                    ],
                    'required' => false,
                ])
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
                ->add('enabled', CheckboxType::class, [
                    'label'    => 'user.user.form.enabled',
                    'required' => false,
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
            ])
            ->add('lastName', TextType::class, [
                'label'    => 'user.user.form.last_name',
                'required' => true,
            ])
            ->add('email', EmailType::class, [
                'label'    => 'user.user.form.email',
                'required' => true,
            ])
        ;
    }

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
