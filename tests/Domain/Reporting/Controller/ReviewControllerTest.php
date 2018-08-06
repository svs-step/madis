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
use App\Domain\Maturity\Repository as MaturityRepository;
use App\Domain\Registry\Repository as RegistryRepository;
use App\Domain\Reporting\Controller\ReviewController;
use App\Domain\Reporting\Handler\WordHandler;
use App\Domain\User\Model as UserModel;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ReviewControllerTest extends TestCase
{
    /**
     * @var WordHandler
     */
    private $wordHandlerProphecy;

    /**
     * @var UserProvider
     */
    private $userProviderProphecy;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationCheckerProphecy;

    /**
     * @var RegistryRepository\Treatment
     */
    private $treatmentRepositoryProphecy;

    /**
     * @var RegistryRepository\Contractor
     */
    private $contractorRepositoryProphecy;

    /**
     * @var RegistryRepository\Mesurement
     */
    private $mesurementRepositoryProphecy;

    /**
     * @var MaturityRepository\Survey;
     */
    private $surveyRepositoryProphecy;

    /**
     * @var ReviewController
     */
    private $controller;

    protected function setUp()
    {
        $this->wordHandlerProphecy            = $this->prophesize(WordHandler::class);
        $this->userProviderProphecy           = $this->prophesize(UserProvider::class);
        $this->authorizationCheckerProphecy   = $this->prophesize(AuthorizationCheckerInterface::class);
        $this->treatmentRepositoryProphecy    = $this->prophesize(RegistryRepository\Treatment::class);
        $this->contractorRepositoryProphecy   = $this->prophesize(RegistryRepository\Contractor::class);
        $this->mesurementRepositoryProphecy   = $this->prophesize(RegistryRepository\Mesurement::class);
        $this->surveyRepositoryProphecy       = $this->prophesize(MaturityRepository\Survey::class);

        $this->controller = new ReviewController(
            $this->wordHandlerProphecy->reveal(),
            $this->userProviderProphecy->reveal(),
            $this->authorizationCheckerProphecy->reveal(),
            $this->treatmentRepositoryProphecy->reveal(),
            $this->contractorRepositoryProphecy->reveal(),
            $this->mesurementRepositoryProphecy->reveal(),
            $this->surveyRepositoryProphecy->reveal()
        );
    }

    /**
     * Test indexAction.
     *
     * @throws \PhpOffice\PhpWord\Exception\Exception
     */
    public function testIndexAction()
    {
        $id               = 'uuid';
        $collectivity     = $this->prophesize(UserModel\Collectivity::class)->reveal();
        $treatments       = [];
        $contractors      = [];
        $mesurements      = [];
        $survey           = [];
        $maturity         = $survey;
        $responseProphecy = $this->prophesize(BinaryFileResponse::class);

        $userProphecy = $this->prophesize(UserModel\User::class);
        $userProphecy->getCollectivity()->shouldBeCalled()->willReturn($collectivity);
        $this->userProviderProphecy->getAuthenticatedUser()->shouldBeCalled()->willReturn($userProphecy->reveal());

        $this->treatmentRepositoryProphecy->findAllActiveByCollectivity($collectivity)->shouldBeCalled()->willReturn($treatments);
        $this->contractorRepositoryProphecy->findAllByCollectivity($collectivity)->shouldBeCalled()->willReturn($contractors);
        $this->mesurementRepositoryProphecy->findAllByCollectivity($collectivity)->shouldBeCalled()->willReturn($mesurements);
        $this->surveyRepositoryProphecy->findAllByCollectivity($collectivity, ['createdAt' => 'DESC'], 2)->shouldBeCalled()->willReturn($survey);
        $this->wordHandlerProphecy
            ->generateOverviewReport($treatments, $contractors, $mesurements, $maturity)
            ->shouldBeCalled()
            ->willReturn($responseProphecy->reveal())
        ;

        $this->assertEquals(
            $responseProphecy->reveal(),
            $this->controller->indexAction($id)
        );
    }
}
