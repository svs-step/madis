<?php

namespace App\Domain\User\Symfony\EventSubscriber\Security;

use App\Domain\User\Event\ExceededLoginAttempts;
use App\Domain\User\Exception\ExceededLoginAttemptsException;
use App\Domain\User\Model\LoginAttempt;
use App\Domain\User\Repository\LoginAttempt as LoginAttemptRepository;
use App\Domain\User\Repository\User as UserRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AuthenticationSubscriber implements EventSubscriberInterface
{
    protected RequestStack $requestStack;
    protected LoginAttemptRepository $loginAttemptRepository;
    protected UserRepository $userRepository;
    protected int $maxAttempts;
    protected EventDispatcherInterface $dispatcher;

    public function __construct(
        RequestStack $requestStack,
        LoginAttemptRepository $loginAttemptRepository,
        UserRepository $userRepository,
        int $maxAttempts,
        EventDispatcherInterface $dispatcher
    ) {
        $this->requestStack           = $requestStack;
        $this->loginAttemptRepository = $loginAttemptRepository;
        $this->userRepository         = $userRepository;
        $this->maxAttempts            = $maxAttempts;
        $this->dispatcher             = $dispatcher;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AuthenticationEvents::AUTHENTICATION_FAILURE => 'onAuthFailure',
            AuthenticationEvents::AUTHENTICATION_SUCCESS => 'onAuthSuccess',
        ];
    }

    public function onAuthFailure(AuthenticationFailureEvent $event)
    {
        $ip      = $this->requestStack->getCurrentRequest()->getClientIp();
        $email   = $event->getAuthenticationToken()->getUsername();
        $attempt = $this->loginAttemptRepository->findOneOrNullByIpAndEmail($ip, $email);

        // If this is the first login attempt, create new entity
        if (null === $attempt) {
            $attempt = new LoginAttempt();
            $attempt->setAttempts(0);
            $attempt->setIp($ip);
            $attempt->setEmail($email);
            $this->loginAttemptRepository->insert($attempt);
        }

        $n = $attempt->getAttempts();

        $attempt->setAttempts($attempt->getAttempts() + 1);

        if ($attempt->getAttempts() > $this->maxAttempts) {
            $user = $this->userRepository->findOneOrNullByEmail($email);
            // Disable this user if it exists and the maximum number of login attempts was exceeded
            if ($user) {
                $user->setEnabled(false);
                $attempt->setAttempts(0);
                $this->loginAttemptRepository->update($attempt);
                $this->userRepository->update($user);
                throw new ExceededLoginAttemptsException();
            }
        }

        $this->loginAttemptRepository->update($attempt);
        // Exponential wait time for wrong passwords
        sleep($n);
    }

    public function onAuthSuccess(AuthenticationSuccessEvent $event)
    {
        if (!$event->getAuthenticationToken()) {
            return;
        }

        $user = $event->getAuthenticationToken()->getUser();
        if (is_string($user)) {
            return;
        }

        $ip      = $this->requestStack->getCurrentRequest()->getClientIp();
        $email   = $event->getAuthenticationToken()->getUsername();
        $attempt = $this->loginAttemptRepository->findOneOrNullByIpAndEmail($ip, $email);
        // If the attempt exists, we reset the number of attempts to zero on successful login.
        if ($attempt) {
            $attempt->setAttempts(0);
            $this->loginAttemptRepository->update($attempt);
        }
    }
}
