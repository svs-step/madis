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

namespace App\Tests\Domain\Maturity\Symfony\EventSubscriber\Doctrine;

use App\Domain\Maturity\Calculator;
use App\Domain\Maturity\Model;
use App\Domain\Maturity\Symfony\EventSubscriber\Doctrine\GenerateMaturitySubscriber;
use App\Tests\Utils\ReflectionTrait;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class GenerateMaturitySubscriberTest extends TestCase
{
    use ReflectionTrait;
    use ProphecyTrait;

    /**
     * @var LifecycleEventArgs
     */
    private $lifeCycleEventArgsProphecy;

    /**
     * @var Calculator\MaturityHandler;
     */
    private $maturityHandlerProphecy;

    /**
     * @var GenerateMaturitySubscriber
     */
    private $subscriber;

    public function setUp(): void
    {
        $this->lifeCycleEventArgsProphecy = $this->prophesize(LifecycleEventArgs::class);
        $this->maturityHandlerProphecy    = $this->prophesize(Calculator\MaturityHandler::class);

        $this->subscriber = new GenerateMaturitySubscriber(
            $this->maturityHandlerProphecy->reveal()
        );
    }

    /**
     * Test instance of Subscriber.
     */
    public function testInstanceOf()
    {
        $this->assertInstanceOf(EventSubscriber::class, $this->subscriber);
    }

    /**
     * Test getSubscribedEvents of current subscriber.
     */
    public function testGetSubscribedEvents()
    {
        $this->assertEquals(
            [
                'prePersist',
                'preUpdate',
            ],
            $this->subscriber->getSubscribedEvents()
        );
    }

    /**
     * Test prePersist.
     */
    public function testPrePersist()
    {
        $object = new Model\Survey();

        $this->lifeCycleEventArgsProphecy->getObject()->shouldBeCalled()->willReturn($object);

        $this->maturityHandlerProphecy->handle($object)->shouldBeCalled();

        $this->subscriber->prePersist($this->lifeCycleEventArgsProphecy->reveal());
    }

    /**
     * Test preUpdate.
     */
    public function testPreUpdate()
    {
        $object = new Model\Survey();

        $this->lifeCycleEventArgsProphecy->getObject()->shouldBeCalled()->willReturn($object);

        $this->maturityHandlerProphecy->handle($object)->shouldBeCalled();

        $this->subscriber->prePersist($this->lifeCycleEventArgsProphecy->reveal());
    }
}
