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

namespace App\Tests\Domain\Registry\Form\Type\Embeddable;

use App\Domain\Registry\Form\Type\Embeddable\AddressType;
use App\Domain\Registry\Model\Embeddable\Address;
use App\Tests\Utils\FormTypeHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressTypeTest extends FormTypeHelper
{
    public function testInstanceOf(): void
    {
        $this->assertInstanceOf(AbstractType::class, new AddressType());
    }

    public function testBuildForm(): void
    {
        $builder = [
            'lineOne'     => TextType::class,
            'lineTwo'     => TextType::class,
            'city'        => TextType::class,
            'zipCode'     => TextType::class,
            'mail'        => EmailType::class,
            'phoneNumber' => TextType::class,
        ];

        (new AddressType())->buildForm($this->prophesizeBuilder($builder), ['validation_groups' => []]);
    }

    public function testConfigureOptions(): void
    {
        $defaults = [
            'data_class'        => Address::class,
            'validation_groups' => [
                'default',
            ],
        ];

        $resolverProphecy = $this->prophesize(OptionsResolver::class);
        $resolverProphecy->setDefaults($defaults)->shouldBeCalled();

        (new AddressType())->configureOptions($resolverProphecy->reveal());
    }
}
