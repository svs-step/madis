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
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RequestType extends AbstractType
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
            ->add('object', DictionaryType::class, [
                'label'    => 'registry.request.form.object',
                'name'     => 'registry_request_object',
                'required' => true,
                'expanded' => true,
            ])
            ->add('otherObject', TextType::class, [
                'label'    => 'registry.request.form.other_object',
                'required' => false,
            ])
            ->add('date', DateType::class, [
                'label'    => 'registry.request.form.date',
                'required' => true,
            ])
            ->add('reason', TextType::class, [
                'label'    => 'registry.request.form.reason',
                'required' => false,
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
                'data_class'        => Request::class,
                'validation_groups' => [
                    'default',
                    'request',
                ],
            ]);
    }
}
