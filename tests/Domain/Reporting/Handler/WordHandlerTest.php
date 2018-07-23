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
        $section          = new Section(1);
        $title            = 'Bilan de gestion des données à caractère personnel';
        $documentName     = 'bilan';
        $treatments       = [];
        $contractors      = [];
        $responseProphecy = $this->prophesize(BinaryFileResponse::class);

        $phpWord = $this->phpWordProphecy->reveal();

        // Initialization + homepage + table of content
        $this->overviewGeneratorProphecy->initializeDocument($phpWord)->shouldBeCalled();
        $this->overviewGeneratorProphecy->addHomepage($phpWord, $title)->shouldBeCalled();
        $this->overviewGeneratorProphecy->createContentSection($phpWord, $title)->shouldBeCalled()->willReturn($section);
        $this->overviewGeneratorProphecy->addTableOfContent($section, 9)->shouldBeCalled();

        // Content
        $this->overviewGeneratorProphecy->generateObjectPart($section)->shouldBeCalled();
        $this->overviewGeneratorProphecy->generateOrganismIntroductionPart($section)->shouldBeCalled();
        $this->overviewGeneratorProphecy->generateRegistries($section, $treatments, $contractors)->shouldBeCalled();

        // Generation
        $this->overviewGeneratorProphecy
            ->generateResponse($phpWord, $documentName)
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
        $section          = new Section(1);
        $title            = 'Registre des sous-traitants';
        $documentName     = 'sous_traitants';
        $contractors      = [];
        $responseProphecy = $this->prophesize(BinaryFileResponse::class);

        $phpWord = $this->phpWordProphecy->reveal();

        // Initialization + homepage + table of content
        $this->contractorGeneratorProphecy->initializeDocument($phpWord)->shouldBeCalled();
        $this->contractorGeneratorProphecy->addHomepage($phpWord, $title)->shouldBeCalled();
        $this->contractorGeneratorProphecy->createContentSection($phpWord, $title)->shouldBeCalled()->willReturn($section);

        // Content
        $this->contractorGeneratorProphecy->addSyntheticView($section, $contractors)->shouldBeCalled();
        $this->contractorGeneratorProphecy->addDetailedView($section, $contractors)->shouldBeCalled();

        // Generation
        $this->contractorGeneratorProphecy
            ->generateResponse($phpWord, $documentName)
            ->shouldBeCalled()
            ->willReturn($responseProphecy->reveal())
        ;

        $this->assertEquals(
            $responseProphecy->reveal(),
            $this->handler->generateRegistryContractorReport($contractors)
        );
    }

    /**
     * Test generateRegistryMesurementReport.
     */
    public function testGenerateRegistryMesurementReport()
    {
        $section          = new Section(1);
        $title            = 'Registre des actions de protection';
        $documentName     = 'actions_de_protection';
        $mesurements      = [];
        $responseProphecy = $this->prophesize(BinaryFileResponse::class);

        $phpWord = $this->phpWordProphecy->reveal();

        // Initialization + homepage + table of content
        $this->mesurementGeneratorProphecy->initializeDocument($phpWord)->shouldBeCalled();
        $this->mesurementGeneratorProphecy->addHomepage($phpWord, $title)->shouldBeCalled();
        $this->mesurementGeneratorProphecy->createContentSection($phpWord, $title)->shouldBeCalled()->willReturn($section);

        // Content
        $this->mesurementGeneratorProphecy->addSyntheticView($section, $mesurements)->shouldBeCalled();
        $this->mesurementGeneratorProphecy->addDetailedView($section, $mesurements)->shouldBeCalled();

        // Generation
        $this->mesurementGeneratorProphecy
            ->generateResponse($phpWord, $documentName)
            ->shouldBeCalled()
            ->willReturn($responseProphecy->reveal())
        ;

        $this->assertEquals(
            $responseProphecy->reveal(),
            $this->handler->generateRegistryMesurementReport($mesurements)
        );
    }

    /**
     * Test generateRegistryTreatmentReport.
     */
    public function testGenerateRegistryTreatmentReport()
    {
        $section          = new Section(1);
        $title            = 'Registre des traitements';
        $documentName     = 'traitements';
        $treatments       = [];
        $responseProphecy = $this->prophesize(BinaryFileResponse::class);

        $phpWord = $this->phpWordProphecy->reveal();

        // Initialization + homepage + table of content
        $this->treatmentGeneratorProphecy->initializeDocument($phpWord)->shouldBeCalled();
        $this->treatmentGeneratorProphecy->addHomepage($phpWord, $title)->shouldBeCalled();
        $this->treatmentGeneratorProphecy->createContentSection($phpWord, $title)->shouldBeCalled()->willReturn($section);

        // Content
        $this->treatmentGeneratorProphecy->addSyntheticView($section, $treatments)->shouldBeCalled();
        $this->treatmentGeneratorProphecy->addDetailedView($section, $treatments)->shouldBeCalled();

        // Generation
        $this->treatmentGeneratorProphecy
            ->generateResponse($phpWord, $documentName)
            ->shouldBeCalled()
            ->willReturn($responseProphecy->reveal())
        ;

        $this->assertEquals(
            $responseProphecy->reveal(),
            $this->handler->generateRegistryTreatmentReport($treatments)
        );
    }
}
