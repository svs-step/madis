<?php

/**
 * This file is part of the SOLURIS - RGPD Management application.
 *
 * (c) Donovan Bourlard <donovan.bourlard@outlook.fr>
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
            ->add('concernedPeople', DictionaryType::class, [
                'label'    => 'registry.treatment.form.concerned_people',
                'name'     => 'registry_treatment_concerned_people',
                'required' => true,
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('dataCategory', TextareaType::class, [
                'label'    => 'registry.treatment.form.data_category',
                'required' => false,
                'attr'     => [
                    'rows' => 3,
                ],
            ])
            ->add('sensibleInformations', CheckboxType::class, [
                'label'    => 'registry.treatment.form.sensible_informations',
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
            ->add('securityEncryption', ComplexChoiceType::class, [
                'label'    => 'registry.treatment.form.security_encryption',
                'required' => false,
            ])
            ->add('securityOther', ComplexChoiceType::class, [
                'label'    => 'registry.treatment.form.security_other',
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
