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

namespace App\Tests\Application\Symfony\EventSubscriber\Doctrine;

use App\Application\Symfony\EventSubscriber\Doctrine\HistorySubscriber;
use App\Application\Traits\Model\HistoryTrait;
use App\Tests\Utils\ReflectionTrait;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class HistorySubscriberTest extends TestCase
{
    use ReflectionTrait;
    use ProphecyTrait;

    /**
     * @var LifecycleEventArgs
     */
    private $lifeCycleEventArgsProphecy;

    /**
     * @var HistorySubscriber
     */
    private $subscriber;

    public function setUp(): void
    {
        $this->lifeCycleEventArgsProphecy = $this->prophesize(LifecycleEventArgs::class);

        $this->subscriber = new HistorySubscriber();
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
     *
     * @throws \Exception
     */
    public function testPrePersist()
    {
        $object = new class() {
            use HistoryTrait;
        };

        $this->lifeCycleEventArgsProphecy->getObject()->shouldBeCalled()->willReturn($object);

        $this->assertNull($object->getCreatedAt());
        $this->assertNull($object->getUpdatedAt());
        $this->subscriber->prePersist($this->lifeCycleEventArgsProphecy->reveal());
        $this->assertInstanceOf(\DateTimeImmutable::class, $object->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $object->getUpdatedAt());
    }

    /**
     * Test preUpdate.
     *
     * @throws \Exception
     */
    public function testPreUpdate()
    {
        $dateTime = (new \DateTimeImmutable())->modify('-5 days');
        $object   = new class() {
            use HistoryTrait;
        };
        $object->setCreatedAt($dateTime);
        $object->setUpdatedAt($dateTime);

        $this->lifeCycleEventArgsProphecy->getObject()->shouldBeCalled()->willReturn($object);

        $this->assertEquals($object->getCreatedAt()->format('Y-m-d H:i:s'), $object->getUpdatedAt()->format('Y-m-d H:i:s'));
        $this->subscriber->preUpdate($this->lifeCycleEventArgsProphecy->reveal());

        $this->assertNotEquals($object->getCreatedAt()->format('Y-m-d H:i:s'), $object->getUpdatedAt()->format('Y-m-d H:i:s'));
    }
}
