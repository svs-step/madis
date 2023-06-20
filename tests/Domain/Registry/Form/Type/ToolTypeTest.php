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

use App\Domain\Registry\Form\Type\Embeddable\ComplexChoiceType;
use App\Domain\Registry\Form\Type\ToolType;
use App\Domain\Registry\Model\Tool;
use App\Domain\User\Model\Collectivity;
use App\Tests\Utils\FormTypeHelper;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class ToolTypeTest extends FormTypeHelper
{
    use ProphecyTrait;

    /**
     * @var ToolType
     */
    private $formType;

    private $security;

    protected function setUp(): void
    {
        $this->security = $this->prophesize(Security::class);
        $this->formType = new ToolType($this->security->reveal());
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf(AbstractType::class, $this->formType);
    }

    public function testBuildForm()
    {
        $tool         = new Tool();
        $collectivity = new Collectivity();
        $collectivity->setIsServicesEnabled(true);
        $tool->setCollectivity($collectivity);

        $builder = [
            'name'               => TextType::class,
            'type'               => DictionaryType::class,
            'description'        => TextareaType::class,
            'other_info'         => TextareaType::class,
            'editor'             => TextType::class,
            'manager'            => TextType::class,
            'contractors'        => EntityType::class,
            'prod_date'          => DateType::class,
            'country_type'       => ChoiceType::class,
            'country_name'       => TextType::class,
            'country_guarantees' => TextType::class,
            'archival'           => ComplexChoiceType::class,
            'tracking'           => ComplexChoiceType::class,
            'encrypted'          => ComplexChoiceType::class,
            'access_control'     => ComplexChoiceType::class,
            'update'             => ComplexChoiceType::class,
            'backup'             => ComplexChoiceType::class,
            'deletion'           => ComplexChoiceType::class,
            'has_comment'        => ComplexChoiceType::class,
            'other'              => ComplexChoiceType::class,
            'updatedBy'          => HiddenType::class,
        ];

        $this->formType->buildForm($this->prophesizeBuilder($builder), ['data' => $tool]);
    }

    public function testConfigureOptions(): void
    {
        $defaults = [
            'data_class'        => Tool::class,
            'validation_groups' => [
                'default',
                'tool',
            ],
        ];

        $resolverProphecy = $this->prophesize(OptionsResolver::class);
        $resolverProphecy->setDefaults($defaults)->shouldBeCalled();

        $this->formType->configureOptions($resolverProphecy->reveal());
    }
}
