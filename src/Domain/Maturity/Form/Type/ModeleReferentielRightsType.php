<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2021 Soluris - Solutions Numériques Territoriales Innovantes
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

namespace App\Domain\Maturity\Form\Type;

use App\Domain\Admin\Dictionary\DuplicationTargetOptionDictionary;
use App\Domain\User\Model\Collectivity;
use Doctrine\ORM\EntityRepository;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModeleReferentielRightsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['data']->getAuthorizedCollectivities()) {
            $dataOptionRightSelectionValue = DuplicationTargetOptionDictionary::KEY_PER_COLLECTIVITY;
        } else {
            $dataOptionRightSelectionValue = DuplicationTargetOptionDictionary::KEY_PER_TYPE;
        }

        $builder
            ->add('optionRightSelection', DictionaryType::class, [
                'name'     => DuplicationTargetOptionDictionary::NAME,
                'label'    => false,
                'mapped'   => false,
                'required' => true,
                'multiple' => false,
                'expanded' => true,
                'data'     => $dataOptionRightSelectionValue,
            ])
            ->add('authorizedCollectivityTypes', DictionaryType::class, [
                'name'     => 'user_collectivity_type',
                'label'    => false,
                'required' => false,
                'multiple' => true,
                'expanded' => false,
                'attr'     => [
                    'size' => 6,
                ],
            ])
            ->add('authorizedCollectivities', EntityType::class, [
                'class'         => Collectivity::class,
                'label'         => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC');
                },
                'required'      => false,
                'multiple'      => true,
                'expanded'      => false,
                'attr'          => [
                    'size' => 18,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'validation_groups' => [
                    'default',
                ],
            ]);
    }
}
