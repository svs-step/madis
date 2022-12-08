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

namespace App\Tests\Domain\User\Component;

use App\Domain\User\Component\Mailer;
use App\Domain\User\Model;
use App\Tests\Utils\ReflectionTrait;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class MailerTest extends TestCase
{
    use ReflectionTrait;
    use ProphecyTrait;

    /**
     * @var MailerInterface
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

    public function setUp(): void
    {
        $this->mailerProphecy     = $this->prophesize(MailerInterface::class);
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
        $this->mailerProphecy->send(Argument::type(Email::class))->shouldBeCalled();

        $this->invokeMethod(
            $this->mailer,
            'send',
            ['to@gmail.com', 'subject', 'body']
        );
    }

    /**
     * Test sendForgetPassword.
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
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

        $this->mailerProphecy->send(Argument::type(Email::class))->shouldBeCalled();

        $this->mailer->sendForgetPassword($userProphecy->reveal());
    }
}
