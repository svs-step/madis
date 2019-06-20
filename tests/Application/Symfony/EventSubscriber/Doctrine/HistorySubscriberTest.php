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

namespace App\Tests\Domain\Event\Symfony\EventSubscriber\Doctrine;

use App\Application\Symfony\EventSubscriber\Doctrine\HistorySubscriber;
use App\Application\Traits\Model\HistoryTrait;
use App\Tests\Utils\ReflectionTrait;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use PHPUnit\Framework\TestCase;

class HistorySubscriberTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @var LifecycleEventArgs
     */
    private $lifeCycleEventArgsProphecy;

    /**
     * @var HistorySubscriber
     */
    private $subscriber;

    public function setUp()
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
        $dateTime = new \DateTimeImmutable('-5 days');
        $object   = new class() {
            use HistoryTrait;
        };
        $object->setCreatedAt($dateTime);
        $object->setUpdatedAt($dateTime);

        $this->lifeCycleEventArgsProphecy->getObject()->shouldBeCalled()->willReturn($object);

        $this->assertEquals($object->getCreatedAt(), $object->getUpdatedAt());
        $this->subscriber->prePersist($this->lifeCycleEventArgsProphecy->reveal());
        $this->assertNotEquals($object->getCreatedAt(), $object->getUpdatedAt());
    }
}
