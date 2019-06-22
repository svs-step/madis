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

use App\Domain\Registry\Form\Type\Embeddable\AddressType;
use App\Domain\Registry\Model\Contractor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContractorType extends AbstractType
{
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
                'label'    => 'registry.contractor.form.name',
                'required' => true,
            ])
            ->add('referent', TextType::class, [
                'label'    => 'registry.contractor.form.referent',
                'required' => false,
            ])
            ->add('contractualClausesVerified', CheckboxType::class, [
                'label'    => 'registry.contractor.form.contractual_clauses_verified',
                'required' => false,
            ])
            ->add('conform', CheckboxType::class, [
                'label'    => 'registry.contractor.form.conform',
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
                'data_class'        => Contractor::class,
                'validation_groups' => [
                    'default',
                    'contractor',
                ],
            ]);
    }
}
