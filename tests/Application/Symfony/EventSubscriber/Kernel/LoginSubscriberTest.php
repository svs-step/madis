<?php

declare(strict_types=1);

namespace App\Tests\Application\Symfony\EventSubscriber\Kernel;

use App\Application\Symfony\EventSubscriber\Kernel\LoginSubscriber;
use App\Domain\Reporting\Model\LogJournal;
use App\Domain\User\Model\Collectivity;
use App\Domain\User\Model\User;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class LoginSubscriberTest extends TestCase
{
    /**
     * @var EntityManagerInterface|ObjectProphecy
     */
    private $entityManager;

    /**
     * @var \App\Domain\Reporting\Repository\LogJournal|ObjectProphecy
     */
    private $logRepository;

    /**
     * @var Security|ObjectProphecy
     */
    private $security;

    /**
     * @var LoginSubscriber
     */
    private $sut;

    protected function setUp(): void
    {
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->logRepository = $this->prophesize(\App\Domain\Reporting\Repository\LogJournal::class);
        $this->security      = $this->prophesize(Security::class);

        $this->security->isGranted('ROLE_ADMIN')->willReturn(true);

        $this->sut           = new LoginSubscriber(
            $this->entityManager->reveal(),
            $this->logRepository->reveal(),
            $this->security->reveal(),
            '6months'
        );

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
                SecurityEvents::INTERACTIVE_LOGIN => 'onSecurityInteractiveLogin',
            ],
            $this->sut->getSubscribedEvents()
        );
    }

    /**
     * Test onKernelRequest.
     */
    public function testOnSecurityInteractiveLogin(): void
    {
        $collectivity   = $this->prophesize(Collectivity::class);
        $user           = $this->prophesize(User::class);
        $eventProphecy  = $this->prophesize(InteractiveLoginEvent::class);
        $tokenInterface = $this->prophesize(TokenInterface::class);
        $eventProphecy->getAuthenticationToken()->shouldBeCalled()->willReturn($tokenInterface);
        $tokenInterface->getUser()->shouldBeCalled()->willReturn($user);
        $user->setLastLogin(Argument::type(\DateTimeImmutable::class))->shouldBeCalled();
        $user->getCollectivity()->shouldBeCalled()->willReturn($collectivity->reveal());

        $this->entityManager->persist($user)->shouldBeCalled();
        $this->entityManager->persist(Argument::type(LogJournal::class))->shouldBeCalled();
        $this->entityManager->flush()->shouldBeCalled();

        $this->sut->onSecurityInteractiveLogin($eventProphecy->reveal());
    }
}
