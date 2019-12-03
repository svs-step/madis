<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2019 Soluris - Solutions Numériques Territoriales Innovantes
 * @author ANODE <contact@agence-anode.fr>
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

namespace App\Domain\Admin\Form\Type;

use App\Domain\Admin\Dictionary\DuplicationTargetOptionDictionary;
use App\Domain\Admin\DTO\DuplicationFormDTO;
use App\Domain\User\Model as UserModel;
use Doctrine\ORM\EntityRepository;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DuplicationType extends AbstractType
{
    /**
     * Build type form.
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', DictionaryType::class, [
                'name'     => 'admin_duplication_type',
                'label'    => 'admin.duplication.form.type',
                'required' => true,
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('sourceCollectivity', EntityType::class, [
                'class'         => UserModel\Collectivity::class,
                'label'         => 'admin.duplication.form.source_collectivity',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC');
                },
                'required' => true,
                'multiple' => false,
                'expanded' => false,
            ])
            ->add('data', ChoiceType::class, [
                'label'    => 'admin.duplication.form.data',
                'required' => true,
                'multiple' => true,
                'expanded' => false,
                'choices'  => [],
                'attr'     => [
                    'size' => 15,
                ],
            ])
            ->add('targetOption', DictionaryType::class, [
                'name'     => DuplicationTargetOptionDictionary::NAME,
                'label'    => false,
                'required' => true,
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('targetCollectivityTypes', DictionaryType::class, [
                'name'     => 'user_collectivity_type',
                'label'    => false,
                'required' => false,
                'multiple' => true,
                'expanded' => false,
                'attr'     => [
                    'size' => 6,
                ],
            ])
            ->add('targetCollectivities', EntityType::class, [
                'class'         => UserModel\Collectivity::class,
                'label'         => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC');
                },
                'required'   => false,
                'multiple'   => true,
                'expanded'   => false,
                'attr'       => [
                    'size' => 18,
                ],
            ])
        ;

        // Reset view transformer to disable mapping between choices values & given values
        // Since we send "random" values which are not defined in Form, no need to validate sended values with transformer
        // This data initial view transformer is \Symfony\Component\Form\Extension\Core\DataTransformer\ChoicesToValuesTransformer
        $builder->get('data')->resetViewTransformers();
    }

    /**
     * Provide type options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class'        => DuplicationFormDTO::class,
                'validation_groups' => [
                    'default',
                ],
            ]);
    }
}
