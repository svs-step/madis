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

namespace App\Tests\Domain\Reporting\Handler;

use App\Domain\Reporting\Generator\Csv\CollectivityGenerator;
use App\Domain\Reporting\Generator\Csv\ContractorGenerator;
use App\Domain\Reporting\Generator\Csv\MesurementGenerator;
use App\Domain\Reporting\Generator\Csv\TreatmentGenerator;
use App\Domain\Reporting\Handler\ExportCsvHandler;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportCsvHandlerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var CollectivityGenerator
     */
    private $collectivityGenerator;

    /**
     * @var ContractorGenerator
     */
    private $contractorGenerator;

    /**
     * @var TreatmentGenerator
     */
    private $treatmentGenerator;

    /**
     * @var MesurementGenerator
     */
    private $mesurementGenerator;

    /**
     * @var ExportCsvHandler
     */
    private $handler;

    protected function setUp(): void
    {
        $this->collectivityGenerator = $this->prophesize(CollectivityGenerator::class);
        $this->contractorGenerator   = $this->prophesize(ContractorGenerator::class);
        $this->treatmentGenerator    = $this->prophesize(TreatmentGenerator::class);
        $this->mesurementGenerator   = $this->prophesize(MesurementGenerator::class);

        $this->handler = new ExportCsvHandler(
            $this->collectivityGenerator->reveal(),
            $this->contractorGenerator->reveal(),
            $this->treatmentGenerator->reveal(),
            $this->mesurementGenerator->reveal()
        );
    }

    /**
     * @dataProvider typesProvider
     */
    public function testItReturnAnBinaryResponseOnType(string $generator, string $type)
    {
        $this->$generator->generateResponse($type)->shouldBeCalled()->willReturn($this->prophesize(BinaryFileResponse::class));
        $response = $this->handler->generateCsv($type);

        $this->assertInstanceOf(BinaryFileResponse::class, $response);
    }

    public function testItThrownAnLogicalExceptionOnBadType()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('The type foo is not support for csv export');

        $this->handler->generateCsv('foo');
    }

    public function typesProvider()
    {
        return [
            ['collectivityGenerator', ExportCsvHandler::COLLECTIVITY_TYPE],
            ['treatmentGenerator', ExportCsvHandler::TREATMENT_TYPE],
            ['contractorGenerator', ExportCsvHandler::CONTRACTOR_TYPE],
            ['mesurementGenerator', ExportCsvHandler::MESUREMENT_TYPE],
        ];
    }
}
