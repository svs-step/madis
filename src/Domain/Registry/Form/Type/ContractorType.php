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

namespace App\Domain\Registry\Form\Type;

use App\Domain\Registry\Form\Type\Embeddable\AddressType;
use App\Domain\Registry\Model\Contractor;
use App\Domain\User\Form\Type\ContactType;
use App\Domain\User\Model\Service;
use App\Domain\User\Model\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Security;

class ContractorType extends AbstractType
{
    /**
     * @var Security
     */
    private $security;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    public function __construct(Security $security, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->security             = $security;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * Build type form.
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $contractor = $options['data'];
        $builder
            ->add('name', TextType::class, [
                'label'    => 'registry.contractor.form.name',
                'required' => true,
                'attr'     => [
                    'maxlength' => 255,
                ],
            ])
        ;
        if ($contractor->getCollectivity()->getIsServicesEnabled()) {
            $builder
                ->add('service', EntityType::class, [
                    'class'         => Service::class,
                    'label'         => 'registry.treatment.form.service',
                    'query_builder' => function (EntityRepository $er) use ($contractor) {
                        /** @var User $authenticatedUser */
                        $authenticatedUser = $this->security->getUser();
                        $collectivity = $contractor->getCollectivity();

                        $qb = $er->createQueryBuilder('s')
                            ->where('s.collectivity = :collectivity')
                            ->setParameter(':collectivity', $collectivity)
                        ;
                        if ($authenticatedUser->getServices()->getValues()) {
                            $qb->leftJoin('s.users', 'users')
                                ->andWhere('users.id = :id')
                                ->setParameter('id', $authenticatedUser->getId())
                            ;
                        }
                        $qb
                            ->orderBy('s.name', 'ASC');

                        return $qb;
                    },
                    'required' => false,
                ])
            ;
        }
        $builder
            ->add('referent', TextType::class, [
                'label'    => 'registry.contractor.form.referent',
                'required' => false,
                'attr'     => [
                    'maxlength' => 255,
                ],
            ])
            ->add('contractualClausesVerified', CheckboxType::class, [
                'label'    => 'registry.contractor.form.contractual_clauses_verified',
                'required' => false,
            ])
            ->add('adoptedSecurityFeatures', CheckboxType::class, [
                'label'    => 'registry.contractor.form.adopted_security_features',
                'required' => false,
            ])
            ->add('maintainsTreatmentRegister', CheckboxType::class, [
                'label'    => 'registry.contractor.form.maintains_treatment_register',
                'required' => false,
            ])
            ->add('sendingDataOutsideEu', CheckboxType::class, [
                'label'    => 'registry.contractor.form.sending_data_outside_eu',
                'required' => false,
            ])
            ->add('otherInformations', TextareaType::class, [
                'label'    => 'registry.contractor.form.other_informations',
                'required' => false,
                'attr'     => [
                    'rows' => 4,
                ],
            ])
            ->add('address', AddressType::class, [
                'label'             => 'registry.contractor.form.address',
                'required'          => false,
                'validation_groups' => ['default', 'contractor'],
            ])
            ->add('legalManager', ContactType::class, [
                'label'             => 'registry.contractor.form.legal_manager',
                'required'          => false,
            ])
            ->add('hasDpo', CheckboxType::class, [
                'label'    => 'registry.contractor.form.has_dpo',
                'required' => false,
            ])
            ->add('dpo', ContactType::class, [
                'label'    => 'registry.contractor.form.dpo',
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
                'data_class'        => Contractor::class,
                'validation_groups' => [
                    'default',
                    'contractor',
                ],
            ]);
    }
}
