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

namespace App\Domain\User\Component;

use App\Domain\User\Model;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class Mailer
{
    /**
     * @var \Symfony\Component\Mailer\Mailer
     */
    private $mailer;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var string
     */
    private $senderEmail;

    /**
     * @var string
     */
    private $senderName;

    public function __construct(
        \Symfony\Component\Mailer\MailerInterface $mailer,
        TranslatorInterface $translator,
        Environment $twig,
        string $senderEmail,
        string $senderName
    ) {
        $this->mailer      = $mailer;
        $this->translator  = $translator;
        $this->twig        = $twig;
        $this->senderEmail = $senderEmail;
        $this->senderName  = $senderName;
    }

    /**
     * Send an email.
     *
     * @param string $to The receiver of the email
     * @param string $subject The subject of the email
     * @param string $body The content of the email
     * @throws TransportExceptionInterface
     */
    private function send(string $to, string $subject, string $body): void
    {
        $message = (new Email())
            ->from(new Address($this->senderEmail, $this->senderName))
            ->to($to)
            ->subject($subject)
            ->html($body)
        ;

        $this->mailer->send($message);
    }

    /**
     * Send forget password email in order to reset it.
     *
     * @param Model\User $user The user to reset password
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function sendForgetPassword(Model\User $user): void
    {
        $this->send(
            $user->getEmail(),
            $this->translator->trans('user.forget_password.subject', [], 'mail'),
            $this->twig->render(
                'User/Mail/forget_password.html.twig',
                [
                    'user' => $user,
                ]
            )
        );
    }
}
