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

namespace App\Tests\Domain\User\Form\Type;

use App\Domain\User\Form\Type\AddressType;
use App\Domain\User\Form\Type\CollectivityType;
use App\Domain\User\Form\Type\ContactType;
use App\Domain\User\Model\Collectivity;
use App\Tests\Utils\FormTypeHelper;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class CollectivityTypeTest extends FormTypeHelper
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationCheckerProphecy;

    /**
     * @var CollectivityType
     */
    private $formType;

    protected function setUp()
    {
        $this->authorizationCheckerProphecy = $this->prophesize(AuthorizationCheckerInterface::class);

        $this->formType = new CollectivityType(
            $this->authorizationCheckerProphecy->reveal()
        );
    }

    public function testInstanceOf(): void
    {
        $this->assertInstanceOf(AbstractType::class, $this->formType);
    }

    public function testBuildFormAdmin(): void
    {
        $builder = [
            'name'                                => TextType::class,
            'shortName'                           => TextType::class,
            'type'                                => DictionaryType::class,
            'siren'                               => NumberType::class,
            'active'                              => ChoiceType::class,
            'website'                             => UrlType::class,
            'address'                             => AddressType::class,
            'legalManager'                        => ContactType::class,
            'referent'                            => ContactType::class,
            'differentDpo'                        => CheckboxType::class,
            'dpo'                                 => ContactType::class,
            'differentItManager'                  => CheckboxType::class,
            'itManager'                           => ContactType::class,
            'reportingBlockManagementCommitment'  => CKEditorType::class,
            'reportingBlockContinuousImprovement' => CKEditorType::class,
            'comiteIlContacts'                    => CollectionType::class,
        ];

        $this->authorizationCheckerProphecy->isGranted('ROLE_ADMIN')->shouldBeCalled()->willReturn(true);

        $this->formType->buildForm($this->prophesizeBuilder($builder), []);
    }

    public function testBuildFormUser(): void
    {
        $builder = [
            'legalManager'                        => ContactType::class,
            'referent'                            => ContactType::class,
            'differentDpo'                        => CheckboxType::class,
            'dpo'                                 => ContactType::class,
            'differentItManager'                  => CheckboxType::class,
            'itManager'                           => ContactType::class,
            'reportingBlockManagementCommitment'  => CKEditorType::class,
            'reportingBlockContinuousImprovement' => CKEditorType::class,
            'comiteIlContacts'                    => CollectionType::class,
        ];

        $this->authorizationCheckerProphecy->isGranted('ROLE_ADMIN')->shouldBeCalled()->willReturn(false);

        $this->formType->buildForm($this->prophesizeBuilder($builder), []);
    }

    public function testConfigureOptions(): void
    {
        $defaults = [
            'data_class'        => Collectivity::class,
            'validation_groups' => [
                'default',
            ],
        ];

        $resolverProphecy = $this->prophesize(OptionsResolver::class);
        $resolverProphecy->setDefaults($defaults)->shouldBeCalled();

        $this->formType->configureOptions($resolverProphecy->reveal());
    }
}
