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

use App\Domain\Registry\Model\Treatment;
use App\Domain\Registry\Model\Violation;
use App\Domain\User\Model\Service;
use App\Domain\User\Model\User;
use Doctrine\ORM\EntityRepository;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Security;

class ViolationType extends AbstractType
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
        $violation = $options['data'];
        $builder
            ->add('date', DateType::class, [
                'label'    => 'registry.violation.form.date',
                'required' => true,
                'widget'   => 'single_text',
                'format'   => 'dd/MM/yyyy',
                'html5'    => false,
                'attr'     => [
                    'class' => 'datepicker',
                ],
            ])
        ;
        if ($violation->getCollectivity()->getIsServicesEnabled()) {
            $builder->add('service', EntityType::class, [
                'class'         => Service::class,
                'label'         => 'registry.treatment.form.service',
                'query_builder' => function (EntityRepository $er) use ($violation) {
                    /** @var User $authenticatedUser */
                    $authenticatedUser = $this->security->getUser();
                    $collectivity = $violation->getCollectivity();

                    $qb = $er->createQueryBuilder('s')
                        ->where('s.collectivity = :collectivity')
                        ->setParameter(':collectivity', $collectivity)
                    ;
                    if (!$this->authorizationChecker->isGranted('ROLE_ADMIN') && ($authenticatedUser->getServices()->getValues())) {
                        $qb->leftJoin('s.users', 'users')
                            ->andWhere('users.id = :id')
                            ->setParameter('id', $authenticatedUser->getId())
                        ;
                    }
                    $qb
                        ->orderBy('s.name', 'ASC');

                    return $qb;
                },
                'required'      => false,
            ]);
        }
        $builder
            ->add('inProgress', CheckboxType::class, [
                'label'    => 'registry.violation.form.in_progress',
                'required' => false,
            ])
            ->add('violationNature', DictionaryType::class, [
                'label'    => 'registry.violation.form.violation_nature',
                'name'     => 'registry_violation_nature',
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('origins', DictionaryType::class, [
                'label'    => 'registry.violation.form.origins',
                'name'     => 'registry_violation_origin',
                'expanded' => false,
                'multiple' => true,
                'attr'     => [
                    'class' => 'selectpicker',
                    'title' => 'placeholder.multiple_select',
                ],
            ])
            ->add('cause', DictionaryType::class, [
                'label'    => 'registry.violation.form.cause',
                'name'     => 'registry_violation_cause',
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('concernedDataNature', DictionaryType::class, [
                'label'    => 'registry.violation.form.concerned_data_nature',
                'name'     => 'registry_violation_concerned_data',
                'expanded' => false,
                'multiple' => true,
                'attr'     => [
                    'class' => 'selectpicker',
                    'title' => 'placeholder.multiple_select',
                ],
            ])
            ->add('concernedPeopleCategories', DictionaryType::class, [
                'label'    => 'registry.violation.form.concerned_people_categories',
                'name'     => 'registry_violation_concerned_people',
                'expanded' => false,
                'multiple' => true,
                'attr'     => [
                    'class' => 'selectpicker',
                    'title' => 'placeholder.multiple_select',
                ],
            ])
            ->add('nbAffectedRows', IntegerType::class, [
                'label' => 'registry.violation.form.nb_affected_rows',
                'attr'  => [
                    'min' => 0,
                ],
            ])
            ->add('nbAffectedPersons', IntegerType::class, [
                'label' => 'registry.violation.form.nb_affected_persons',
                'attr'  => [
                    'min' => 0,
                ],
            ])
            ->add('potentialImpactsNature', DictionaryType::class, [
                'label'    => 'registry.violation.form.potential_impacts_nature',
                'name'     => 'registry_violation_impact',
                'expanded' => false,
                'multiple' => true,
                'attr'     => [
                    'class' => 'selectpicker',
                    'title' => 'placeholder.multiple_select',
                ],
            ])
            ->add('gravity', DictionaryType::class, [
                'label'    => 'registry.violation.form.gravity',
                'name'     => 'registry_violation_gravity',
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('communication', DictionaryType::class, [
                'label'    => 'registry.violation.form.communication',
                'name'     => 'registry_violation_communication',
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('communicationPrecision', TextareaType::class, [
                'label'    => 'registry.violation.form.communication_precision',
                'required' => false,
                'attr'     => [
                    'rows' => 5,
                ],
            ])
            ->add('appliedMeasuresAfterViolation', TextareaType::class, [
                'label' => 'registry.violation.form.applied_measures_after_violation',
                'attr'  => [
                    'rows' => 5,
                ],
            ])
            ->add('notification', DictionaryType::class, [
                'label'       => 'registry.violation.form.notification',
                'name'        => 'registry_violation_notification',
                'required'    => false,
                'expanded'    => true,
                'multiple'    => false,
                'placeholder' => 'Aucune notification à envoyer',
            ])
            ->add('notificationDetails', TextType::class, [
                'label'    => 'registry.violation.form.notification_details',
                'required' => false,
                'attr'     => [
                    'maxlength' => 255,
                ],
            ])
            ->add('comment', TextareaType::class, [
                'label'    => 'registry.violation.form.comment',
                'required' => false,
                'attr'     => [
                    'rows' => 5,
                ],
            ])
            ->add('treatments', EntityType::class, [
                'class'         => Treatment::class,
                'label'         => 'registry.violation.form.treatment',
                'query_builder' => function (EntityRepository $er) use ($violation){
                    $collectivity = $violation->getCollectivity();

                    return $er->createQueryBuilder('s')
                        ->where('s.collectivity = :collectivity')
                        ->setParameter(':collectivity', $collectivity)
                        ->orderBy('s.name', 'ASC');
                },
                'required'      => false,
                'multiple'      => true,
                'expanded'      => true,
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
                'data_class'        => Violation::class,
                'validation_groups' => [
                    'default',
                    'violation',
                ],
            ]);
    }
}
