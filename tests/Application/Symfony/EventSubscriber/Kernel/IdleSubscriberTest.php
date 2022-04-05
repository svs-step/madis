<?php

declare(strict_types=1);

namespace App\Tests\Application\Symfony\EventSubscriber\Kernel;

use App\Application\Symfony\EventSubscriber\Kernel\IdleSubscriber;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MetadataBag;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class IdleSubscriberTest extends TestCase
{
    use ProphecyTrait;
    /**
     * @var int
     */
    private $expirationTime;

    /**
     * @var IdleSubscriber
     */
    private $sut;

    protected function setUp(): void
    {
        $this->expirationTime = 666;
        $this->sut            = new IdleSubscriber($this->expirationTime);

        parent::setUp();
    }

    /**
     * Test instance of Subscriber.
     */
    public function testInstanceOf(): void
    {
        $this->assertInstanceOf(EventSubscriberInterface::class, $this->sut);
    }

    /**
     * Test getSubscribedEvents of current subscriber.
     */
    public function testGetSubscribedEvents(): void
    {
        $this->assertEquals(
            [
                KernelEvents::REQUEST => [
                    ['onKernelRequest', 9],
                ],
            ],
            $this->sut->getSubscribedEvents()
        );
    }

    /**
     * Test onKernelRequest.
     */
    public function testOnKernelRequest(): void
    {
        $isMasterRequest = true;
        $sessionLastUsed = 946684800; // 2000-01-01 00:00:00

        $sessionMetadataBagProphecy = $this->prophesize(MetadataBag::class);
        $sessionMetadataBagProphecy->getLastUsed()->shouldBeCalled()->willReturn($sessionLastUsed);

        $sessionProphecy = $this->prophesize(Session::class);
        $sessionProphecy->start()->shouldBeCalled();
        $sessionProphecy->getMetadataBag()->shouldBeCalled()->willReturn($sessionMetadataBagProphecy->reveal());

        $sessionProphecy->invalidate()->shouldBeCalled();

        $request = new Request();
        $request->setSession($sessionProphecy->reveal());

        $eventProphecy = $this->prophesize(RequestEvent::class);
        $eventProphecy->isMasterRequest()->shouldBeCalled()->willReturn($isMasterRequest);
        $eventProphecy->getRequest()->shouldBeCalled()->willReturn($request);

        $this->sut->onKernelRequest($eventProphecy->reveal());
    }

    /**
     * Test onKernelRequest
     * Not the master request.
     */
    public function testOnKernelRequestNotMasterRequest(): void
    {
        $isMasterRequest = false;

        $eventProphecy = $this->prophesize(RequestEvent::class);
        $eventProphecy->isMasterRequest()->shouldBeCalled()->willReturn($isMasterRequest);
        $eventProphecy->getRequest()->shouldNotBeCalled();

        $this->sut->onKernelRequest($eventProphecy->reveal());
    }

    /**
     * Test onKernelRequest
     * Not at expiration time.
     */
    public function testOnKernelRequestNotAtExpirationTime(): void
    {
        $isMasterRequest = true;
        $sessionLastUsed = time(); // check $this->expirationTime, we got 666s to succeed test

        $sessionMetadataBagProphecy = $this->prophesize(MetadataBag::class);
        $sessionMetadataBagProphecy->getLastUsed()->shouldBeCalled()->willReturn($sessionLastUsed);

        $sessionProphecy = $this->prophesize(Session::class);
        $sessionProphecy->start()->shouldBeCalled();
        $sessionProphecy->getMetadataBag()->shouldBeCalled()->willReturn($sessionMetadataBagProphecy->reveal());

        $sessionProphecy->invalidate()->shouldNotBeCalled();

        $request = new Request();
        $request->setSession($sessionProphecy->reveal());

        $eventProphecy = $this->prophesize(RequestEvent::class);
        $eventProphecy->isMasterRequest()->shouldBeCalled()->willReturn($isMasterRequest);
        $eventProphecy->getRequest()->shouldBeCalled()->willReturn($request);

        $this->sut->onKernelRequest($eventProphecy->reveal());
    }
}
