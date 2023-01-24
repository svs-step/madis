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

use App\Domain\Registry\Model\Contractor;
use App\Domain\Registry\Model\Mesurement;
use App\Domain\Registry\Model\Proof;
use App\Domain\Registry\Model\Request;
use App\Domain\Registry\Model\Tool;
use App\Domain\Registry\Model\Treatment;
use App\Domain\Registry\Model\Violation;
use App\Domain\User\Model\User;
use Doctrine\ORM\EntityRepository;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class ToolType extends AbstractType
{
    /**
     * Build type form.
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Tool $tool */
        $tool = $options['data'] ?? null;
        $builder
            ->add('name', TextType::class, [
                'label'    => 'registry.tool.form.name',
                'required' => true,
                'attr'     => [
                    'maxlength' => 255,
                ],
            ])

            ->add('type', DictionaryType::class, [
                'label'    => 'registry.tool.form.type',
                'name'     => 'registry_tool_type',
                'required' => true,
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('editor', CheckboxType::class, [
                'label'    => 'registry.tool.form.editor',
                'required' => false,
                'attr'     => [
                    'maxlength' => 255,
                ],
            ])

            ->add('contractors', EntityType::class, [
                'label'         => 'registry.tool.form.contractor',
                'class'         => Contractor::class,
                'required'      => false,
                'multiple'      => true,
                'expanded'      => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->addOrderBy('c.name', 'asc')
                    ;
                },
                'attr'          => [
                    'class' => 'selectpicker',
                    'title' => 'placeholder.multiple_select',
                ],
            ])

            ->add('treatments', EntityType::class, [
                'label'         => 'registry.tool.form.treatment',
                'class'         => Treatment::class,
                'required'      => false,
                'multiple'      => true,
                'expanded'      => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->addOrderBy('c.name', 'asc')
                    ;
                },
                'attr'          => [
                    'class' => 'selectpicker',
                    'title' => 'placeholder.multiple_select',
                ],
            ])

            ->add('proofs', EntityType::class, [
                'label'         => 'registry.tool.form.proof',
                'class'         => Proof::class,
                'required'      => false,
                'multiple'      => true,
                'expanded'      => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->addOrderBy('c.name', 'asc')
                    ;
                },
                'attr'          => [
                    'class' => 'selectpicker',
                    'title' => 'placeholder.multiple_select',
                ],
            ])

            ->add('actions', EntityType::class, [
                'label'         => 'registry.tool.form.action',
                'class'         => Mesurement::class,
                'required'      => false,
                'multiple'      => true,
                'expanded'      => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->addOrderBy('c.name', 'asc')
                    ;
                },
                'attr'          => [
                    'class' => 'selectpicker',
                    'title' => 'placeholder.multiple_select',
                ],
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
                'data_class'        => Tool::class,
                'validation_groups' => [
                    'default',
                    'tool',
                ],
            ]);
    }
}
