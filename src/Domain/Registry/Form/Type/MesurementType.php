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

use App\Domain\Registry\Model\Mesurement;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MesurementType extends AbstractType
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
                'label'    => 'registry.mesurement.form.name',
                'required' => true,
            ])
            /*
            ->add('type', DictionaryType::class, [
                'label'    => 'registry.mesurement.form.type',
                'name'     => 'registry_mesurement_type',
                'required' => true,
                'multiple' => false,
                'expanded' => true,
            ])
            */
            ->add('description', TextareaType::class, [
                'label'    => 'registry.mesurement.form.description',
                'required' => false,
                'attr'     => [
                    'rows' => 3,
                ],
            ])
            ->add('cost', TextType::class, [
                'label'    => 'registry.mesurement.form.cost',
                'required' => false,
            ])
            ->add('charge', TextType::class, [
                'label'    => 'registry.mesurement.form.charge',
                'required' => false,
            ])
            ->add('status', DictionaryType::class, [
                'label'    => 'registry.mesurement.form.status',
                'name'     => 'registry_mesurement_status',
                'required' => true,
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('planificationDate', DateType::class, [
                'label'    => 'registry.mesurement.form.planification_date',
                'required' => false,
                'widget'   => 'single_text',
                'format'   => 'dd/MM/yyyy',
                'html5'    => false,
                'attr'     => [
                    'class' => 'datepicker',
                ],
            ])
            ->add('comment', TextType::class, [
                'label'    => 'registry.mesurement.form.comment',
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
                'data_class'        => Mesurement::class,
                'validation_groups' => [
                    'default',
                    'mesurement',
                ],
            ]);
    }
}
