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

use App\Domain\User\Form\Type\ResetPasswordType;
use App\Tests\Utils\FormTypeHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResetPasswordTypeTest extends FormTypeHelper
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(AbstractType::class, new ResetPasswordType());
    }

    public function testBuildForm()
    {
        $builder = [
            'plainPassword' => RepeatedType::class,
        ];

        $builderProphecy = $this->prophesizeBuilder($builder, false);

        (new ResetPasswordType())->buildForm($builderProphecy->reveal(), []);
    }

    public function testConfigureOptions(): void
    {
        $defaults = [
            'validation_groups' => 'default',
        ];

        $resolverProphecy = $this->prophesize(OptionsResolver::class);
        $resolverProphecy->setDefaults($defaults)->shouldBeCalled();

        (new ResetPasswordType())->configureOptions($resolverProphecy->reveal());
    }
}
