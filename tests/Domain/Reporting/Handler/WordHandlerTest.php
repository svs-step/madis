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
use App\Domain\Reporting\Generator\Word\MesurementGenerator;
use App\Domain\Reporting\Generator\Word\OverviewGenerator;
use App\Domain\Reporting\Generator\Word\TreatmentGenerator;
use App\Domain\Reporting\Handler\WordHandler;
use App\Tests\Utils\ReflectionTrait;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\PhpWord;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class WordHandlerTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @var PhpWord
     */
    private $phpWordProphecy;

    /**
     * @var ContractorGenerator
     */
    private $contractorGeneratorProphecy;

    /**
     * @var MesurementGenerator
     */
    private $mesurementGeneratorProphecy;

    /**
     * @var OverviewGenerator
     */
    private $overviewGeneratorProphecy;

    /**
     * @var TreatmentGenerator
     */
    private $treatmentGeneratorProphecy;

    /**
     * @var WordHandler
     */
    private $handler;

    protected function setUp()
    {
        $this->phpWordProphecy             = $this->prophesize(PhpWord::class);
        $this->contractorGeneratorProphecy = $this->prophesize(ContractorGenerator::class);
        $this->mesurementGeneratorProphecy = $this->prophesize(MesurementGenerator::class);
        $this->overviewGeneratorProphecy   = $this->prophesize(OverviewGenerator::class);
        $this->treatmentGeneratorProphecy  = $this->prophesize(TreatmentGenerator::class);

        $this->handler = new WordHandler(
            $this->phpWordProphecy->reveal(),
            $this->contractorGeneratorProphecy->reveal(),
            $this->mesurementGeneratorProphecy->reveal(),
            $this->overviewGeneratorProphecy->reveal(),
            $this->treatmentGeneratorProphecy->reveal()
        );
    }

    /**
     * Test generateOverviewReport.
     */
    public function testGenerateOverviewReport(): void
    {
        $section          = Argument::type(Section::class);
        $treatments       = [];
        $contractors      = [];
        $responseProphecy = $this->prophesize(BinaryFileResponse::class);

        $phpWord = $this->phpWordProphecy->reveal();

        $this->overviewGeneratorProphecy->generateFirstPage($phpWord)->shouldBeCalled();
        $this->overviewGeneratorProphecy->createContentSection($phpWord)->shouldBeCalled();
        $this->overviewGeneratorProphecy->generateTableOfContent($section)->shouldBeCalled();

        // Object
        $this->overviewGeneratorProphecy->generateObjectPart($section)->shouldBeCalled();

        // Organism introduction
        $this->overviewGeneratorProphecy->generateOrganismIntroductionPart($section)->shouldBeCalled();

        // Registries
        $this->overviewGeneratorProphecy->generateRegistries($section, $treatments, $contractors)->shouldBeCalled();

        // Generation
        $this->overviewGeneratorProphecy
            ->generateResponse($phpWord, 'bilan')
            ->shouldBeCalled()
            ->willReturn($responseProphecy->reveal())
        ;

        $this->assertEquals(
            $responseProphecy->reveal(),
            $this->handler->generateOverviewReport($treatments, $contractors)
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
            ->generateHeader($this->phpWordProphecy->reveal(), 'Registre des sous-traitants')
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
     * Test generateRegistryMesurementReport.
     */
    public function testGenerateRegistryMesurementReport()
    {
        $contractors = [];
        $response    = $this->prophesize(BinaryFileResponse::class)->reveal();

        $this->mesurementGeneratorProphecy
            ->generateHeader($this->phpWordProphecy->reveal(), 'Registre des actions de protection')
            ->shouldBeCalled()
        ;
        $this->mesurementGeneratorProphecy
            ->generateOverview($this->phpWordProphecy->reveal(), $contractors)
            ->shouldBeCalled()
        ;
        $this->mesurementGeneratorProphecy
            ->generateDetails($this->phpWordProphecy->reveal(), $contractors)
            ->shouldBeCalled()
        ;
        $this->mesurementGeneratorProphecy
            ->generateResponse($this->phpWordProphecy->reveal(), 'actions-de-protection')
            ->shouldBeCalled()
            ->willReturn($response)
        ;

        $this->assertEquals(
            $response,
            $this->handler->generateRegistryMesurementReport($contractors)
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
            ->generateHeader($this->phpWordProphecy->reveal(), 'Registre des traitements')
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
