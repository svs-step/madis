<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2019 Soluris - Solutions Numériques Territoriales Innovantes
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

namespace App\Tests\Domain\Reporting\Metrics;

use App\Application\Symfony\Security\UserProvider;
use App\Domain\Maturity\Model\Referentiel;
use App\Domain\Maturity\Model\Survey;
use App\Domain\Registry\Model\Contractor;
use App\Domain\Registry\Model\Mesurement;
use App\Domain\Registry\Model\Treatment;
use App\Domain\Registry\Model\Violation;
use App\Domain\Registry\Repository\ConformiteTraitement\ConformiteTraitement;
use App\Domain\Registry\Repository\Request;
use App\Domain\Reporting\Metrics\MetricInterface;
use App\Domain\Reporting\Metrics\UserMetric;
use App\Domain\User\Model\Collectivity;
use App\Domain\User\Model\User;
use App\Infrastructure\ORM\Registry\Repository\ConformiteOrganisation\Evaluation;
use App\Infrastructure\ORM\Reporting\Repository\LogJournal;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use App\Infrastructure\ORM\Maturity\Repository\Survey as SurveyRepository;

class UserMetricTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ConformiteTraitement
     */
    private $conformiteTraitementRepository;

    /**
     * @var Evaluation|ObjectProphecy
     */
    private $evaluationRepository;

    /**
     * @var Request
     */
    private $requestRepository;

    /**
     * @var \App\Domain\Registry\Repository\Treatment
     */
    private $treatmentRepository;

    /**
     * @var UserProvider
     */
    private $userProvider;

    /**
     * @var UserMetric
     */
    private $userMetric;

    /**
     * @var LogJournal
     */
    private $logJournalRepository;
    /**
     * @var SurveyRepository
     */
    private $surveyRepository;

    protected function setUp(): void
    {
        $this->entityManager                  = $this->prophesize(EntityManagerInterface::class);
        $this->conformiteTraitementRepository = $this->prophesize(ConformiteTraitement::class);
        $this->requestRepository              = $this->prophesize(Request::class);
        $this->treatmentRepository            = $this->prophesize(\App\Domain\Registry\Repository\Treatment::class);
        $this->userProvider                   = $this->prophesize(UserProvider::class);
        $this->evaluationRepository           = $this->prophesize(Evaluation::class);
        $this->logJournalRepository           = $this->prophesize(LogJournal::class);
        $this->surveyRepository               = $this->prophesize(SurveyRepository::class);
        $this->userMetric = new UserMetric(
            $this->entityManager->reveal(),
            $this->conformiteTraitementRepository->reveal(),
            $this->requestRepository->reveal(),
            $this->treatmentRepository->reveal(),
            $this->userProvider->reveal(),
            $this->evaluationRepository->reveal(),
            $this->logJournalRepository->reveal(),
            $this->surveyRepository->reveal(),
            15
        );
    }

    public function testItInstanceOfMetricInterface()
    {
        $this->assertInstanceOf(MetricInterface::class, $this->userMetric);
    }

    public function testItReturnTemplateName()
    {
        $this->assertSame('Reporting/Dashboard/index.html.twig', $this->userMetric->getTemplateViewName());
    }

    public function testItReturnData()
    {
        $user         = $this->prophesize(User::class);
        $collectivity = new Collectivity();
        $referentiel = new Referentiel();
        $user->getCollectivity()->willReturn($collectivity);
        $this->userProvider->getAuthenticatedUser()->shouldBeCalled()->willReturn($user);

        $contractorRepo = $this->prophesize(ObjectRepository::class);
        $mesurementRepo = $this->prophesize(ObjectRepository::class);
        $treatmentRepo  = $this->prophesize(ObjectRepository::class);
        $violationRepo  = $this->prophesize(ObjectRepository::class);
        $this->entityManager->getRepository(Contractor::class)->shouldBeCalled()->willReturn($contractorRepo);

        $this->entityManager->getRepository(Mesurement::class)->shouldBeCalled()->willReturn($mesurementRepo);
        $this->entityManager->getRepository(Treatment::class)->shouldBeCalled()->willReturn($treatmentRepo);
        $this->entityManager->getRepository(Violation::class)->shouldBeCalled()->willReturn($violationRepo);
        $contractorRepo->findBy(Argument::cetera())->shouldBeCalled()->willReturn([]);
        $this->surveyRepository->findLatestByReferentialAndCollectivity(Argument::cetera())->shouldBeCalled()->willReturn([]);
        $mesurementRepo->findBy(Argument::cetera())->shouldBeCalled()->willReturn([]);
        $treatmentRepo->findBy(Argument::cetera())->shouldBeCalled()->willReturn([]);
        $violationRepo->findBy(Argument::cetera())->shouldBeCalled()->willReturn([]);
        $this->requestRepository->findAllByCollectivity(Argument::cetera())->shouldBeCalled()->willReturn([]);
        $collectivity->setHasModuleConformiteTraitement(true);
        $this->conformiteTraitementRepository->findAllByCollectivity(Argument::cetera())->shouldBeCalled()->willReturn([]);
        $this->evaluationRepository->findLastByOrganisation(Argument::any())->shouldBeCalled()->willReturn(null);
        $this->logJournalRepository->findAllByCollectivityWithoutSubjects($collectivity, 15, Argument::any())->shouldBeCalled()->willReturn([]);

        $this->assertIsArray($this->userMetric->getData($referentiel));
    }
}
