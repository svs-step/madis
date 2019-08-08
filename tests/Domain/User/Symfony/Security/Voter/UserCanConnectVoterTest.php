<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2019 Soluris - Solutions Numériques Territoriales Innovantes
 * @author ANODE <contact@agence-anode.fr>
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

namespace App\Tests\Domain\User\Symfony\Security\Voter;

use App\Domain\User\Model;
use App\Domain\User\Symfony\Security\Authorization\UserAuthorization;
use App\Domain\User\Symfony\Security\Voter\UserCanConnectVoter;
use App\Tests\Utils\ReflectionTrait;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class UserCanConnectVoterTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @var UserAuthorization
     */
    private $userAuthorizationProphecy;

    /**
     * @var UserCanConnectVoter
     */
    private $sut;

    protected function setUp(): void
    {
        $this->userAuthorizationProphecy = $this->prophesize(UserAuthorization::class);

        $this->sut = new UserCanConnectVoter(
            $this->userAuthorizationProphecy->reveal()
        );

        parent::setUp();
    }

    /**
     * Test vote.
     *
     * @throws \Exception
     */
    public function testVote(): void
    {
        $token      = $this->prophesize(TokenInterface::class)->reveal();
        $subject    = new Model\User();
        $attributes = [
            UserCanConnectVoter::CAN_CONNECT,
        ];

        $this->userAuthorizationProphecy->canConnect($subject)->shouldBeCalled()->willReturn(true);

        $this->assertEquals(
            VoterInterface::ACCESS_GRANTED,
            $this->sut->vote($token, $subject, $attributes)
        );
    }

    public function dataProviderNotSupporting(): array
    {
        $dataProvider = [];
        $defaultToken = $this->prophesize(TokenInterface::class)->reveal();

        // Attribute is not the expected one
        $dataProvider[] = [
            $defaultToken,
            new Model\User(),
            [
                'ThisIsNotAnExpectedAttribute',
            ],
        ];

        // Subject class is not the expected one
        $dataProvider[] = [
            $defaultToken,
            new \StdClass(),
            [
                UserCanConnectVoter::CAN_CONNECT,
            ],
        ];

        return $dataProvider;
    }

    /**
     * Test vote
     * The voter don't support provided data.
     *
     * @dataProvider dataProviderNotSupporting
     *
     * @param TokenInterface $token
     * @param $subject
     * @param array $attributes
     */
    public function testVoteNotSupporting(TokenInterface $token, $subject, array $attributes): void
    {
        $this->userAuthorizationProphecy->canConnect(Argument::cetera())->shouldNotBeCalled();

        $this->assertEquals(
            VoterInterface::ACCESS_ABSTAIN,
            $this->sut->vote($token, $subject, $attributes)
        );
    }

    public function testVoteNotGranted(): void
    {
        $token      = $this->prophesize(TokenInterface::class)->reveal();
        $subject    = new Model\User();
        $attributes = [
            UserCanConnectVoter::CAN_CONNECT,
        ];

        $this->userAuthorizationProphecy->canConnect($subject)->shouldBeCalled()->willReturn(false);

        $this->assertEquals(
            VoterInterface::ACCESS_DENIED,
            $this->sut->vote($token, $subject, $attributes)
        );
    }
}
