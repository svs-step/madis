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

namespace App\Domain\Registry\Form\Type;

use App\Application\Symfony\Security\UserProvider;
use App\Domain\Registry\Form\Type\Embeddable\ComplexChoiceType;
use App\Domain\Registry\Form\Type\Embeddable\DelayType;
use App\Domain\Registry\Model\Contractor;
use App\Domain\Registry\Model\Treatment;
use App\Domain\Registry\Model\TreatmentDataCategory;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TreatmentType extends AbstractType
{
    /**
     * @var UserProvider
     */
    private $userProvider;

    public function __construct(UserProvider $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    /**
     * Build type form.
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label'    => 'registry.treatment.form.name',
                'required' => true,
            ])
            ->add('goal', TextareaType::class, [
                'label'    => 'registry.treatment.form.goal',
                'required' => false,
                'attr'     => [
                    'rows' => 4,
                ],
            ])
            ->add('manager', TextType::class, [
                'label'    => 'registry.treatment.form.manager',
                'required' => false,
            ])
            ->add('software', TextType::class, [
                'label'    => 'registry.treatment.form.software',
                'required' => false,
            ])
            ->add('paperProcessing', CheckboxType::class, [
                'label'    => 'registry.treatment.form.paper_processing',
                'required' => false,
            ])
            ->add('legalBasis', DictionaryType::class, [
                'label'    => 'registry.treatment.form.legal_basis',
                'name'     => 'registry_treatment_legal_basis',
                'required' => true,
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('legalBasisJustification', TextareaType::class, [
                'label'    => 'registry.treatment.form.legal_basis_justification',
                'required' => false,
            ])
            ->add('observation', TextareaType::class, [
                'label'    => 'registry.treatment.form.observation',
                'required' => false,
                'attr'     => [
                    'rows' => 2,
                ],
            ])
            ->add('concernedPeople', DictionaryType::class, [
                'label'    => 'registry.treatment.form.concerned_people',
                'name'     => 'registry_treatment_concerned_people',
                'required' => true,
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('dataCategories', EntityType::class, [
                'label'         => 'registry.treatment.form.data_category',
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
                'attr'     => [
                    'size' => 6,
                ],
            ])
            ->add('dataCategoryOther', TextareaType::class, [
                'label'    => 'registry.treatment.form.data_category_other',
                'required' => false,
                'attr'     => [
                    'rows' => 3,
                ],
            ])
            ->add('dataOrigin', TextType::class, [
                'label'    => 'registry.treatment.form.data_origin',
                'required' => false,
            ])
            ->add('recipientCategory', TextareaType::class, [
                'label'    => 'registry.treatment.form.recipient_category',
                'required' => false,
                'attr'     => [
                    'rows' => 2,
                ],
            ])
            ->add('contractors', EntityType::class, [
                'label'         => 'registry.treatment.form.contractors',
                'class'         => Contractor::class,
                'required'      => false,
                'multiple'      => true,
                'expanded'      => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->where('c.collectivity = :collectivity')
                        ->addOrderBy('c.name', 'asc')
                        ->setParameter('collectivity', $this->userProvider->getAuthenticatedUser()->getCollectivity())
                    ;
                },
            ])
            ->add('delay', DelayType::class, [
                'label'    => 'registry.treatment.form.delay',
                'required' => false,
            ])
            ->add('securityAccessControl', ComplexChoiceType::class, [
                'label'    => 'registry.treatment.form.security_access_control',
                'required' => false,
            ])
            ->add('securityTracability', ComplexChoiceType::class, [
                'label'    => 'registry.treatment.form.security_tracability',
                'required' => false,
            ])
            ->add('securitySaving', ComplexChoiceType::class, [
                'label'    => 'registry.treatment.form.security_saving',
                'required' => false,
            ])
            ->add('securityUpdate', ComplexChoiceType::class, [
                'label'    => 'registry.treatment.form.security_update',
                'required' => false,
            ])
            ->add('securityOther', ComplexChoiceType::class, [
                'label'    => 'registry.treatment.form.security_other',
                'required' => false,
            ])
            ->add('systematicMonitoring', CheckboxType::class, [
                'label'    => 'registry.treatment.form.systematic_monitoring',
                'required' => false,
            ])
            ->add('largeScaleCollection', CheckboxType::class, [
                'label'    => 'registry.treatment.form.large_scale_collection',
                'required' => false,
            ])
            ->add('vulnerablePeople', CheckboxType::class, [
                'label'    => 'registry.treatment.form.vulnerable_people',
                'required' => false,
            ])
            ->add('dataCrossing', CheckboxType::class, [
                'label'    => 'registry.treatment.form.data_crossing',
                'required' => false,
            ])
            ->add('authorizedPeople', TextType::class, [
                'label'    => 'registry.treatment.form.authorized_people',
                'required' => false,
            ])
            ->add('active', ChoiceType::class, [
                'label'    => 'registry.treatment.form.active',
                'required' => true,
                'choices'  => [
                    'label.active'   => true,
                    'label.inactive' => false,
                ],
                'multiple' => false,
                'expanded' => true,
            ])
        ;
    }

    /**
     * Provide type options.
     *
     * @param OptionsResolver $resolver
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
