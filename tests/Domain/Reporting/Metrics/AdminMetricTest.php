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

use App\Domain\Maturity;
use App\Domain\Registry;
use App\Domain\Reporting\Metrics\AdminMetric;
use App\Domain\Reporting\Metrics\MetricInterface;
use App\Domain\User;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Security\Core\Security;

class AdminMetricTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var User\Repository\Collectivity
     */
    private $collectivityRepository;

    /**
     * @var Registry\Repository\Mesurement
     */
    private $mesurementRepository;

    /**
     * @var Registry\Repository\Proof
     */
    private $proofRepository;

    /**
     * @var Maturity\Repository\Survey
     */
    private $surveyRepository;

    /**
     * @var Registry\Repository\Treatment
     */
    private $treatmentRepository;

    /**
     * @var Security
     */
    private $security;

    /**
     * @var AdminMetric
     */
    private $adminMetric;

    protected function setUp(): void
    {
        $this->collectivityRepository = $this->prophesize(User\Repository\Collectivity::class);
        $this->mesurementRepository   = $this->prophesize(Registry\Repository\Mesurement::class);
        $this->proofRepository        = $this->prophesize(Registry\Repository\Proof::class);
        $this->surveyRepository       = $this->prophesize(Maturity\Repository\Survey::class);
        $this->treatmentRepository    = $this->prophesize(Registry\Repository\Treatment::class);
        $this->security               = $this->prophesize(Security::class);

        $this->adminMetric = new AdminMetric(
            $this->collectivityRepository->reveal(),
            $this->mesurementRepository->reveal(),
            $this->proofRepository->reveal(),
            $this->surveyRepository->reveal(),
            $this->treatmentRepository->reveal(),
            $this->security->reveal()
        );
    }

    public function testItInstanceOfMetricInterface()
    {
        $this->assertInstanceOf(MetricInterface::class, $this->adminMetric);
    }

    public function testItReturnTemplateName()
    {
        $this->assertSame('Reporting/Dashboard/index_admin.html.twig', $this->adminMetric->getTemplateViewName());
    }

    public function testItReturnData()
    {
        $this->collectivityRepository->findAllActive()->shouldBeCalled()->willReturn([]);
        $this->security->isGranted(Argument::any())->willReturn(true);

        $this->assertIsArray($this->adminMetric->getData());
    }
}
