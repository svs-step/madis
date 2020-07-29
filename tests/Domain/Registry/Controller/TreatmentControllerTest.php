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
use App\Domain\Registry\Controller\TreatmentController;
use App\Domain\Registry\Form\Type\TreatmentType;
use App\Domain\Registry\Model;
use App\Domain\Registry\Repository;
use App\Domain\Reporting\Handler\WordHandler;
use App\Domain\User\Model as UserModel;
use App\Domain\User\Repository as UserRepository;
use App\Tests\Utils\ReflectionTrait;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Snappy\Pdf;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class TreatmentControllerTest extends TestCase
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
     * @var Repository\Treatment
     */
    private $repositoryProphecy;

    /**
     * @var UserRepository\Collectivity
     */
    private $collectivityRepositoryProphecy;

    /**
     * @var RequestStack
     */
    private $requestStackProphecy;

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
     * @var Pdf|ObjectProphecy
     */
    private $pdf;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var TreatmentController
     */
    private $controller;

    public function setUp()
    {
        $this->managerProphecy                = $this->prophesize(EntityManagerInterface::class);
        $this->translatorProphecy             = $this->prophesize(TranslatorInterface::class);
        $this->repositoryProphecy             = $this->prophesize(Repository\Treatment::class);
        $this->collectivityRepositoryProphecy = $this->prophesize(UserRepository\Collectivity::class);
        $this->requestStackProphecy           = $this->prophesize(RequestStack::class);
        $this->wordHandlerProphecy            = $this->prophesize(WordHandler::class);
        $this->authenticationCheckerProphecy  = $this->prophesize(AuthorizationCheckerInterface::class);
        $this->userProviderProphecy           = $this->prophesize(UserProvider::class);
        $this->pdf                            = $this->prophesize(Pdf::class);
        $this->router                         = $this->prophesize(RouterInterface::class);

        $this->controller = new TreatmentController(
            $this->managerProphecy->reveal(),
            $this->translatorProphecy->reveal(),
            $this->repositoryProphecy->reveal(),
            $this->collectivityRepositoryProphecy->reveal(),
            $this->requestStackProphecy->reveal(),
            $this->wordHandlerProphecy->reveal(),
            $this->authenticationCheckerProphecy->reveal(),
            $this->userProviderProphecy->reveal(),
            $this->pdf->reveal(),
            $this->router->reveal()
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
            'treatment',
            $this->invokeMethod($this->controller, 'getModel', [])
        );
    }

    public function testGetModelClass()
    {
        $this->assertEquals(
            Model\Treatment::class,
            $this->invokeMethod($this->controller, 'getModelClass', [])
        );
    }

    public function testGetFormType()
    {
        $this->assertEquals(
            TreatmentType::class,
            $this->invokeMethod($this->controller, 'getFormType', [])
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
        $treatments  = [];
        $response    = $this->prophesize(BinaryFileResponse::class)->reveal();

        $collectivity = $this->prophesize(UserModel\Collectivity::class)->reveal();
        $userProphecy = $this->prophesize(UserModel\User::class);
        $userProphecy->getCollectivity()->shouldBeCalled()->willReturn($collectivity);
        $this->userProviderProphecy
            ->getAuthenticatedUser()
            ->shouldBeCalled()
            ->willReturn($userProphecy->reveal())
        ;

        // findAllByCollectivity must be called but not findAll
        $this->repositoryProphecy
            ->findAllActiveByCollectivity($collectivity, true, [$orderKey => $orderDir])
            ->shouldBeCalled()
            ->willReturn($treatments)
        ;

        $this->wordHandlerProphecy
            ->generateRegistryTreatmentReport($treatments)
            ->shouldBeCalled()
            ->willReturn($response)
        ;

        $this->assertEquals(
            $response,
            $this->controller->reportAction()
        );
    }
}
