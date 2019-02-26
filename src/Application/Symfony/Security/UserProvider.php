<?php

declare(strict_types=1);

namespace App\Application\Symfony\Security;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserProvider
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Get the authenticated user
     * - User isn't under protected route: return null
     * - User is anonymous: return null
     * - User is connected: return UserInterface instance.
     *
     * @return UserInterface|null
     */
    public function getAuthenticatedUser(): ?UserInterface
    {
        if (null === $token = $this->tokenStorage->getToken()) {
            return null;
        }

        if (!\is_object($user = $token->getUser())) {
            // e.g. anonymous authentication
            return null;
        }

        return $user;
    }

    public function isGranted(string $role): bool
    {
    }
}
