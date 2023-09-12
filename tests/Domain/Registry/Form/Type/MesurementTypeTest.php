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

namespace App\Tests\Domain\Registry\Form\Type;

use App\Application\Form\Extension\SanitizeTextFormType;
use App\Domain\Registry\Form\Type\MesurementType;
use App\Domain\Registry\Model\Mesurement;
use App\Domain\User\Model\Collectivity;
use App\Tests\Utils\FormTypeHelper;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class MesurementTypeTest extends FormTypeHelper
{
    use ProphecyTrait;

    private MesurementType $formType;

    protected function setUp(): void
    {
        $this->security = $this->prophesize(Security::class);

        $this->formType = new MesurementType(
            $this->security->reveal(),
        );
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf(AbstractType::class, $this->formType);
    }

    public function testBuildForm()
    {
        $mesurement   = new Mesurement();
        $collectivity = new Collectivity();
        $collectivity->setIsServicesEnabled(true);
        $mesurement->setCollectivity($collectivity);

        $builder = [
            'name'              => SanitizeTextFormType::class,
            'description'       => TextareaType::class,
            'cost'              => SanitizeTextFormType::class,
            'charge'            => SanitizeTextFormType::class,
            'status'            => DictionaryType::class,
            'planificationDate' => DateType::class,
            'comment'           => SanitizeTextFormType::class,
            'priority'          => DictionaryType::class,
            'manager'           => SanitizeTextFormType::class,
            'contractors'       => EntityType::class,
            'treatments'        => EntityType::class,
            'violations'        => EntityType::class,
            'requests'          => EntityType::class,
            'updatedBy'         => HiddenType::class,
        ];

        $this->formType->buildForm($this->prophesizeBuilder($builder), ['data' => $mesurement]);
    }

    public function testConfigureOptions(): void
    {
        $defaults = [
            'data_class'        => Mesurement::class,
            'validation_groups' => [
                'default',
                'mesurement',
            ],
        ];

        $resolverProphecy = $this->prophesize(OptionsResolver::class);
        $resolverProphecy->setDefaults($defaults)->shouldBeCalled();

        $this->formType->configureOptions($resolverProphecy->reveal());
    }
}
