<?php

declare(strict_types=1);

namespace App\Tests\Application\Symfony\EventSubscriber\Kernel;

use App\Application\Symfony\EventSubscriber\Kernel\AccessModuleConformiteSubscriber;
use App\Domain\Registry\Controller\ConformiteTraitementController;
use App\Domain\User\Model\Collectivity;
use App\Domain\User\Model\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;

class AccessModuleConformiteSubscriberTest extends TestCase
{
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

    public function testItReturnNullOnNoControllerAndEmptyUser(): void
    {
        $event = $this->prophesize(ControllerEvent::class);

        $event->getController()->shouldBeCalled()->willReturn([]);

        $this->assertNull($this->sut->onKernelController($event->reveal()));

        $controller = $this->prophesize(ConformiteTraitementController::class);

        $event->getController()->shouldBeCalled()->willReturn(['foo']);
        $this->security->getUser()->shouldBeCalled()->willReturn(null);

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

        $event->getController()->shouldBeCalled()->willReturn([$controller->reveal()]);
        $this->security->getUser()->shouldBeCalled()->willReturn($user);
        $user->getCollectivity()->shouldBeCalled()->willReturn($collectivity);
        $collectivity->isHasModuleConformiteTraitement()->shouldBeCalled()->willReturn(true);

        $this->assertNull($this->sut->onKernelController($event->reveal()));
    }
}
