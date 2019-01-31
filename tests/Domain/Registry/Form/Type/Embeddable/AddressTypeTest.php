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
