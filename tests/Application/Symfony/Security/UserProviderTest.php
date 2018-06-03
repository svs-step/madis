<?php

declare(strict_types=1);

namespace App\Tests\Application\Symfony\Security;

use App\Application\Symfony\Security\UserProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserProviderTest extends TestCase
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    private $token;

    /**
     * @var UserProvider
     */
    private $userProvider;

    public function setUp()
    {
        $this->token        = $this->prophesize(TokenInterface::class);
        $this->tokenStorage = $this->prophesize(TokenStorageInterface::class);

        $this->userProvider = new UserProvider(
            $this->tokenStorage->reveal()
        );
    }

    /**
     * Test to get an authenticated user.
     */
    public function testGetAuthenticatedUser()
    {
        $user = $this->prophesize(UserInterface::class)->reveal();
        $this->token->getUser()->shouldBeCalled()->willReturn($user);
        $this->tokenStorage->getToken()->shouldBeCalled()->willReturn($this->token->reveal());

        $this->assertInstanceOf(UserInterface::class, $this->userProvider->getAuthenticatedUser());
    }

    /**
     * Test to get an authenticated user but the route isn't under security.
     */
    public function testGetAuthenticatedUserButRouteIsntUnderSecurity()
    {
        $this->token->getUser()->shouldNotBeCalled();
        $this->tokenStorage->getToken()->shouldBeCalled()->willReturn(null);

        $this->assertNull($this->userProvider->getAuthenticatedUser());
    }

    /**
     * Test to get an authenticated user but user isn't an object (case of anonymous user).
     */
    public function testGetAuthenticatedUserWithoutObjectUser()
    {
        $user = 'anon.';
        $this->token->getUser()->shouldBeCalled()->willReturn($user);
        $this->tokenStorage->getToken()->shouldBeCalled()->willReturn($this->token->reveal());

        $this->assertNull($this->userProvider->getAuthenticatedUser());
    }
}
