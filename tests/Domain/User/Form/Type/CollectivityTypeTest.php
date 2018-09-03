<?php

/**
 * This file is part of the SOLURIS - RGPD Management application.
 *
 * (c) Donovan Bourlard <donovan.bourlard@outlook.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Tests\Domain\User\Form\Type;

use App\Domain\User\Form\Type\AddressType;
use App\Domain\User\Form\Type\CollectivityType;
use App\Domain\User\Form\Type\ContactType;
use App\Tests\Utils\FormTypeHelper;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
            'name'               => TextType::class,
            'shortName'          => TextType::class,
            'type'               => DictionaryType::class,
            'siren'              => NumberType::class,
            'active'             => ChoiceType::class,
            'website'            => UrlType::class,
            'address'            => AddressType::class,
            'legalManager'       => ContactType::class,
            'referent'           => ContactType::class,
            'differentDpo'       => CheckboxType::class,
            'dpo'                => ContactType::class,
            'differentItManager' => CheckboxType::class,
            'itManager'          => ContactType::class,
        ];

        $this->authorizationCheckerProphecy->isGranted('ROLE_ADMIN')->shouldBeCalled()->willReturn(true);

        $this->formType->buildForm($this->prophesizeBuilder($builder), []);
    }

    public function testBuildFormUser(): void
    {
        $builder = [
            'legalManager'       => ContactType::class,
            'referent'           => ContactType::class,
            'differentDpo'       => CheckboxType::class,
            'dpo'                => ContactType::class,
            'differentItManager' => CheckboxType::class,
            'itManager'          => ContactType::class,
        ];

        $this->authorizationCheckerProphecy->isGranted('ROLE_ADMIN')->shouldBeCalled()->willReturn(false);

        $this->formType->buildForm($this->prophesizeBuilder($builder), []);
    }

    public function testConfigureOptions(): void
    {
        $defaults = [
            'validation_groups' => [
                'default',
            ],
        ];

        $resolverProphecy = $this->prophesize(OptionsResolver::class);
        $resolverProphecy->setDefaults($defaults)->shouldBeCalled();

        $this->formType->configureOptions($resolverProphecy->reveal());
    }
}
