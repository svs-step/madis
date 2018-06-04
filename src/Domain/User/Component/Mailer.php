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

namespace App\Domain\User\Component;

use App\Domain\User\Model;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;

class Mailer
{
    /**
     * @var \Swift_Mailer
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
        \Swift_Mailer $mailer,
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
     * @param string $to      The receiver of the email
     * @param string $subject The subject of the email
     * @param string $body    The content of the email
     *
     * @return int
     */
    private function send(string $to, string $subject, string $body): int
    {
        $message = (new \Swift_Message())
            ->setFrom(
                [
                    $this->senderEmail => $this->senderName,
                ]
            )
            ->setTo($to)
            ->setSubject($subject)
            ->setBody($body)
            ->setContentType('text/html')
        ;

        return $this->mailer->send($message);
    }

    /**
     * Send forget password email in order to reset it.
     *
     * @param Model\User $user The user to reset password
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
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
