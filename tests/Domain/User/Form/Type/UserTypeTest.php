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

use App\Domain\User\Form\DataTransformer\RoleTransformer;
use App\Domain\User\Form\Type\UserType;
use App\Tests\Utils\FormTypeHelper;
use Prophecy\Argument;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UserTypeTest extends FormTypeHelper
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(AbstractType::class, new UserType());
    }

    public function testBuildForm()
    {
        $builder = [
            'firstName'     => TextType::class,
            'lastName'      => TextType::class,
            'email'         => EmailType::class,
            'collectivity'  => EntityType::class,
            'roles'         => ChoiceType::class,
            'plainPassword' => PasswordType::class,
        ];

        $builderProphecy = $this->prophesizeBuilder($builder, false);
        $builderProphecy->get('roles')->shouldBeCalled()->willReturn($builderProphecy);
        $builderProphecy
            ->addModelTransformer(Argument::type(RoleTransformer::class))
            ->shouldBeCalled()
        ;

        (new UserType())->buildForm($builderProphecy->reveal(), []);
    }
}
