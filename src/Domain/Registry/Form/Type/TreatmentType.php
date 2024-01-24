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

use App\Domain\Registry\Form\Type\Embeddable\ComplexChoiceType;
use App\Domain\Registry\Model\Contractor;
use App\Domain\Registry\Model\Tool;
use App\Domain\Registry\Model\Treatment;
use App\Domain\Registry\Model\TreatmentDataCategory;
use App\Domain\User\Model\Service;
use App\Domain\User\Model\User;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Security;

class TreatmentType extends AbstractType
{
    /**
     * @var Security
     */
    private $security;
    private AuthorizationCheckerInterface $authorizationChecker;

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
        /** @var Treatment $treatment */
        $treatment = $options['data'];
        /** @var User $user */
        $user = $this->security->getUser();
        $builder
            ->add('public', CheckboxType::class, [
                'label'    => 'registry.treatment.label.public_registry',
                'required' => false,
            ])
            ->add('name', TextType::class, [
                'label'    => 'registry.treatment.label.name',
                'required' => true,
                'attr'     => [
                    'maxlength' => 255,
                ],
                'purify_html' => true,
            ])
            ->add('exempt_AIPD', CheckboxType::class, [
                'label'    => 'registry.treatment.label.exempt_AIPD',
                'required' => false,
            ])

            ->add('goal', TextareaType::class, [
                'label'    => 'registry.treatment.label.goal',
                'required' => false,
                'attr'     => [
                    'rows' => 4,
                ],
                'purify_html' => true,
            ])
            ->add('manager', TextType::class, [
                'label'    => 'registry.treatment.label.manager',
                'required' => false,
                'attr'     => [
                    'maxlength' => 255,
                ],
                'purify_html' => true,
            ])

            ->add('paperProcessing', CheckboxType::class, [
                'label'    => 'registry.treatment.label.paper_processing',
                'required' => false,
            ])
            ->add('legalBasis', DictionaryType::class, [
                'label'    => 'registry.treatment.label.legal_basis',
                'name'     => 'registry_treatment_legal_basis',
                'required' => true,
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('legalBasisJustification', TextareaType::class, [
                'label'       => 'registry.treatment.label.legal_basis_justification',
                'required'    => false,
                'purify_html' => true,
            ])
            ->add('observation', TextareaType::class, [
                'label'    => 'registry.treatment.label.observation',
                'required' => false,
                'attr'     => [
                    'rows' => 2,
                ],
                'purify_html' => true,
            ])
            ->add('concernedPeopleParticular', ComplexChoiceType::class, [
                'label'    => 'registry.treatment.label.concerned_people_particular',
                'required' => false,
            ])
            ->add('concernedPeopleUser', ComplexChoiceType::class, [
                'label'    => 'registry.treatment.label.concerned_people_user',
                'required' => false,
            ])
            ->add('concernedPeopleAgent', ComplexChoiceType::class, [
                'label'    => 'registry.treatment.label.concerned_people_agent',
                'required' => false,
            ])
            ->add('concernedPeopleElected', ComplexChoiceType::class, [
                'label'    => 'registry.treatment.label.concerned_people_elected',
                'required' => false,
            ])
            ->add('concernedPeopleCompany', ComplexChoiceType::class, [
                'label'    => 'registry.treatment.label.concerned_people_company',
                'required' => false,
            ])
            ->add('concernedPeoplePartner', ComplexChoiceType::class, [
                'label'    => 'registry.treatment.label.concerned_people_partner',
                'required' => false,
            ])
            ->add('concernedPeopleUsager', ComplexChoiceType::class, [
                'label'    => 'registry.treatment.label.concerned_people_usager',
                'required' => false,
            ])
            ->add('concernedPeopleOther', ComplexChoiceType::class, [
                'label'    => 'registry.treatment.label.concerned_people_other',
                'required' => false,
            ])
            ->add('dataCategories', EntityType::class, [
                'label'         => 'registry.treatment.label.data_category',
                'class'         => TreatmentDataCategory::class,
                'required'      => false,
                'expanded'      => false,
                'multiple'      => true,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('dc')
                        ->orderBy('dc.position', Criteria::ASC);
                },
                'choice_attr' => function (TreatmentDataCategory $model) {
                    if ($model->isSensible()) {
                        return [
                            'style' => 'font-weight: bold;',
                        ];
                    }

                    return [];
                },
                'attr' => [
                    'class'            => 'selectpicker',
                    'data-live-search' => 'true',
                    'title'            => 'global.placeholder.multiple_select',
                    'aria-label'       => 'Catégorie des données',
                ],
            ])
            ->add('dataCategoryOther', TextareaType::class, [
                'label'    => 'registry.treatment.label.data_category_other',
                'required' => false,
                'attr'     => [
                    'rows' => 3,
                ],
                'purify_html' => true,
            ])
            ->add('dataOrigin', TextType::class, [
                'label'    => 'registry.treatment.label.data_origin',
                'required' => false,
                'attr'     => [
                    'maxlength' => 255,
                ],
                'purify_html' => true,
            ])
            ->add('recipientCategory', TextareaType::class, [
                'label'    => 'registry.treatment.label.recipient_category',
                'required' => false,
                'attr'     => [
                    'rows' => 2,
                ],
                'purify_html' => true,
            ])
            ->add('contractors', EntityType::class, [
                'label'         => 'global.label.linked_contractor',
                'class'         => Contractor::class,
                'required'      => false,
                'multiple'      => true,
                'expanded'      => false,
                'query_builder' => function (EntityRepository $er) use ($treatment) {
                    $collectivity = null;
                    if (!\is_null($treatment->getCollectivity())) {
                        $collectivity = $treatment->getCollectivity();
                    } else {
                        /** @var User $authenticatedUser */
                        $authenticatedUser = $this->security->getUser();
                        $collectivity      = $authenticatedUser->getCollectivity();
                    }

                    return $er->createQueryBuilder('c')
                        ->where('c.collectivity = :collectivity')
                        ->addOrderBy('c.name', 'asc')
                        ->setParameter('collectivity', $collectivity)
                    ;
                },
                'attr' => [
                    'class'            => 'selectpicker',
                    'data-live-search' => 'true',
                    'title'            => 'global.placeholder.multiple_select',
                    'aria-label'       => 'Sous-traitants',
                ],
            ])
            ->add('securityAccessControl', ComplexChoiceType::class, [
                'label'    => 'registry.treatment.label.security_access_control',
                'required' => false,
            ])
            ->add('securityTracability', ComplexChoiceType::class, [
                'label'    => 'registry.treatment.label.security_tracability',
                'required' => false,
            ])
            ->add('securitySaving', ComplexChoiceType::class, [
                'label'    => 'registry.treatment.label.security_saving',
                'required' => false,
            ])
            ->add('securityUpdate', ComplexChoiceType::class, [
                'label'    => 'registry.treatment.label.security_update',
                'required' => false,
            ])
            ->add('securityOther', ComplexChoiceType::class, [
                'label'    => 'registry.treatment.label.security_other',
                'required' => false,
            ])
            ->add('systematicMonitoring', CheckboxType::class, [
                'label'    => 'registry.treatment.label.systematic_monitoring',
                'required' => false,
            ])
            ->add('largeScaleCollection', CheckboxType::class, [
                'label'    => 'registry.treatment.label.large_scale_collection',
                'required' => false,
            ])
            ->add('vulnerablePeople', CheckboxType::class, [
                'label'    => 'registry.treatment.label.vulnerable_people',
                'required' => false,
            ])
            ->add('dataCrossing', CheckboxType::class, [
                'label'    => 'registry.treatment.label.data_crossing',
                'required' => false,
            ])
            ->add('evaluationOrRating', CheckboxType::class, [
                'label'    => 'registry.treatment.label.evaluation_or_rating',
                'required' => false,
            ])
            ->add('automatedDecisionsWithLegalEffect', CheckboxType::class, [
                'label'    => 'registry.treatment.label.automated_decisions_with_legal_effect',
                'required' => false,
            ])
            ->add('automaticExclusionService', CheckboxType::class, [
                'label'    => 'registry.treatment.label.automatic_exclusion_service',
                'required' => false,
            ])
            ->add('innovativeUse', CheckboxType::class, [
                'label'    => 'registry.treatment.label.innovative_use',
                'required' => false,
            ])
            ->add('active', ChoiceType::class, [
                'label'    => 'registry.treatment.label.active',
                'required' => true,
                'choices'  => [
                    'global.label.active'   => true,
                    'global.label.inactive' => false,
                ],
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('author', DictionaryType::class, [
                'label'    => 'registry.treatment.label.author',
                'name'     => 'registry_treatment_author',
                'required' => true,
            ])
            ->add('coordonneesResponsableTraitement', TextareaType::class, [
                'label'    => 'registry.treatment.label.coordonnees_responsable_traitement',
                'required' => false,
                'attr'     => [
                    'rows' => 3,
                ],
                'purify_html' => true,
            ])
            ->add('collectingMethod', DictionaryType::class, [
                'label'       => 'registry.treatment.label.collecting_method',
                'name'        => 'registry_treatment_collecting_method',
                'required'    => false,
                'expanded'    => false,
                'multiple'    => true,
                'placeholder' => 'global.placeholder.precision',
                'attr'        => [
                    'class'            => 'selectpicker',
                    'data-live-search' => 'true',
                    'title'            => 'global.placeholder.multiple_select',
                    'aria-label'       => 'Moyens de la collecte',
                ],
            ])
            ->add('estimatedConcernedPeople', IntegerType::class, [
                'label'    => 'registry.treatment.label.estimated_concerned_people',
                'required' => false,
                'attr'     => [
                    'min' => 0,
                ],
            ])
            ->add('securityEntitledPersons', CheckboxType::class, [
                'label'    => 'registry.treatment.label.security_entitled_persons',
                'required' => false,
            ])
            ->add('securityOpenAccounts', CheckboxType::class, [
                'label'    => 'registry.treatment.label.security_open_accounts',
                'required' => false,
            ])
            ->add('securitySpecificitiesDelivered', CheckboxType::class, [
                'label'    => 'registry.treatment.label.security_specificities_delivered',
                'required' => false,
            ])
            ->add('shelfLifes', CollectionType::class, [
                'entry_type'   => ShelfLifeType::class,
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->add('otherCollectingMethod', TextType::class, [
                'label'       => 'registry.treatment.label.otherCollectingMethod',
                'required'    => false,
                'purify_html' => true,
            ])
            ->add('updatedBy', HiddenType::class, [
                'required' => false,
                'data'     => $user ? $user->getFirstName() . ' ' . strtoupper($user->getLastName()) : '',
            ])
            ->add('legalMentions', CheckboxType::class, [
                'label'    => 'registry.treatment.label.legal_mentions',
                'required' => false,
            ])
            ->add('consentRequest', CheckboxType::class, [
                'label'    => 'registry.treatment.label.consent_request',
                'required' => false,
            ])
            ->add('consentRequestFormat', TextType::class, [
                'label'       => 'registry.treatment.label.consent_request_format',
                'required'    => false,
                'purify_html' => true,
            ])

        ;

        if ($this->authorizationChecker->isGranted('ROLE_ADMIN') || $this->authorizationChecker->isGranted('ROLE_REFERENT')) {
            $builder
                ->add('dpoMessage', TextAreaType::class, [
                    'label'       => 'registry.treatment.label.dpoMessage',
                    'required'    => false,
                    'purify_html' => true,
                ])
                ->add('statut', DictionaryType::class, [
                    'label'    => 'registry.treatment.label.statut',
                    'name'     => 'treatment_statut',
                    'required' => true,
                ]);
        } else {
            $builder
                ->add('statut', HiddenType::class, [
                    'data' => 'finished',
                ]);
        }

        if ($options['data']->getCollectivity()->isHasModuleTools()) {
            $builder->add('tools', EntityType::class, [
                'label'         => 'global.label.linked_tool',
                'class'         => Tool::class,
                'required'      => false,
                'multiple'      => true,
                'expanded'      => false,
                'query_builder' => function (EntityRepository $er) use ($treatment) {
                    $collectivity = null;
                    if (!\is_null($treatment->getCollectivity())) {
                        $collectivity = $treatment->getCollectivity();
                    } else {
                        /** @var User $authenticatedUser */
                        $authenticatedUser = $this->security->getUser();
                        $collectivity      = $authenticatedUser->getCollectivity();
                    }

                    return $er->createQueryBuilder('c')
                        ->where('c.collectivity = :collectivity')
                        ->addOrderBy('c.name', 'asc')
                        ->setParameter('collectivity', $collectivity)
                    ;
                },
                'attr' => [
                    'class'            => 'selectpicker',
                    'data-live-search' => 'true',
                    'title'            => 'global.placeholder.multiple_select',
                    'aria-label'       => 'Logiciels et supports',
                ],
            ]);
        } else {
            $builder->add('software', TextType::class, [
                'label'    => 'registry.treatment.label.tools_if_inactif',
                'required' => false,
                'attr'     => [
                    'maxlength' => 255,
                ],
                'purify_html' => true,
            ]);
        }

        // Check if services are enabled for the collectivity's treatment
        if ($options['data']->getCollectivity()->getIsServicesEnabled()) {
            $builder->add('service', EntityType::class, [
                'class'         => Service::class,
                'label'         => 'registry.label.service',
                'query_builder' => function (EntityRepository $er) use ($treatment) {
                    if ($treatment->getCollectivity()) {
                        /** @var User $authenticatedUser */
                        $authenticatedUser = $this->security->getUser();
                        $collectivity      = $treatment->getCollectivity();

                        $qb = $er->createQueryBuilder('s')
                        ->where('s.collectivity = :collectivity')
                        ->setParameter(':collectivity', $collectivity)
                        ;

                        if (!$this->authorizationChecker->isGranted('ROLE_ADMIN') && $authenticatedUser->getServices()->getValues()) {
                            $qb->leftJoin('s.users', 'users')
                                ->andWhere('users.id = :id')
                                ->setParameter('id', $authenticatedUser->getId())
                            ;
                        }

                        $qb
                        ->orderBy('s.name', 'ASC');

                        return $qb;
                    }

                    return $er->createQueryBuilder('s')
                        ->orderBy('s.name', 'ASC');
                },
                'required' => false,
            ]);
        }
    }

    /**
     * Provide type options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class'        => Treatment::class,
                'validation_groups' => [
                    'default',
                    'treatment',
                ],
            ]);
    }
}
