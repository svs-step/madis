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

namespace App\Tests\Domain\Reporting\Controller;

use App\Application\Symfony\Security\UserProvider;
use App\Domain\Maturity\Repository as MaturityRepository;
use App\Domain\Registry\Model\ConformiteOrganisation as ConformiteOrganisationModel;
use App\Domain\Registry\Repository as RegistryRepository;
use App\Domain\Reporting\Controller\ReviewController;
use App\Domain\Reporting\Handler\WordHandler;
use App\Domain\User\Model as UserModel;
use App\Infrastructure\ORM\Registry\Repository\ConformiteOrganisation\Evaluation;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ReviewControllerTest extends TestCase
{
    use ProphecyTrait;

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
     * @var RegistryRepository\Request
     */
    private $requestRepositoryProphecy;

    /**
     * @var RegistryRepository\Violation
     */
    private $violationRepositoryProphecy;

    /**
     * @var Evaluation|ObjectProphecy
     */
    private $evaluationRepository;

    /**
     * @var ReviewController
     */
    private $controller;

    protected function setUp(): void
    {
        $this->wordHandlerProphecy          = $this->prophesize(WordHandler::class);
        $this->userProviderProphecy         = $this->prophesize(UserProvider::class);
        $this->authorizationCheckerProphecy = $this->prophesize(AuthorizationCheckerInterface::class);
        $this->treatmentRepositoryProphecy  = $this->prophesize(RegistryRepository\Treatment::class);
        $this->contractorRepositoryProphecy = $this->prophesize(RegistryRepository\Contractor::class);
        $this->mesurementRepositoryProphecy = $this->prophesize(RegistryRepository\Mesurement::class);
        $this->surveyRepositoryProphecy     = $this->prophesize(MaturityRepository\Survey::class);
        $this->requestRepositoryProphecy    = $this->prophesize(RegistryRepository\Request::class);
        $this->violationRepositoryProphecy  = $this->prophesize(RegistryRepository\Violation::class);
        $this->evaluationRepository         = $this->prophesize(Evaluation::class);

        $this->controller = new ReviewController(
            $this->wordHandlerProphecy->reveal(),
            $this->userProviderProphecy->reveal(),
            $this->authorizationCheckerProphecy->reveal(),
            $this->treatmentRepositoryProphecy->reveal(),
            $this->contractorRepositoryProphecy->reveal(),
            $this->mesurementRepositoryProphecy->reveal(),
            $this->requestRepositoryProphecy->reveal(),
            $this->violationRepositoryProphecy->reveal(),
            $this->surveyRepositoryProphecy->reveal(),
            $this->evaluationRepository->reveal()
        );
    }

    /**
     * Test indexAction.
     *
     * @throws \PhpOffice\PhpWord\Exception\Exception
     */
    public function testIndexAction()
    {
        $id                    = 'uuid';
        $collectivity          = $this->prophesize(UserModel\Collectivity::class)->reveal();
        $treatments            = [];
        $contractors           = [];
        $mesurements           = [];
        $survey                = [];
        $maturity              = $survey;
        $requests              = [];
        $violations            = [];
        $conformiteTraitements = [];
        $responseProphecy      = $this->prophesize(BinaryFileResponse::class);
        $evaluation            = $this->prophesize(ConformiteOrganisationModel\Evaluation::class);

        $userProphecy = $this->prophesize(UserModel\User::class);
        $userProphecy->getCollectivity()->shouldBeCalled()->willReturn($collectivity);
        $this->userProviderProphecy->getAuthenticatedUser()->shouldBeCalled()->willReturn($userProphecy->reveal());

        $this->treatmentRepositoryProphecy->findAllActiveByCollectivity($collectivity)->shouldBeCalled()->willReturn($treatments);
        $this->contractorRepositoryProphecy->findAllByCollectivity($collectivity)->shouldBeCalled()->willReturn($contractors);
        $this->mesurementRepositoryProphecy->findAllByCollectivity($collectivity)->shouldBeCalled()->willReturn($mesurements);
        $this->surveyRepositoryProphecy->findAllByCollectivity($collectivity, ['createdAt' => 'DESC'], 1, ['o.referentiel is not null'])->shouldBeCalled()->willReturn($survey);
        $this->requestRepositoryProphecy->findAllArchivedByCollectivity($collectivity, false)->shouldBeCalled()->willReturn($requests);
        $this->violationRepositoryProphecy->findAllArchivedByCollectivity($collectivity, false)->shouldBeCalled()->willReturn($violations);
        $this->evaluationRepository->findLastByOrganisation($collectivity)->shouldBeCalled()->willReturn($evaluation->reveal());
        $this->wordHandlerProphecy
            ->generateOverviewReport($treatments, $contractors, $mesurements, $maturity, $requests, $violations, $evaluation->reveal())
            ->shouldBeCalled()
            ->willReturn($responseProphecy->reveal())
        ;

        $this->assertEquals(
            $responseProphecy->reveal(),
            $this->controller->indexAction($id)
        );
    }
}
