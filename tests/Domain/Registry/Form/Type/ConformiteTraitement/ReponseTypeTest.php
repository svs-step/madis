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

namespace App\Tests\Domain\Registry\Form\Type\ConformiteTraitement;

use App\Domain\Registry\Form\Type\ConformiteTraitement\ReponseType;
use App\Domain\Registry\Model\ConformiteTraitement\Reponse;
use App\Tests\Utils\FormTypeHelper;
use Prophecy\Argument;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class ReponseTypeTest extends FormTypeHelper
{
    /**
     * @var Security
     */
    private $security;

    public function setUp(): void
    {
        $this->security = $this->prophesize(Security::class)->reveal();
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf(AbstractType::class, new ReponseType($this->security));
    }

    public function testBuildForm()
    {
        $builder = [
            'conforme' => ChoiceType::class,
        ];

        $builderProphecy = $this->prophesizeBuilder($builder, false);

        $builderProphecy
            ->addEventListener(FormEvents::PRE_SET_DATA, Argument::any())
            ->shouldBeCalled()
        ;

        (new ReponseType($this->security))->buildForm($builderProphecy->reveal(), ['data' => 'foo']);
    }

    public function testConfigureOptions(): void
    {
        $defaults = [
            'data_class'        => Reponse::class,
            'validation_groups' => [
                'default',
                'reponse',
            ],
        ];

        $resolverProphecy = $this->prophesize(OptionsResolver::class);
        $resolverProphecy->setDefaults($defaults)->shouldBeCalled();

        (new ReponseType($this->security))->configureOptions($resolverProphecy->reveal());
    }
}
