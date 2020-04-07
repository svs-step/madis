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

use App\Domain\User\Model\Collectivity;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
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

    /**
     * CollectivityType constructor.
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * Build type form.
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Add collectivity general information only for admins
        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $builder
                ->add('name', TextType::class, [
                    'label'    => 'user.collectivity.form.name',
                    'required' => true,
                    'attr'     => [
                        'maxlength' => 255,
                    ],
                ])
                ->add('shortName', TextType::class, [
                    'label'    => 'user.collectivity.form.short_name',
                    'required' => true,
                    'attr'     => [
                        'maxlength' => 20,
                    ],
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
                    'attr'     => [
                        'maxlength' => 9,
                    ],
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
            ->add('reportingBlockManagementCommitment', CKEditorType::class)
            ->add('reportingBlockContinuousImprovement', CKEditorType::class)
            ->add('comiteIlContacts', CollectionType::class, [
                'entry_type'   => ComiteIlContactType::class,
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false,
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
                'data_class'        => Collectivity::class,
                'validation_groups' => [
                    'default',
                ],
            ]);
    }
}
