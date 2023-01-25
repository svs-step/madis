<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2019 Soluris - Solutions Numériques Territoriales Innovantes
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

namespace App\Tests\Domain\Reporting\Handler;

use App\Domain\Reporting\Handler\MetricsHandler;
use App\Domain\Reporting\Metrics\AdminMetric;
use App\Domain\Reporting\Metrics\UserMetric;
use App\Domain\User\Dictionary\UserRoleDictionary;
use App\Domain\User\Model\User;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Security\Core\Security;

class MetricsHandlerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var Security
     */
    private $security;

    /**
     * @var UserMetric
     */
    private $userMetric;

    /**
     * @var AdminMetric
     */
    private $adminMetric;

    /**
     * @var MetricsHandler
     */
    private $handler;

    protected function setUp(): void
    {
        $this->security    = $this->prophesize(Security::class);
        $this->userMetric  = $this->prophesize(UserMetric::class);
        $this->adminMetric = $this->prophesize(AdminMetric::class);

        $this->handler = new MetricsHandler(
            $this->security->reveal(),
            $this->userMetric->reveal(),
            $this->adminMetric->reveal()
        );
    }

    public function testItReturnAdminMetricWhenUserRoleIsAdmin()
    {
        $user = $this->prophesize(User::class);
        $user->getRoles()->shouldBeCalled()->willReturn([UserRoleDictionary::ROLE_ADMIN]);
        $this->security->getUser()->shouldBeCalled()->willReturn($user);

        $this->assertInstanceOf(AdminMetric::class, $this->handler->getHandler());
    }

    public function testItReturnAdminMetricWhenUserRoleIsNotAdmin()
    {
        $user = $this->prophesize(User::class);
        $user->getRoles()->shouldBeCalled()->willReturn([UserRoleDictionary::ROLE_USER]);
        $this->security->getUser()->shouldBeCalled()->willReturn($user);

        $this->assertInstanceOf(UserMetric::class, $this->handler->getHandler());
    }
}
