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

namespace App\Tests\Domain\User\Controller;

use App\Application\Controller\CRUDController;
use App\Domain\User\Controller\UserController;
use App\Domain\User\Form\Type\UserType;
use App\Domain\User\Model;
use App\Domain\User\Repository;
use App\Tests\Utils\ReflectionTrait;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Translation\TranslatorInterface;

class UserControllerTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @var EntityManagerInterface
     */
    private $managerProphecy;

    /**
     * @var TranslatorInterface
     */
    private $translatorProphecy;

    /**
     * @var Repository\User
     */
    private $repositoryProphecy;

    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactoryProphecy;

    /**
     * @var UserController
     */
    private $controller;

    public function setUp()
    {
        $this->managerProphecy        = $this->prophesize(EntityManagerInterface::class);
        $this->translatorProphecy     = $this->prophesize(TranslatorInterface::class);
        $this->repositoryProphecy     = $this->prophesize(Repository\User::class);
        $this->encoderFactoryProphecy = $this->prophesize(EncoderFactoryInterface::class);

        $this->controller = new UserController(
            $this->managerProphecy->reveal(),
            $this->translatorProphecy->reveal(),
            $this->repositoryProphecy->reveal(),
            $this->encoderFactoryProphecy->reveal()
        );
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf(CRUDController::class, $this->controller);
    }

    public function testGetDomain()
    {
        $this->assertEquals(
            'user',
            $this->invokeMethod($this->controller, 'getDomain', [])
        );
    }

    public function testGetModel()
    {
        $this->assertEquals(
            'user',
            $this->invokeMethod($this->controller, 'getModel', [])
        );
    }

    public function testGetModelClass()
    {
        $this->assertEquals(
            Model\User::class,
            $this->invokeMethod($this->controller, 'getModelClass', [])
        );
    }

    public function testGetFormType()
    {
        $this->assertEquals(
            UserType::class,
            $this->invokeMethod($this->controller, 'getFormType', [])
        );
    }

    /**
     * Test formPrePersistData
     * There is a plain password, encode it.
     */
    public function testFormPrePersistData()
    {
        $plainPassword   = 'plainPassword';
        $encodedPassword = 'encodedPassword';

        $userProphecy = $this->prophesize(Model\User::class);
        $userProphecy->getPlainPassword()->shouldBeCalled()->willReturn($plainPassword);
        $userProphecy->setPassword($encodedPassword)->shouldBeCalled();
        $userProphecy->eraseCredentials()->shouldBeCalled();

        $encoderProphecy = $this->prophesize(PasswordEncoderInterface::class);
        $encoderProphecy->encodePassword($plainPassword, '')->shouldBeCalled()->willReturn($encodedPassword);
        $this->encoderFactoryProphecy->getEncoder($userProphecy->reveal())->shouldBeCalled()->willReturn($encoderProphecy->reveal());

        $this->controller->formPrePersistData($userProphecy->reveal());
    }

    /**
     * Test formPrePersistData
     * There is a plain password, encode it.
     */
    public function testFormPrePersistDataWithoutPasswordToEncode()
    {
        $plainPassword = null;

        $userProphecy = $this->prophesize(Model\User::class);
        $userProphecy->getPlainPassword()->shouldBeCalled()->willReturn($plainPassword);

        // No encoder & password setting process called since no password to encode
        $userProphecy->setPassword(Argument::cetera())->shouldNotBeCalled();
        $userProphecy->eraseCredentials()->shouldNotBeCalled();
        $this->encoderFactoryProphecy->getEncoder(Argument::cetera())->shouldNotBeCalled();

        $this->controller->formPrePersistData($userProphecy->reveal());
    }
}
