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

namespace App\Tests\Domain\Reporting\Controller;

use App\Application\Symfony\Security\UserProvider;
use App\Domain\Reporting\Controller\ReviewController;
use App\Domain\Reporting\Generator\WordGenerator;
use App\Domain\User\Model as UserModel;
use App\Domain\User\Repository as UserRepository;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ReviewControllerTest extends TestCase
{
    /**
     * @var WordGenerator
     */
    private $generatorProphecy;

    /**
     * @var UserProvider
     */
    private $userProviderProphecy;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationCheckerProphecy;

    /**
     * @var UserRepository\Collectivity
     */
    private $collectivityRepositoryProphecy;

    /**
     * @var ReviewController
     */
    private $controller;

    protected function setUp()
    {
        $this->generatorProphecy              = $this->prophesize(WordGenerator::class);
        $this->userProviderProphecy           = $this->prophesize(UserProvider::class);
        $this->authorizationCheckerProphecy   = $this->prophesize(AuthorizationCheckerInterface::class);
        $this->collectivityRepositoryProphecy = $this->prophesize(UserRepository\Collectivity::class);

        $this->controller = new ReviewController(
            $this->generatorProphecy->reveal(),
            $this->userProviderProphecy->reveal(),
            $this->authorizationCheckerProphecy->reveal(),
            $this->collectivityRepositoryProphecy->reveal()
        );
    }

    /**
     * Test indexAction.
     *
     * @throws \PhpOffice\PhpWord\Exception\Exception
     */
    public function testIndexAction()
    {
        $id           = 'uuid';
        $collectivity = $this->prophesize(UserModel\Collectivity::class)->reveal();
        $response     = $this->prophesize(BinaryFileResponse::class);

        $userProphecy = $this->prophesize(UserModel\User::class);
        $userProphecy->getCollectivity()->shouldBeCalled()->willReturn($collectivity);
        $this->userProviderProphecy->getAuthenticatedUser()->shouldBeCalled()->willReturn($userProphecy->reveal());

        $this->collectivityRepositoryProphecy->findOneById($id)->shouldBeCalled()->willReturn($collectivity);

        // Not called since collectivity are the same
        $this->authorizationCheckerProphecy->isGranted('ROLE_ADMIN')->shouldNotBeCalled()->willReturn(true);

        $this->generatorProphecy->generateHeader()->shouldBeCalled();
        $this->generatorProphecy->generateCollectivitySection($collectivity)->shouldBeCalled();
        $this->generatorProphecy
            ->generateResponse(Argument::type('string'))
            ->shouldBeCalled()
            ->willReturn($response->reveal())
        ;

        $this->assertEquals(
            $response->reveal(),
            $this->controller->indexAction($id)
        );
    }

    /**
     * Test indexAction
     * Collectivity not found.
     *
     * @throws \PhpOffice\PhpWord\Exception\Exception
     */
    public function testIndexActionCollectivityNotFound()
    {
        $id = 'uuid';

        $this->expectException(NotFoundHttpException::class);
        $this->collectivityRepositoryProphecy->findOneById($id)->shouldBeCalled()->willReturn(null);

        // No call is made since no collectivity was found
        $this->userProviderProphecy->getAuthenticatedUser()->shouldNotBeCalled();

        $this->authorizationCheckerProphecy->isGranted(Argument::cetera())->shouldNotBeCalled();

        $this->generatorProphecy->generateHeader()->shouldNotBeCalled();
        $this->generatorProphecy->generateCollectivitySection(Argument::cetera())->shouldNotBeCalled();
        $this->generatorProphecy
            ->generateResponse(Argument::cetera())
            ->shouldNotBeCalled()
        ;

        $this->controller->indexAction($id);
    }

    /**
     * Test indexAction
     * I am not an admin and It's not my collectivity.
     *
     * @throws \PhpOffice\PhpWord\Exception\Exception
     */
    public function testIndexActionNotGrantedAdminNotMyCollectivity()
    {
        $id            = 'uuid';
        $collectivity1 = new UserModel\Collectivity();
        $collectivity2 = new UserModel\Collectivity();

        $this->expectException(AccessDeniedException::class);

        $userProphecy = $this->prophesize(UserModel\User::class);
        $userProphecy->getCollectivity()->shouldBeCalled()->willReturn($collectivity1);
        $this->userProviderProphecy->getAuthenticatedUser()->shouldBeCalled()->willReturn($userProphecy->reveal());

        $this->collectivityRepositoryProphecy->findOneById($id)->shouldBeCalled()->willReturn($collectivity2);

        $this->authorizationCheckerProphecy->isGranted('ROLE_ADMIN')->shouldBeCalled()->willReturn(false);

        // No generator is called since I can't get report
        $this->generatorProphecy->generateHeader()->shouldNotBeCalled();
        $this->generatorProphecy->generateCollectivitySection(Argument::cetera())->shouldNotBeCalled();
        $this->generatorProphecy->generateResponse(Argument::cetera())->shouldNotBeCalled();

        $this->controller->indexAction($id);
    }

    /**
     * Test indexAction
     * I am an admin and it's not my collectivity.
     *
     * @throws \PhpOffice\PhpWord\Exception\Exception
     */
    public function testIndexActionGrantedAdminNotMyCollectivity()
    {
        $id            = 'uuid';
        $collectivity1 = $this->prophesize(UserModel\Collectivity::class)->reveal();
        $collectivity2 = $this->prophesize(UserModel\Collectivity::class)->reveal();
        $response      = $this->prophesize(BinaryFileResponse::class);

        $userProphecy = $this->prophesize(UserModel\User::class);
        $userProphecy->getCollectivity()->shouldBeCalled()->willReturn($collectivity1);
        $this->userProviderProphecy->getAuthenticatedUser()->shouldBeCalled()->willReturn($userProphecy->reveal());

        $this->collectivityRepositoryProphecy->findOneById($id)->shouldBeCalled()->willReturn($collectivity2);

        $this->authorizationCheckerProphecy->isGranted('ROLE_ADMIN')->shouldBeCalled()->willReturn(true);

        $this->generatorProphecy->generateHeader()->shouldBeCalled();
        $this->generatorProphecy->generateCollectivitySection($collectivity2)->shouldBeCalled();
        $this->generatorProphecy
            ->generateResponse(Argument::type('string'))
            ->shouldBeCalled()
            ->willReturn($response->reveal())
        ;

        $this->assertEquals(
            $response->reveal(),
            $this->controller->indexAction($id)
        );
    }
}
