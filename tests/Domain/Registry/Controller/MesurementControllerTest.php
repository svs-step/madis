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

namespace App\Tests\Domain\Registry\Controller;

use App\Application\Controller\CRUDController;
use App\Application\Symfony\Security\UserProvider;
use App\Domain\Registry\Controller\MesurementController;
use App\Domain\Registry\Dictionary\MesurementStatusDictionary;
use App\Domain\Registry\Form\Type\MesurementType;
use App\Domain\Registry\Model;
use App\Domain\Registry\Model\Mesurement;
use App\Domain\Registry\Repository;
use App\Domain\Reporting\Handler\WordHandler;
use App\Domain\User\Model as UserModel;
use App\Domain\User\Repository as UserRepository;
use App\Tests\Utils\ReflectionTrait;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Snappy\Pdf;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class MesurementControllerTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @var EntityManagerInterface
     */
    private $managerProphecy;

    /**
     * @var TranslatorInterface
     */
    private $translatorProphecy;

    /**
     * @var Repository\Mesurement
     */
    private $repositoryProphecy;

    /**
     * @var UserRepository\Collectivity
     */
    private $collectivityRepositoryProphecy;

    /**
     * @var WordHandler
     */
    private $wordHandlerProphecy;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authenticationCheckerProphecy;

    /**
     * @var UserProvider
     */
    private $userProviderProphecy;

    /**
     * @var FormFactoryInterface|ObjectProphecy
     */
    private $formFactory;

    /**
     * @var RouterInterface|ObjectProphecy
     */
    private $router;

    /**
     * @var Pdf|ObjectProphecy
     */
    private $pdf;

    /**
     * @var RequestStack|ObjectProphecy
     */
    private $requestStack;

    /**
     * @var MesurementController
     */
    private $controller;

    public function setUp()
    {
        $this->managerProphecy                = $this->prophesize(EntityManagerInterface::class);
        $this->translatorProphecy             = $this->prophesize(TranslatorInterface::class);
        $this->repositoryProphecy             = $this->prophesize(Repository\Mesurement::class);
        $this->collectivityRepositoryProphecy = $this->prophesize(UserRepository\Collectivity::class);
        $this->wordHandlerProphecy            = $this->prophesize(WordHandler::class);
        $this->authenticationCheckerProphecy  = $this->prophesize(AuthorizationCheckerInterface::class);
        $this->userProviderProphecy           = $this->prophesize(UserProvider::class);
        $this->formFactory                    = $this->prophesize(FormFactoryInterface::class);
        $this->router                         = $this->prophesize(RouterInterface::class);
        $this->pdf                            = $this->prophesize(Pdf::class);
        $this->requestStack                   = $this->prophesize(RequestStack::class);

        $this->controller = new MesurementController(
            $this->managerProphecy->reveal(),
            $this->translatorProphecy->reveal(),
            $this->repositoryProphecy->reveal(),
            $this->collectivityRepositoryProphecy->reveal(),
            $this->wordHandlerProphecy->reveal(),
            $this->authenticationCheckerProphecy->reveal(),
            $this->userProviderProphecy->reveal(),
            $this->formFactory->reveal(),
            $this->router->reveal(),
            $this->pdf->reveal(),
            $this->requestStack->reveal()
        );
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf(CRUDController::class, $this->controller);
    }

    public function testGetDomain()
    {
        $this->assertEquals(
            'registry',
            $this->invokeMethod($this->controller, 'getDomain', [])
        );
    }

    public function testGetModel()
    {
        $this->assertEquals(
            'mesurement',
            $this->invokeMethod($this->controller, 'getModel', [])
        );
    }

    public function testGetModelClass()
    {
        $this->assertEquals(
            Model\Mesurement::class,
            $this->invokeMethod($this->controller, 'getModelClass', [])
        );
    }

    public function testGetFormType()
    {
        $this->assertEquals(
            MesurementType::class,
            $this->invokeMethod($this->controller, 'getFormType', [])
        );
    }

    /**
     * Test getListData
     * User is granted ROLE_ADMIN.
     */
    public function testGetListDataForRoleGranted()
    {
        $valueReturnedByRepository = ['dummyValues'];

        $user         = $this->prophesize(UserModel\User::class);
        $user->getRoles()->shouldBeCalled()->willReturn([]);

        $this->userProviderProphecy
            ->getAuthenticatedUser()
            ->shouldBeCalled()
            ->willReturn($user)
        ;

        // Granted
        $this->authenticationCheckerProphecy
            ->isGranted('ROLE_ADMIN')
            ->shouldBeCalled()
            ->willReturn(true)
        ;

        $this->repositoryProphecy
            ->findBy([])
            ->shouldBeCalled()
            ->willReturn($valueReturnedByRepository)
        ;
        $this->repositoryProphecy
            ->findBy(['collectivity' => Argument::type(UserModel\Collectivity::class)])
            ->shouldNotBeCalled()
        ;

        $this->assertEquals(
            $valueReturnedByRepository,
            $this->invokeMethod($this->controller, 'getListData')
        );
    }

    /**
     * Test getListData
     * User is not granted ROLE_ADMIN.
     */
    public function testGetListDataForRoleNotGranted()
    {
        $valueReturnedByRepository = ['dummyValues'];

        // Not granted
        $this->authenticationCheckerProphecy
            ->isGranted('ROLE_ADMIN')
            ->shouldBeCalled()
            ->willReturn(false)
        ;

        $collectivity = $this->prophesize(UserModel\Collectivity::class)->reveal();
        $userProphecy = $this->prophesize(UserModel\User::class);
        $userProphecy->getCollectivity()->shouldBeCalled()->willReturn($collectivity);
        $userProphecy->getRoles()->shouldBeCalled()->willReturn([]);
        $this->userProviderProphecy
            ->getAuthenticatedUser()
            ->shouldBeCalled()
            ->willReturn($userProphecy->reveal())
        ;

        $this->repositoryProphecy
            ->findBy(['collectivity' => $collectivity])
            ->shouldBeCalled()
            ->willReturn($valueReturnedByRepository)
        ;
        $this->repositoryProphecy
            ->findBy([])
            ->shouldNotBeCalled()
        ;

        $this->assertEquals(
            $valueReturnedByRepository,
            $this->invokeMethod($this->controller, 'getListData')
        );
    }

    /**
     * Test reportAction.
     *
     * @throws \PhpOffice\PhpWord\Exception\Exception
     */
    public function testReportAction()
    {
        $orderKey    = 'name';
        $orderDir    = 'asc';
        $mesurements = [];
        $response    = $this->prophesize(BinaryFileResponse::class)->reveal();

        $collectivity = $this->prophesize(UserModel\Collectivity::class)->reveal();
        $userProphecy = $this->prophesize(UserModel\User::class);
        $userProphecy->getCollectivity()->shouldBeCalled()->willReturn($collectivity);
        $this->userProviderProphecy
            ->getAuthenticatedUser()
            ->shouldBeCalled()
            ->willReturn($userProphecy->reveal())
        ;

        $this->repositoryProphecy
            ->findAllByCollectivity($collectivity, [$orderKey => $orderDir])
            ->shouldBeCalled()
            ->willReturn($mesurements)
        ;

        $this->wordHandlerProphecy
            ->generateRegistryMesurementReport($mesurements)
            ->shouldBeCalled()
            ->willReturn($response)
        ;

        $this->assertEquals(
            $response,
            $this->controller->reportAction()
        );
    }

    public function testCreateFromJsonAction()
    {
        $mesurementName = 'fooName';
        $request        = new Request([], [
            'mesurement[name]' => $mesurementName,
        ]);

        $fomType    = $this->prophesize(FormInterface::class);
        $mesurement = $this->prophesize(Mesurement::class);

        $this->formFactory->create(MesurementType::class, null, ['csrf_protection' => false])->shouldBeCalled()->willReturn($fomType->reveal());

        $fomType->handleRequest($request)->shouldBeCalled()->willReturn(true);
        $fomType->isSubmitted()->shouldBeCalled()->willReturn(true);
        $fomType->isValid()->shouldBeCalled()->willReturn(true);
        $fomType->getData()->shouldBeCalled()->willReturn($mesurement->reveal());
        $mesurement->setStatus(MesurementStatusDictionary::STATUS_NOT_APPLIED)->shouldBeCalled();

        $this->managerProphecy->persist($mesurement->reveal())->shouldBeCalled();
        $this->managerProphecy->flush()->shouldBeCalled();

        $uuid = Uuid::uuid4();
        $mesurement->getId()->shouldBeCalled()->willReturn($uuid);
        $mesurement->getName()->shouldBeCalled()->willReturn($mesurementName);

        $expectedResponse = \json_encode([
            'id'   => $uuid->toString(),
            'name' => $mesurementName,
        ]);

        $response = $this->controller->createFromJsonAction($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(Response::HTTP_CREATED, $response->getStatusCode());
        $decoded_response = \json_decode($response->getContent(), true);
        $this->assertEquals($expectedResponse, $decoded_response);
    }
}
