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
use App\Domain\Registry\Form\Type\ProofType;
use App\Domain\Registry\Model;
use App\Domain\User\Model as UserModel;
use App\Tests\Utils\FormTypeHelper;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class ProofTypeTest extends FormTypeHelper
{
    use ProphecyTrait;

    /**
     * @var Security
     */
    private $security;

    /**
     * @var ProofType
     */
    private $sut;

    protected function setUp(): void
    {
        $this->security = $this->prophesize(Security::class);

        $this->sut = new ProofType(
            $this->security->reveal(),
            '4M',
        );

        $user         = new UserModel\User();
        $collectivity = new UserModel\Collectivity();
        $user->setCollectivity($collectivity);
        $this->security->getUser()->willReturn($user);

        parent::setUp();
    }

    /**
     * Test inheritance.
     */
    public function testInstanceOf()
    {
        $this->assertInstanceOf(AbstractType::class, $this->sut);
    }

    /**
     * Test buildForm.
     */
    public function testBuildForm()
    {
        $proof        = new Model\Proof();
        $collectivity = new UserModel\Collectivity();

        $proof->setCollectivity($collectivity);
        $builder = [
            'name'         => SanitizeTextFormType::class,
            'type'         => DictionaryType::class,
            'documentFile' => FileType::class,
            'comment'      => SanitizeTextFormType::class,
            'treatments'   => EntityType::class,
            'contractors'  => EntityType::class,
            'mesurements'  => EntityType::class,
            'requests'     => EntityType::class,
            'violations'   => EntityType::class,
        ];

        $this->sut->buildForm(
            $this->prophesizeBuilder($builder),
            ['data' => $proof]
        );
    }

    /**
     * Test configureOptions.
     */
    public function testConfigureOptions(): void
    {
        $defaults = [
            'data_class'        => Model\Proof::class,
            'validation_groups' => [
                'default',
                'proof',
            ],
        ];

        $resolverProphecy = $this->prophesize(OptionsResolver::class);
        $resolverProphecy->setDefaults($defaults)->shouldBeCalled();

        $this->sut->configureOptions($resolverProphecy->reveal());
    }
}
