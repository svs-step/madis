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
use App\Domain\Registry\Controller\ContractorController;
use App\Domain\Registry\Controller\ProofController;
use App\Domain\Registry\Form\Type\ProofType;
use App\Domain\Registry\Model;
use App\Domain\Registry\Repository;
use App\Domain\Reporting\Handler\WordHandler;
use App\Domain\User\Model\Collectivity;
use App\Domain\User\Model\User;
use App\Tests\Utils\ReflectionTrait;
use Doctrine\ORM\EntityManagerInterface;
use Gaufrette\Filesystem;
use Gaufrette\FilesystemInterface;
use Gaufrette\FilesystemMap;
use Gaufrette\StreamWrapper;
use Knp\Snappy\Pdf;
use Nelmio\Alice\ParameterBag;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProofControllerTest extends TestCase
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
     * @var Repository\Proof
     */
    private $repositoryProphecy;

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
     * @var FilesystemInterface
     */
    private $documentFilesystemProphecy;

    /**
     * @var Pdf|ObjectProphecy
     */
    private $pdf;

    /**
     * @var ContractorController
     */
    private $controller;

    public function setUp()
    {
        $this->managerProphecy               = $this->prophesize(EntityManagerInterface::class);
        $this->translatorProphecy            = $this->prophesize(TranslatorInterface::class);
        $this->repositoryProphecy            = $this->prophesize(Repository\Proof::class);
        $this->requestStackProphecy          = $this->prophesize(RequestStack::class);
        $this->wordHandlerProphecy           = $this->prophesize(WordHandler::class);
        $this->authenticationCheckerProphecy = $this->prophesize(AuthorizationCheckerInterface::class);
        $this->userProviderProphecy          = $this->prophesize(UserProvider::class);
        $this->documentFilesystemProphecy    = $this->prophesize(FilesystemInterface::class);
        $this->pdf                           = $this->prophesize(Pdf::class);

        $this->controller = new ProofController(
            $this->managerProphecy->reveal(),
            $this->translatorProphecy->reveal(),
            $this->repositoryProphecy->reveal(),
            $this->requestStackProphecy->reveal(),
            $this->wordHandlerProphecy->reveal(),
            $this->authenticationCheckerProphecy->reveal(),
            $this->userProviderProphecy->reveal(),
            $this->documentFilesystemProphecy->reveal(),
            $this->pdf->reveal()
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
            'proof',
            $this->invokeMethod($this->controller, 'getModel', [])
        );
    }

    public function testGetModelClass()
    {
        $this->assertEquals(
            Model\Proof::class,
            $this->invokeMethod($this->controller, 'getModelClass', [])
        );
    }

    public function testGetFormType()
    {
        $this->assertEquals(
            ProofType::class,
            $this->invokeMethod($this->controller, 'getFormType', [])
        );
    }

    public function testIsSoftDelete()
    {
        $this->assertFalse($this->invokeMethod($this->controller, 'isSoftDelete'));
    }

    /**
     * Test getListData
     * User is granted ROLE_ADMIN.
     */
    public function testGetListDataForRoleGranted()
    {
        $valueReturnedByRepository = ['dummyValues'];
        $archived                  = false;

        $request             = new Request();
        $request->attributes = new ParameterBag(['archive' => "'{$archived}'"]);
        $this->requestStackProphecy->getMasterRequest()->shouldBeCalled()->willReturn($request);

        // Granted
        $this->authenticationCheckerProphecy
            ->isGranted('ROLE_ADMIN')
            ->shouldBeCalled()
            ->willReturn(true)
        ;

        // No need to restrict query to collectivity
        $this->userProviderProphecy
            ->getAuthenticatedUser()
            ->shouldNotBeCalled()
        ;

        // findAll must be called but not findAllByCollectivity
        $this->repositoryProphecy
            ->findAllArchived($archived)
            ->shouldBeCalled()
            ->willReturn($valueReturnedByRepository)
        ;
        $this->repositoryProphecy
            ->findAllArchivedByCollectivity(Argument::cetera())
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
        $archived                  = false;

        $request             = new Request();
        $request->attributes = new ParameterBag(['archive' => "'{$archived}'"]);
        $this->requestStackProphecy->getMasterRequest()->shouldBeCalled()->willReturn($request);

        // Not granted
        $this->authenticationCheckerProphecy
            ->isGranted('ROLE_ADMIN')
            ->shouldBeCalled()
            ->willReturn(false)
        ;

        $collectivity = $this->prophesize(Collectivity::class)->reveal();
        $userProphecy = $this->prophesize(User::class);
        $userProphecy->getCollectivity()->shouldBeCalled()->willReturn($collectivity);
        $this->userProviderProphecy
            ->getAuthenticatedUser()
            ->shouldBeCalled()
            ->willReturn($userProphecy->reveal())
        ;

        // findAllByCollectivity must be called but not findAll
        $this->repositoryProphecy
            ->findAllArchivedByCollectivity($collectivity, $archived)
            ->shouldBeCalled()
            ->willReturn($valueReturnedByRepository)
        ;
        $this->repositoryProphecy
            ->findAllArchived(Argument::cetera())
            ->shouldNotBeCalled()
        ;

        $this->assertEquals(
            $valueReturnedByRepository,
            $this->invokeMethod($this->controller, 'getListData')
        );
    }

    /*
    public function testDownloadAction()
    {
        // Mock Wrapper
        StreamWrapper::register();
        $fsMap = new FilesystemMap();
        $fsMap->set('registry_proof_document', $this->prophesize(Filesystem::class)->reveal());
        StreamWrapper::setFilesystemMap($fsMap);

        $id = 'id';
        $objectProphecy = $this->prophesize(Model\Proof::class);
        $objectCollectivity = $this->prophesize(Collectivity::class)->reveal();
        $objectName = 'name';
        $objectDocument = 'document.png';
        $objectProphecy->getCollectivity()->shouldBeCalled()->willReturn($objectCollectivity);
        $objectProphecy->getName()->shouldBeCalled()->willReturn($objectName);
        $objectProphecy->getDocument()->shouldBeCalled()->willReturn($objectDocument);

        $userCollectivity = $objectCollectivity;
        $userProphecy = $this->prophesize(User::class);
        $userProphecy->getCollectivity()->shouldBeCalled()->willReturn($userCollectivity);
        $this->userProviderProphecy
            ->getAuthenticatedUser()
            ->shouldBeCalled()
            ->willReturn($userProphecy->reveal())
        ;

        // findAllByCollectivity must be called but not findAll
        $this->repositoryProphecy
            ->findOneById($id)
            ->shouldBeCalled()
            ->willReturn($objectProphecy->reveal())
        ;

        $this->assertInstanceOf(BinaryFileResponse::class, $this->controller->downloadAction($id));
    }
    */
}
