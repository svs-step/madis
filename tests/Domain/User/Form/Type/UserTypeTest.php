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
use App\Domain\User\Model\User;
use App\Tests\Utils\FormTypeHelper;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Prophecy\Argument;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class UserTypeTest extends FormTypeHelper
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationCheckerProphecy;

    /**
     * @var UserType
     */
    private $formType;

    protected function setUp()
    {
        $this->authorizationCheckerProphecy = $this->prophesize(AuthorizationCheckerInterface::class);

        $this->formType = new UserType(
            $this->authorizationCheckerProphecy->reveal()
        );
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf(AbstractType::class, $this->formType);
    }

    public function testBuildFormAdmin()
    {
        $builder = [
            'firstName'     => TextType::class,
            'lastName'      => TextType::class,
            'email'         => EmailType::class,
            'collectivity'  => EntityType::class,
            'roles'         => DictionaryType::class,
            'enabled'       => CheckboxType::class,
            'plainPassword' => RepeatedType::class,
        ];

        $this->authorizationCheckerProphecy->isGranted('ROLE_ADMIN')->shouldBeCalled()->willReturn(true);

        $builderProphecy = $this->prophesizeBuilder($builder, false);
        $builderProphecy->get('roles')->shouldBeCalled()->willReturn($builderProphecy);
        $builderProphecy
            ->addModelTransformer(Argument::type(RoleTransformer::class))
            ->shouldBeCalled()
        ;

        $this->formType->buildForm($builderProphecy->reveal(), []);
    }

    public function testBuildFormUser()
    {
        $builder = [
            'firstName'     => TextType::class,
            'lastName'      => TextType::class,
            'email'         => EmailType::class,
            'plainPassword' => RepeatedType::class,
        ];

        $this->authorizationCheckerProphecy->isGranted('ROLE_ADMIN')->shouldBeCalled()->willReturn(false);

        // No transformer since not granted admin
        $builderProphecy = $this->prophesizeBuilder($builder, false);
        $builderProphecy->get('roles')->shouldNotBeCalled();
        $builderProphecy->addModelTransformer(Argument::cetera())->shouldNotBeCalled();

        $this->formType->buildForm($builderProphecy->reveal(), []);
    }

    public function testConfigureOptions(): void
    {
        $defaults = [
            'data_class'        => User::class,
            'validation_groups' => [
                'default',
                'user',
            ],
        ];

        $resolverProphecy = $this->prophesize(OptionsResolver::class);
        $resolverProphecy->setDefaults($defaults)->shouldBeCalled();

        $this->formType->configureOptions($resolverProphecy->reveal());
    }
}
