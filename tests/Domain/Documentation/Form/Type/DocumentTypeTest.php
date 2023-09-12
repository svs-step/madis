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

use App\Application\Form\Extension\SanitizeTextFormType;
use App\Domain\Documentation\Form\Type\DocumentType;
use App\Domain\Documentation\Model\Document;
use App\Tests\Utils\FormTypeHelper;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DocumentTypeTest extends FormTypeHelper
{
    use ProphecyTrait;

    private $requestStack;

    public function setUp(): void
    {
        $this->requestStack = $this->prophesize(RequestStack::class)->reveal();
        parent::setUp();
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf(AbstractType::class, new DocumentType($this->requestStack, '4M'));
    }

    public function testBuildForm()
    {
        $builder = [
            'isLink'            => HiddenType::class,
            'name'              => SanitizeTextFormType::class,
            'categories'        => EntityType::class,
            'pinned'            => CheckboxType::class,
            'thumbUploadedFile' => FileType::class,
        ];

        $dt = new DocumentType($this->requestStack, '4M');

        $prophecy = $this->prophesizeBuilder($builder, true, $dt);

        $dt->buildForm($prophecy, []);
    }

    public function testConfigureOptions(): void
    {
        $defaults = [
            'data_class'        => Document::class,
            'validation_groups' => [
                'default',
                'document',
            ],
        ];

        $resolverProphecy = $this->prophesize(OptionsResolver::class);
        $resolverProphecy->setDefaults($defaults)->shouldBeCalled();

        (new DocumentType($this->requestStack, '4M'))->configureOptions($resolverProphecy->reveal());
    }
}
