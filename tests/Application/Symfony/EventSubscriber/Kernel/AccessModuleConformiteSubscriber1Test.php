<?php

declare(strict_types=1);

namespace App\Tests\Application\Symfony\EventSubscriber\Kernel;

use App\Application\Symfony\EventSubscriber\Kernel\AccessModuleConformiteSubscriber;
use App\Domain\Registry\Controller\ConformiteTraitementController;
use App\Domain\User\Model\Collectivity;
use App\Domain\User\Model\User;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\EventDispatcher\StoppableEventInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Security;

class A {
    public function __invoke()
    {
        return '';
    }
}

class AccessModuleConformiteSubscriber1Test extends TestCase
{
    use ProphecyTrait;

    /**
     * @var Security
     */
    private $security;

    /**
     * @var AccessModuleConformiteSubscriber
     */
    private $sut;

    protected function setUp(): void
    {
        $this->security = $this->prophesize(Security::class);
        $this->sut      = new AccessModuleConformiteSubscriber($this->security->reveal());

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
                KernelEvents::CONTROLLER => [
                    ['onKernelController'],
                ],
            ],
            $this->sut->getSubscribedEvents()
        );
    }

    public function testReturnNullOnNulController(): void
    {
        $kernel = $this->prophesize(KernelInterface::class)->reveal();
        $callable = function() {};
        $controller = $this->prophesize(A::class);
        $request = $this->prophesize(Request::class)->reveal();

        $event = new ControllerEvent($kernel, $controller, $request, HttpKernelInterface::MAIN_REQUEST);

        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber($this->sut);
        $dispatcher->dispatch($event);

        $event->getController()->shouldBeCalled()->willReturn(ConformiteTraitementController::class);

        $this->security->getUser()->shouldBeCalled()->willReturn(new User());
    }

    public function testItReturnNullOnNotArrayNoControllerAndEmptyUser(): void
    {
        $prophecy = $this->prophesize();
        $prophecy->willExtend(KernelEvent::class);
        $prophecy->willImplement(StoppableEventInterface::class);

        $prophecy->read('123')->willReturn('value');

        $controller = $this->prophesize(AbstractController::class);
        $controller->shouldBeCalled()->willReturn('foo');

        $this->assertNull($this->sut->onKernelController($event->reveal()));

        $event = $this->prophesize(ControllerEvent::class);

        $controller->shouldBeCalled()->willReturn([]);

        $this->assertNull($this->sut->onKernelController($event->reveal()));

        $controller->shouldBeCalled()->willReturn(['foo']);
        $this->security->getUser()->shouldBeCalled()->willReturn(null);

        $this->assertNull($this->sut->onKernelController($event->reveal()));

        $controller->shouldBeCalled()->willReturn(['foo']);
        $this->security->getUser()->shouldBeCalled()->willReturn(new User());
        $this->security->isGranted('ROLE_ADMIN')->shouldBeCalled()->willReturn(true);

        $this->assertNull($this->sut->onKernelController($event->reveal()));
    }

    public function testItNotAllowAccessToConformiteTraitement(): void
    {
        $this->expectException(AccessDeniedHttpException::class);
        $this->expectExceptionMessage('You can\'t access to conformite des traitements');

        $event        = $this->prophesize(ControllerEvent::class);
        $controller   = $this->prophesize(ConformiteTraitementController::class);
        $user         = $this->prophesize(User::class);
        $collectivity = $this->prophesize(Collectivity::class);

        $this->security->isGranted('ROLE_ADMIN')->shouldBeCalled()->willReturn(false);
        $event->getController()->shouldBeCalled()->willReturn([$controller->reveal()]);
        $this->security->getUser()->shouldBeCalled()->willReturn($user);
        $user->getCollectivity()->shouldBeCalled()->willReturn($collectivity);
        $collectivity->isHasModuleConformiteTraitement()->shouldBeCalled()->willReturn(false);

        $this->sut->onKernelController($event->reveal());
    }

    public function testItAllowAccessToConformiteTraitement(): void
    {
        $event        = $this->prophesize(ControllerEvent::class);
        $controller   = $this->prophesize(ConformiteTraitementController::class);
        $user         = $this->prophesize(User::class);
        $collectivity = $this->prophesize(Collectivity::class);
        $this->security->isGranted('ROLE_ADMIN')->shouldBeCalled()->willReturn(false);

        $event->getController()->shouldBeCalled()->willReturn([$controller->reveal()]);
        $this->security->getUser()->shouldBeCalled()->willReturn($user);
        $user->getCollectivity()->shouldBeCalled()->willReturn($collectivity);
        $collectivity->isHasModuleConformiteTraitement()->shouldBeCalled()->willReturn(true);

        $this->assertNull($this->sut->onKernelController($event->reveal()));
    }
}
