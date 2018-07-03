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

namespace App\Tests\Domain\Reporting\Handler;

use App\Domain\Reporting\Generator\Word\ContractorGenerator;
use App\Domain\Reporting\Generator\Word\TreatmentGenerator;
use App\Domain\Reporting\Handler\WordHandler;
use PhpOffice\PhpWord\PhpWord;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class WordHandlerTest extends TestCase
{
    /**
     * @var PhpWord
     */
    private $phpWordProphecy;

    /**
     * @var ContractorGenerator
     */
    private $contractorGeneratorProphecy;

    /**
     * @var TreatmentGenerator
     */
    private $treatmentGeneratorProphecy;

    /**
     * @var array
     */
    private $dpo;

    /**
     * @var WordHandler
     */
    private $handler;

    protected function setUp()
    {
        $this->phpWordProphecy             = $this->prophesize(PhpWord::class);
        $this->contractorGeneratorProphecy = $this->prophesize(ContractorGenerator::class);
        $this->treatmentGeneratorProphecy  = $this->prophesize(TreatmentGenerator::class);
        $this->dpo                         = [];

        $this->handler = new WordHandler(
            $this->phpWordProphecy->reveal(),
            $this->contractorGeneratorProphecy->reveal(),
            $this->treatmentGeneratorProphecy->reveal(),
            $this->dpo
        );
    }

    /**
     * Test generateRegistryContractorReport.
     */
    public function testGenerateRegistryContractorReport()
    {
        $contractors = [];
        $response    = $this->prophesize(BinaryFileResponse::class)->reveal();

        $this->contractorGeneratorProphecy
            ->generateHeader($this->phpWordProphecy->reveal(), 'Sous-traitants')
            ->shouldBeCalled()
        ;
        $this->contractorGeneratorProphecy
            ->generateOverview($this->phpWordProphecy->reveal(), $contractors)
            ->shouldBeCalled()
        ;
        $this->contractorGeneratorProphecy
            ->generateDetails($this->phpWordProphecy->reveal(), $contractors)
            ->shouldBeCalled()
        ;
        $this->contractorGeneratorProphecy
            ->generateResponse($this->phpWordProphecy->reveal(), 'sous-traitants')
            ->shouldBeCalled()
            ->willReturn($response)
        ;

        $this->assertEquals(
            $response,
            $this->handler->generateRegistryContractorReport($contractors)
        );
    }

    /**
     * Test generateRegistryTreatmentReport.
     */
    public function testGenerateRegistryTreatmentReport()
    {
        $treatments = [];
        $response   = $this->prophesize(BinaryFileResponse::class)->reveal();

        $this->treatmentGeneratorProphecy
            ->generateHeader($this->phpWordProphecy->reveal(), 'Traitements')
            ->shouldBeCalled()
        ;
        $this->treatmentGeneratorProphecy
            ->generateOverview($this->phpWordProphecy->reveal(), $treatments)
            ->shouldBeCalled()
        ;
        $this->treatmentGeneratorProphecy
            ->generateDetails($this->phpWordProphecy->reveal(), $treatments)
            ->shouldBeCalled()
        ;
        $this->treatmentGeneratorProphecy
            ->generateResponse($this->phpWordProphecy->reveal(), 'traitements')
            ->shouldBeCalled()
            ->willReturn($response)
        ;

        $this->assertEquals(
            $response,
            $this->handler->generateRegistryTreatmentReport($treatments)
        );
    }
}
