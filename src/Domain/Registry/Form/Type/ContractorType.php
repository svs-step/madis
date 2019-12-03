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
