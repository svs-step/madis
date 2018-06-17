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

namespace App\Tests\Domain\User\Symfony\Security;

use App\Domain\User\Model\User;
use App\Domain\User\Symfony\Security\UserChecker;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;

class UserCheckerTest extends TestCase
{
    /**
     * @var UserChecker
     */
    private $checker;

    protected function setUp()
    {
        $this->checker = new UserChecker();
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf(UserCheckerInterface::class, $this->checker);
    }

    /**
     * Test checkPreAuth.
     */
    public function testCheckPreAuth()
    {
        $userProphecy = $this->prophesize(User::class);
        $userProphecy->isEnabledOrCollectivityActive()->shouldBeCalled()->willReturn(true);

        $this->checker->checkPreAuth($userProphecy->reveal());
    }

    /**
     * Test checkPreAuth
     * User isn't enabled or it collectivity not active.
     */
    public function testCheckPreAuthNotEnabledOrActive()
    {
        $this->expectException(DisabledException::class);

        $userProphecy = $this->prophesize(User::class);
        $userProphecy->isEnabledOrCollectivityActive()->shouldBeCalled()->willReturn(false);

        $this->checker->checkPreAuth($userProphecy->reveal());
    }
}
