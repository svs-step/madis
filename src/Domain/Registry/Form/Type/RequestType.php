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

use App\Domain\Registry\Form\Type\Embeddable\RequestAnswerType;
use App\Domain\Registry\Form\Type\Embeddable\RequestApplicantType;
use App\Domain\Registry\Form\Type\Embeddable\RequestConcernedPeopleType;
use App\Domain\Registry\Model\Request;
use App\Domain\Registry\Model\Treatment;
use App\Domain\User\Model\Service;
use App\Domain\User\Model\User;
use Doctrine\ORM\EntityRepository;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Security;

class RequestType extends AbstractType
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
        $request = $options['data'];
        $builder
            ->add('object', DictionaryType::class, [
                'label'    => 'registry.request.form.object',
                'name'     => 'registry_request_object',
                'required' => true,
                'expanded' => true,
            ])
        ;
        if ($request->getCollectivity()->getIsServicesEnabled()) {
            $builder
                ->add('service', EntityType::class, [
                    'class'         => Service::class,
                    'label'         => 'registry.treatment.form.service',
                    'query_builder' => function (EntityRepository $er) use ($request) {
                        /** @var User $authenticatedUser */
                        $authenticatedUser = $this->security->getUser();
                        $collectivity      = $request->getCollectivity();

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
                    },
                    'required' => false,
                ])
            ;
        }
        $builder
            ->add('otherObject', TextType::class, [
                'label'    => 'registry.request.form.other_object',
                'required' => false,
                'attr'     => [
                    'maxlength' => 255,
                ],
            ])
            ->add('date', DateType::class, [
                'label'    => 'registry.request.form.date',
                'required' => true,
                'widget'   => 'single_text',
                'format'   => 'dd/MM/yyyy',
                'html5'    => false,
                'attr'     => [
                    'class' => 'datepicker',
                ],
            ])
            ->add('reason', TextType::class, [
                'label'    => 'registry.request.form.reason',
                'required' => false,
                'attr'     => [
                    'maxlength' => 255,
                ],
            ])
            ->add('applicant', RequestApplicantType::class, [
                'label'    => false,
                'required' => true,
            ])
            ->add('concernedPeople', RequestConcernedPeopleType::class, [
                'label'    => false,
                'required' => false,
            ])
            ->add('complete', CheckboxType::class, [
                'label'    => 'registry.request.form.complete',
                'required' => false,
            ])
            ->add('legitimateApplicant', CheckboxType::class, [
                'label'    => 'registry.request.form.legitimate_applicant',
                'required' => false,
            ])
            ->add('legitimateRequest', CheckboxType::class, [
                'label'    => 'registry.request.form.legitimate_request',
                'required' => false,
            ])
            ->add('answer', RequestAnswerType::class, [
                'label'    => false,
                'required' => false,
            ])
            ->add('state', DictionaryType::class, [
                'label'    => 'registry.request.form.state',
                'name'     => 'registry_request_state',
                'required' => true,
            ])
            ->add('stateRejectionReason', TextareaType::class, [
                'label'    => 'registry.request.form.state_rejection_reason',
                'required' => false,
                'attr'     => [
                    'rows' => 4,
                ],
            ])
            ->add('treatments', EntityType::class, [
                'class'         => Treatment::class,
                'label'         => 'registry.request.form.treatment',
                'query_builder' => function (EntityRepository $er) use ($request) {
                    $collectivity = $request->getCollectivity();

                    return $er->createQueryBuilder('s')
                        ->where('s.collectivity = :collectivity')
                        ->setParameter(':collectivity', $collectivity)
                        ->orderBy('s.name', 'ASC');
                },
                'attr' => [
                    'class'            => 'selectpicker',
                    'data-live-search' => 'true',
                    'title'            => 'placeholder.multiple_select_traitement_associe',
                ],
                'required' => false,
                'multiple' => true,
                'expanded' => false,
            ])
            ->add('updatedBy', HiddenType::class, [
                'required' => false,
                'data'     => $this->security->getUser()->getFirstName() . ' ' . strtoupper($this->security->getUser()->getLastName()),
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
                'data_class'        => Request::class,
                'validation_groups' => [
                    'default',
                    'request',
                ],
            ]);
    }
}
