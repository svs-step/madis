<?php

namespace App\Tests\Domain\Notification\Controller;

use App\Application\Controller\CRUDController;
use App\Application\Symfony\Security\UserProvider;
use App\Domain\Notification\Controller\NotificationController;
use App\Domain\Notification\Model\Notification;
use App\Domain\Notification\Subscriber\NotificationEventSubscriber;
use App\Infrastructure\ORM\AIPD\Repository\AnalyseImpact;
use App\Infrastructure\ORM\Notification\Repository\Notification as NotificationRepository;
use App\Tests\Utils\ReflectionTrait;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Snappy\Pdf;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class NotificationControllerTest extends TestCase
{
    use ReflectionTrait;
    use ProphecyTrait;

    private NotificationController $controller;
    private ObjectProphecy $router;
    private ObjectProphecy $entityManager;
    private ObjectProphecy $translator;
    private ObjectProphecy $repository;
    private ObjectProphecy $authorizationChecker;
    private ObjectProphecy $userProvider;
    private ObjectProphecy $pdf;
    private ObjectProphecy $AIPDRepository;

    private NotificationEventSubscriber $subscriber;

    public function setUp(): void
    {
        $this->repository           = $this->prophesize(NotificationRepository::class);
        $this->router               = $this->prophesize(RouterInterface::class);
        $this->entityManager        = $this->prophesize(EntityManagerInterface::class);
        $this->translator           = $this->prophesize(TranslatorInterface::class);
        $this->authorizationChecker = $this->prophesize(AuthorizationCheckerInterface::class);
        $this->userProvider         = $this->prophesize(UserProvider::class);
        $this->pdf                  = $this->prophesize(Pdf::class);
        $this->AIPDRepository       = $this->prophesize(AnalyseImpact::class);

        $this->controller = new NotificationController(
            $this->router->reveal(),
            $this->entityManager->reveal(),
            $this->translator->reveal(),
            $this->repository->reveal(),
            $this->AIPDRepository->reveal(),
            $this->authorizationChecker->reveal(),
            $this->userProvider->reveal(),
            $this->pdf->reveal()
        );
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf(CRUDController::class, $this->controller);
    }

    public function testIsServersideDatatables()
    {
        $this->assertEquals(method_exists($this->controller, 'listDataTables'), true);
        $this->assertEquals(method_exists($this->controller, 'getCorrespondingLabelFromKey'), true);
        $this->assertEquals(method_exists($this->controller, 'getBaseDataTablesResponse'), true);
        $this->assertEquals(method_exists($this->controller, 'getResults'), true);
        $this->assertEquals(method_exists($this->controller, 'getLabelAndKeysArray'), true);
    }

    public function testGetDomain()
    {
        $this->assertEquals(
            'notification',
            $this->invokeMethod($this->controller, 'getDomain', [])
        );
    }

    public function testGetModel()
    {
        $this->assertEquals(
            'notification',
            $this->invokeMethod($this->controller, 'getModel', [])
        );
    }

    public function testGetModelClass()
    {
        $this->assertEquals(
            Notification::class,
            $this->invokeMethod($this->controller, 'getModelClass', [])
        );
    }

    public function testGetFormType()
    {
        $this->assertEquals(
            '',
            $this->invokeMethod($this->controller, 'getFormType', [])
        );
    }

    /*
    public function testGetLabelsAndKeys()
    {
        $this->assertEquals(
            [
                'state',
                'module',
                'action',
                'name',
                'object',
                'collectivity',
                'date',
                'user',
                'read_date',
                'read_by',
            ],
            $this->invokeMethod($this->controller, 'getLabelAndKeysArray', [])
        );
    }
    */

    public function testGetObjectLink()
    {
        $notification = new Notification();

        $this->router->generate('registry_treatment_show', ['id' => '1'], UrlGeneratorInterface::ABSOLUTE_URL)->shouldBeCalled()->willReturn('link');

        $notification->setModule('notification.modules.treatment');
        $ob = ['id' => '1'];
        $notification->setObject((object) $ob);

        $link = $this->invokeMethod($this->controller, 'getObjectLink', [$notification]);
        $this->assertEquals('link', $link);
    }
}
