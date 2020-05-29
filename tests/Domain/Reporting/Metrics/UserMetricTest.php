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
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class UserMetricTest extends TestCase
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ConformiteTraitement
     */
    private $conformiteTraitementRepository;

    /**
     * @var Request
     */
    private $requestRepository;

    /**
     * @var UserProvider
     */
    private $userProvider;

    /**
     * @var UserMetric
     */
    private $userMetric;

    protected function setUp()
    {
        $this->entityManager                  = $this->prophesize(EntityManagerInterface::class);
        $this->conformiteTraitementRepository = $this->prophesize(ConformiteTraitement::class);
        $this->requestRepository              = $this->prophesize(Request::class);
        $this->userProvider                   = $this->prophesize(UserProvider::class);

        $this->userMetric = new UserMetric(
            $this->entityManager->reveal(),
            $this->conformiteTraitementRepository->reveal(),
            $this->requestRepository->reveal(),
            $this->userProvider->reveal()
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
        $user = $this->prophesize(User::class);
        $user->getCollectivity()->willReturn(new Collectivity());
        $this->userProvider->getAuthenticatedUser()->shouldBeCalled()->willReturn($user);

        $contractorRepo = $this->prophesize(ObjectRepository::class);
        $surveyRepo     = $this->prophesize(ObjectRepository::class);
        $mesurementRepo = $this->prophesize(ObjectRepository::class);
        $treatmentRepo  = $this->prophesize(ObjectRepository::class);
        $violationRepo  = $this->prophesize(ObjectRepository::class);
        $this->entityManager->getRepository(Contractor::class)->shouldBeCalled()->willReturn($contractorRepo);
        $this->entityManager->getRepository(Survey::class)->shouldBeCalled()->willReturn($surveyRepo);
        $this->entityManager->getRepository(Mesurement::class)->shouldBeCalled()->willReturn($mesurementRepo);
        $this->entityManager->getRepository(Treatment::class)->shouldBeCalled()->willReturn($treatmentRepo);
        $this->entityManager->getRepository(Violation::class)->shouldBeCalled()->willReturn($violationRepo);
        $contractorRepo->findBy(Argument::cetera())->shouldBeCalled()->willReturn([]);
        $surveyRepo->findBy(Argument::cetera())->shouldBeCalled()->willReturn([]);
        $mesurementRepo->findBy(Argument::cetera())->shouldBeCalled()->willReturn([]);
        $treatmentRepo->findBy(Argument::cetera())->shouldBeCalled()->willReturn([]);
        $violationRepo->findBy(Argument::cetera())->shouldBeCalled()->willReturn([]);
        $this->requestRepository->findAllByCollectivity(Argument::cetera())->shouldBeCalled()->willReturn([]);
        $this->conformiteTraitementRepository->findAllByCollectivity(Argument::cetera())->shouldBeCalled()->willReturn([]);

        $this->assertIsArray($this->userMetric->getData());
    }
}
