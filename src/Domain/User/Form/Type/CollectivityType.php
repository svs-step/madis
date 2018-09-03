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

use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class CollectivityType extends AbstractType
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
                ->add('name', TextType::class, [
                    'label'    => 'user.collectivity.form.name',
                    'required' => true,
                ])
                ->add('shortName', TextType::class, [
                    'label'    => 'user.collectivity.form.short_name',
                    'required' => true,
                ])
                ->add('type', DictionaryType::class, [
                    'label'    => 'user.collectivity.form.type',
                    'required' => true,
                    'name'     => 'user_collectivity_type',
                    'multiple' => false,
                    'expanded' => true,
                ])
                ->add('siren', NumberType::class, [
                    'label'    => 'user.collectivity.form.siren',
                    'required' => true,
                ])
                ->add('active', ChoiceType::class, [
                    'label'    => 'user.collectivity.form.active',
                    'required' => true,
                    'choices'  => [
                        'label.active'   => true,
                        'label.inactive' => false,
                    ],
                    'multiple' => false,
                    'expanded' => true,
                ])
                ->add('website', UrlType::class, [
                    'label'    => 'user.collectivity.form.website',
                    'required' => false,
                    'attr'     => [
                        'placeholder' => 'user.collectivity.form.placeholder.website',
                    ],
                ])
                ->add('address', AddressType::class, [
                    'label'    => 'user.collectivity.form.address',
                    'required' => true,
                ])
            ;
        }

        // Now add standard information
        $builder
            ->add('legalManager', ContactType::class, [
                'label'             => 'user.collectivity.form.legal_manager',
                'required'          => true,
                'validation_groups' => ['default', 'collectivity_legal_manager'],
            ])
            ->add('referent', ContactType::class, [
                'label'             => 'user.collectivity.form.referent',
                'required'          => true,
                'validation_groups' => ['default', 'collectivity_referent'],
            ])
            ->add('differentDpo', CheckboxType::class, [
                'label'    => 'user.collectivity.form.different_dpo',
                'required' => false,
            ])
            ->add('dpo', ContactType::class, [
                'label'    => 'user.collectivity.form.dpo',
                'required' => false,
            ])
            ->add('differentItManager', CheckboxType::class, [
                'label'    => 'user.collectivity.form.different_it_manager',
                'required' => false,
            ])
            ->add('itManager', ContactType::class, [
                'label'    => 'user.collectivity.form.it_manager',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'validation_groups' => [
                    'default',
                ],
            ]);
    }
}
