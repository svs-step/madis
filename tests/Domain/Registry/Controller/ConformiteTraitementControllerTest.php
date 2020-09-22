<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2019 Soluris - Solutions Numériques Territoriales Innovantes
 * @author ANODE <contact@agence-anode.fr>
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
use App\Domain\Registry\Controller\ConformiteTraitementController;
use App\Domain\Registry\Controller\ContractorController;
use App\Domain\Registry\Form\Type\ConformiteTraitement\ConformiteTraitementType;
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
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ConformiteTraitementControllerTest extends TestCase
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
     * @var Repository\ConformiteTraitement\ConformiteTraitement
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
     * @var Repository\Treatment
     */
    private $treatmentRepository;

    /**
     * @var ContractorController
     */
    private $controller;

    /**
     * @var Repository\ConformiteTraitement\Question
     */
    private $questionRepository;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var Pdf|ObjectProphecy
     */
    private $pdf;

    public function setUp()
    {
        $this->managerProphecy                = $this->prophesize(EntityManagerInterface::class);
        $this->translatorProphecy             = $this->prophesize(TranslatorInterface::class);
        $this->repositoryProphecy             = $this->prophesize(Repository\ConformiteTraitement\ConformiteTraitement::class);
        $this->collectivityRepositoryProphecy = $this->prophesize(UserRepository\Collectivity::class);
        $this->wordHandlerProphecy            = $this->prophesize(WordHandler::class);
        $this->authenticationCheckerProphecy  = $this->prophesize(AuthorizationCheckerInterface::class);
        $this->userProviderProphecy           = $this->prophesize(UserProvider::class);
        $this->treatmentRepository            = $this->prophesize(Repository\Treatment::class);
        $this->questionRepository             = $this->prophesize(Repository\ConformiteTraitement\Question::class);
        $this->eventDispatcher                = $this->prophesize(EventDispatcherInterface::class);
        $this->pdf                            = $this->prophesize(Pdf::class);

        $this->controller = new ConformiteTraitementController(
            $this->managerProphecy->reveal(),
            $this->translatorProphecy->reveal(),
            $this->repositoryProphecy->reveal(),
            $this->collectivityRepositoryProphecy->reveal(),
            $this->wordHandlerProphecy->reveal(),
            $this->authenticationCheckerProphecy->reveal(),
            $this->userProviderProphecy->reveal(),
            $this->treatmentRepository->reveal(),
            $this->questionRepository->reveal(),
            $this->eventDispatcher->reveal(),
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
            'conformite_traitement',
            $this->invokeMethod($this->controller, 'getModel', [])
        );
    }

    public function testGetModelClass()
    {
        $this->assertEquals(
            Model\ConformiteTraitement\ConformiteTraitement::class,
            $this->invokeMethod($this->controller, 'getModelClass', [])
        );
    }

    public function testGetFormType()
    {
        $this->assertEquals(
            ConformiteTraitementType::class,
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
        $collectivity = $this->prophesize(UserModel\Collectivity::class);
        $user->getRoles()->shouldBeCalled()->willReturn([]);

        $this->userProviderProphecy
            ->getAuthenticatedUser()
            ->shouldBeCalled()
            ->willReturn($user)
        ;

        $user->getCollectivity()
            ->shouldBeCalled()
            ->willReturn($collectivity)
        ;

        $this->treatmentRepository
            ->findAllActiveByCollectivityWithHasModuleConformiteTraitement($collectivity->reveal())
            ->shouldBeCalled()
            ->willReturn($valueReturnedByRepository)
        ;

        $this->assertEquals(
            $valueReturnedByRepository,
            $this->invokeMethod($this->controller, 'getListData')
        );
    }
}
