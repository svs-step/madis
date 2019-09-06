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

namespace App\Tests\Domain\Admin\Form\Type;

use App\Domain\Admin\DTO\DuplicationFormDTO;
use App\Domain\Admin\Form\Type\DuplicationType;
use App\Tests\Utils\FormTypeHelper;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormConfigBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DuplicationTypeTest extends FormTypeHelper
{
    /**
     * Test inheritance.
     */
    public function testInstanceof(): void
    {
        $this->assertInstanceOf(AbstractType::class, new DuplicationType());
    }

    /**
     * Test buildForm.
     */
    public function testBuildForm(): void
    {
        $builder = [
            'type'                    => DictionaryType::class,
            'sourceCollectivity'      => EntityType::class,
            'data'                    => ChoiceType::class,
            'targetOption'            => DictionaryType::class,
            'targetCollectivityTypes' => DictionaryType::class,
            'targetCollectivities'    => EntityType::class,
        ];

        $formType = new DuplicationType();

        // Generate all prophesized fields & add data transformer call
        $builderProphecy                    = $this->prophesizeBuilder($builder, false);
        $dataFieldFormConfigBuilderProphecy = $this->prophesize(FormConfigBuilderInterface::class);
        $dataFieldFormConfigBuilderProphecy->resetViewTransformers()->shouldBeCalled();
        $builderProphecy->get('data')->shouldBeCalled()->willReturn($dataFieldFormConfigBuilderProphecy);

        $formType->buildForm(
            $builderProphecy->reveal(),
            []
        );
    }

    /**
     * Test configureOptions.
     */
    public function testConfigureOptions(): void
    {
        $defaults = [
            'data_class'        => DuplicationFormDTO::class,
            'validation_groups' => [
                'default',
            ],
        ];

        $resolverProphecy = $this->prophesize(OptionsResolver::class);
        $resolverProphecy->setDefaults($defaults)->shouldBeCalled();

        (new DuplicationType())->configureOptions($resolverProphecy->reveal());
    }
}
