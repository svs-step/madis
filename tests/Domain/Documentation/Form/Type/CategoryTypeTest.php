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

namespace App\Tests\Domain\Documentation\Form\Type;

use App\Domain\Documentation\Form\Type\CategoryType;
use App\Domain\Documentation\Model\Category;
use App\Tests\Utils\FormTypeHelper;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryTypeTest extends FormTypeHelper
{
    use ProphecyTrait;

    public function testInstanceOf()
    {
        $this->assertInstanceOf(AbstractType::class, new CategoryType());
    }

    public function testBuildForm()
    {
        $builder = [
            'name' => TextType::class,
        ];

        (new CategoryType())->buildForm($this->prophesizeBuilder($builder), []);
    }

    public function testConfigureOptions(): void
    {
        $defaults = [
            'data_class'        => Category::class,
            'validation_groups' => [
                'default',
                'category',
            ],
        ];

        $resolverProphecy = $this->prophesize(OptionsResolver::class);
        $resolverProphecy->setDefaults($defaults)->shouldBeCalled();

        (new CategoryType())->configureOptions($resolverProphecy->reveal());
    }
}
