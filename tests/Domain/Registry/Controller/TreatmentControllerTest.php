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

namespace App\Tests\Domain\Registry\Controller;

use App\Application\Controller\CRUDController;
use App\Application\Symfony\Security\UserProvider;
use App\Domain\Registry\Controller\TreatmentController;
use App\Domain\Registry\Form\Type\TreatmentType;
use App\Domain\Registry\Model;
use App\Domain\Registry\Repository;
use App\Domain\User\Model\Collectivity;
use App\Domain\User\Model\User;
use App\Tests\Utils\ReflectionTrait;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;

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
     * @var AuthorizationCheckerInterface
     */
    private $authenticationCheckerProphecy;

    /**
     * @var UserProvider
     */
    private $userProvierProphecy;

    /**
     * @var TreatmentController
     */
    private $controller;

    public function setUp()
    {
        $this->managerProphecy               = $this->prophesize(EntityManagerInterface::class);
        $this->translatorProphecy            = $this->prophesize(TranslatorInterface::class);
        $this->repositoryProphecy            = $this->prophesize(Repository\Treatment::class);
        $this->authenticationCheckerProphecy = $this->prophesize(AuthorizationCheckerInterface::class);
        $this->userProvierProphecy           = $this->prophesize(UserProvider::class);

        $this->controller = new TreatmentController(
            $this->managerProphecy->reveal(),
            $this->translatorProphecy->reveal(),
            $this->repositoryProphecy->reveal(),
            $this->authenticationCheckerProphecy->reveal(),
            $this->userProvierProphecy->reveal()
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
     * Test getListData
     * User is granted ROLE_ADMIN.
     */
    public function testGetListDataForRoleGranted()
    {
        $valueReturnedByRepository = ['dummyValues'];

        // Granted
        $this->authenticationCheckerProphecy
            ->isGranted('ROLE_ADMIN')
            ->shouldBeCalled()
            ->willReturn(true)
        ;

        // No need to restrict query to collectivity
        $this->userProvierProphecy
            ->getAuthenticatedUser()
            ->shouldNotBeCalled()
        ;

        // findAll must be called but not findAllByCollectivity
        $this->repositoryProphecy
            ->findAll()
            ->shouldBeCalled()
            ->willReturn($valueReturnedByRepository)
        ;
        $this->repositoryProphecy
            ->findAllByCollectivity()
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

        $collectivity = $this->prophesize(Collectivity::class)->reveal();
        $userProphecy = $this->prophesize(User::class);
        $userProphecy->getCollectivity()->shouldBeCalled()->willReturn($collectivity);
        $this->userProvierProphecy
            ->getAuthenticatedUser()
            ->shouldBeCalled()
            ->willReturn($userProphecy->reveal())
        ;

        // findAllByCollectivity must be called but not findAll
        $this->repositoryProphecy
            ->findAllByCollectivity($collectivity)
            ->shouldBeCalled()
            ->willReturn($valueReturnedByRepository)
        ;
        $this->repositoryProphecy
            ->findAll()
            ->shouldNotBeCalled()
        ;

        $this->assertEquals(
            $valueReturnedByRepository,
            $this->invokeMethod($this->controller, 'getListData')
        );
    }
}
