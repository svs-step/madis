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

namespace App\Tests\Domain\User\Component;

use App\Domain\User\Component\Mailer;
use App\Domain\User\Model;
use App\Tests\Utils\ReflectionTrait;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;

class MailerTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @var \Swift_Mailer
     */
    private $mailerProphecy;

    /**
     * @var TranslatorInterface
     */
    private $translatorProphecy;

    /**
     * @var Environment
     */
    private $twigProphecy;

    /**
     * @var string
     */
    private $senderEmail;

    /**
     * @var string
     */
    private $senderName;

    /**
     * @var Mailer
     */
    private $mailer;

    public function setUp()
    {
        $this->mailerProphecy     = $this->prophesize(\Swift_Mailer::class);
        $this->translatorProphecy = $this->prophesize(TranslatorInterface::class);
        $this->twigProphecy       = $this->prophesize(Environment::class);
        $this->senderEmail        = 'foo@bar.baz';
        $this->senderName         = 'Jane Doe';

        $this->mailer = new Mailer(
            $this->mailerProphecy->reveal(),
            $this->translatorProphecy->reveal(),
            $this->twigProphecy->reveal(),
            $this->senderEmail,
            $this->senderName
        );
    }

    /**
     * Test send (Email is sent).
     *
     * @throws \ReflectionException
     */
    public function testSend()
    {
        $this->mailerProphecy->send(Argument::type(\Swift_Message::class))->shouldBeCalled()->willReturn(1);

        $this->assertEquals(
            1,
            $this->invokeMethod(
                $this->mailer,
                'send',
                ['to@gmail.com', 'subject', 'body']
            )
        );
    }

    /**
     * Test sendForgetPassword.
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function testSendForgetPassword()
    {
        $userEmail = 'user@mail.com';

        $userProphecy = $this->prophesize(Model\User::class);
        $userProphecy->getEmail()->shouldBeCalled()->willReturn($userEmail);

        $this->translatorProphecy
            ->trans('user.forget_password.subject', [], 'mail')
            ->shouldBeCalled()
            ->willReturn('translated text')
        ;

        $this->twigProphecy
            ->render(
                'User/Mail/forget_password.html.twig',
                [
                    'user' => $userProphecy->reveal(),
                ]
            )
            ->shouldBeCalled()
            ->willReturn('dummy twig template')
        ;

        $this->mailerProphecy->send(Argument::type(\Swift_Message::class))->shouldBeCalled()->willReturn(1);

        $this->mailer->sendForgetPassword($userProphecy->reveal());
    }
}
