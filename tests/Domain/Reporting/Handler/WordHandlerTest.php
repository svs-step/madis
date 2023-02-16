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

namespace App\Tests\Domain\Reporting\Handler;

use App\Domain\Reporting\Generator\Word\ConformiteOrganisationGenerator;
use App\Domain\Reporting\Generator\Word\ConformiteTraitementGenerator;
use App\Domain\Reporting\Generator\Word\ContractorGenerator;
use App\Domain\Reporting\Generator\Word\MaturityGenerator;
use App\Domain\Reporting\Generator\Word\MesurementGenerator;
use App\Domain\Reporting\Generator\Word\OverviewGenerator;
use App\Domain\Reporting\Generator\Word\RequestGenerator;
use App\Domain\Reporting\Generator\Word\ToolGenerator;
use App\Domain\Reporting\Generator\Word\TreatmentGenerator;
use App\Domain\Reporting\Generator\Word\ViolationGenerator;
use App\Domain\Reporting\Handler\WordHandler;
use App\Tests\Utils\ReflectionTrait;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\PhpWord;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class WordHandlerTest extends TestCase
{
    use ReflectionTrait;
    use ProphecyTrait;

    /**
     * @var PhpWord
     */
    private $phpWordProphecy;

    /**
     * @var ContractorGenerator
     */
    private $contractorGeneratorProphecy;

    /**
     * @var MaturityGenerator
     */
    private $maturityGeneratorProphecy;

    /**
     * @var MesurementGenerator
     */
    private $mesurementGeneratorProphecy;

    /**
     * @var OverviewGenerator
     */
    private $overviewGeneratorProphecy;

    /**
     * @var RequestGenerator
     */
    private $requestGeneratorProphecy;

    /**
     * @var TreatmentGenerator
     */
    private $treatmentGeneratorProphecy;

    /**
     * @var ToolGenerator
     */
    private $toolGeneratorProphecy;

    /**
     * @var ViolationGenerator
     */
    private $violationGeneratorProphecy;

    /**
     * @var ConformiteTraitementGenerator
     */
    private $conformiteTraitementGeneratorProphecy;

    /**
     * @var ConformiteTraitementGenerator
     */
    private $conformiteOrganisationGenerator;

    /**
     * @var WordHandler
     */
    private $handler;

    protected function setUp(): void
    {
        $this->phpWordProphecy                       = $this->prophesize(PhpWord::class);
        $this->contractorGeneratorProphecy           = $this->prophesize(ContractorGenerator::class);
        $this->maturityGeneratorProphecy             = $this->prophesize(MaturityGenerator::class);
        $this->mesurementGeneratorProphecy           = $this->prophesize(MesurementGenerator::class);
        $this->overviewGeneratorProphecy             = $this->prophesize(OverviewGenerator::class);
        $this->requestGeneratorProphecy              = $this->prophesize(RequestGenerator::class);
        $this->treatmentGeneratorProphecy            = $this->prophesize(TreatmentGenerator::class);
        $this->violationGeneratorProphecy            = $this->prophesize(ViolationGenerator::class);
        $this->toolGeneratorProphecy                 = $this->prophesize(ToolGenerator::class);
        $this->conformiteTraitementGeneratorProphecy = $this->prophesize(ConformiteTraitementGenerator::class);
        $this->conformiteOrganisationGenerator       = $this->prophesize(ConformiteOrganisationGenerator::class);

        $this->handler = new WordHandler(
            $this->phpWordProphecy->reveal(),
            $this->contractorGeneratorProphecy->reveal(),
            $this->overviewGeneratorProphecy->reveal(),
            $this->maturityGeneratorProphecy->reveal(),
            $this->mesurementGeneratorProphecy->reveal(),
            $this->requestGeneratorProphecy->reveal(),
            $this->treatmentGeneratorProphecy->reveal(),
            $this->violationGeneratorProphecy->reveal(),
            $this->toolGeneratorProphecy->reveal(),
            $this->conformiteTraitementGeneratorProphecy->reveal(),
            $this->conformiteOrganisationGenerator->reveal(),
        );
    }

    /**
     * Test generateOverviewReport.
     */
    public function testGenerateOverviewReport(): void
    {
        $section               = new Section(1);
        $title                 = 'Bilan de gestion des données à caractère personnel';
        $documentName          = 'bilan';
        $treatments            = [];
        $contractors           = [];
        $maturity              = [];
        $mesurements           = [];
        $requests              = [];
        $violations            = [];
        $conformiteTraitements = [];
        $responseProphecy      = $this->prophesize(BinaryFileResponse::class);
        $evaluation            = null;

        $phpWord = $this->phpWordProphecy->reveal();

        // Initialization + homepage + table of content
        $this->overviewGeneratorProphecy->initializeDocument($phpWord)->shouldBeCalled();
        $this->overviewGeneratorProphecy->addHomepage($phpWord, $title)->shouldBeCalled();
        $this->overviewGeneratorProphecy->createContentSection($phpWord, $title)->shouldBeCalled()->willReturn($section);
        $this->overviewGeneratorProphecy->addTableOfContent($section, 9)->shouldBeCalled();

        // Content
        $this->overviewGeneratorProphecy->generateObjectPart($section)->shouldBeCalled();
        $this->overviewGeneratorProphecy->generateOrganismIntroductionPart($section)->shouldBeCalled();
        $this->overviewGeneratorProphecy->generateRegistries($section, $treatments, $contractors, $requests, $violations)->shouldBeCalled();
        $this->overviewGeneratorProphecy->generateManagementSystemAndCompliance($section, $maturity, $mesurements, $conformiteTraitements, null)->shouldBeCalled();
        $this->overviewGeneratorProphecy->generateContinuousImprovements($section)->shouldBeCalled();
        $this->overviewGeneratorProphecy->generateAnnexeMention($section, $treatments)->shouldBeCalled();

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
        $this->contractorGeneratorProphecy->addTableOfContent($section, 1)->shouldBeCalled();

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
     * Test generateMaturitySurveyReport.
     */
    public function testGenerateMaturitySurveyReport()
    {
        $section          = new Section(1);
        $title            = 'Indice de maturité';
        $documentName     = 'indice_de_maturite';
        $data             = [];
        $responseProphecy = $this->prophesize(BinaryFileResponse::class);

        $phpWord = $this->phpWordProphecy->reveal();

        // Initialization + homepage + table of content
        $this->maturityGeneratorProphecy->initializeDocument($phpWord)->shouldBeCalled();
        $this->maturityGeneratorProphecy->addHomepage($phpWord, $title)->shouldBeCalled();
        $this->maturityGeneratorProphecy->createContentSection($phpWord, $title)->shouldBeCalled()->willReturn($section);
        $this->maturityGeneratorProphecy->addTableOfContent($section, 1)->shouldBeCalled();

        // Content
        $this->maturityGeneratorProphecy->addSyntheticView($section, $data)->shouldBeCalled();
        $this->maturityGeneratorProphecy->addDetailedView($section, $data)->shouldBeCalled();

        // Generation
        $this->maturityGeneratorProphecy
            ->generateResponse($phpWord, $documentName)
            ->shouldBeCalled()
            ->willReturn($responseProphecy->reveal())
        ;

        $this->assertEquals(
            $responseProphecy->reveal(),
            $this->handler->generateMaturitySurveyReport($data)
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
        $this->mesurementGeneratorProphecy->addTableOfContent($section, 1)->shouldBeCalled();

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
     * Test generateRegistryRequestReport.
     */
    public function testGenerateRegistryRequestReport()
    {
        $section          = new Section(1);
        $title            = 'Registre des demandes des personnes concernées';
        $documentName     = 'demandes_des_personnes_concernees';
        $requests         = [];
        $responseProphecy = $this->prophesize(BinaryFileResponse::class);

        $phpWord = $this->phpWordProphecy->reveal();

        // Initialization + homepage + table of content
        $this->requestGeneratorProphecy->initializeDocument($phpWord)->shouldBeCalled();
        $this->requestGeneratorProphecy->addHomepage($phpWord, $title)->shouldBeCalled();
        $this->requestGeneratorProphecy->createContentSection($phpWord, $title)->shouldBeCalled()->willReturn($section);
        $this->requestGeneratorProphecy->addTableOfContent($section, 1)->shouldBeCalled();

        // Content
        $this->requestGeneratorProphecy->addSyntheticView($section, $requests)->shouldBeCalled();
        $this->requestGeneratorProphecy->addDetailedView($section, $requests)->shouldBeCalled();

        // Generation
        $this->requestGeneratorProphecy
            ->generateResponse($phpWord, $documentName)
            ->shouldBeCalled()
            ->willReturn($responseProphecy->reveal())
        ;

        $this->assertEquals(
            $responseProphecy->reveal(),
            $this->handler->generateRegistryRequestReport($requests)
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
        $this->treatmentGeneratorProphecy->addTableOfContent($section, 1)->shouldBeCalled();

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

    /**
     * Test generateRegistryToolReport.
     */
    public function testGenerateRegistryToolReport()
    {
        $section          = new Section(1);
        $title            = 'Registre des logiciels et supports';
        $documentName     = 'logiciels-et-supports';
        $tools            = [];
        $responseProphecy = $this->prophesize(BinaryFileResponse::class);

        $phpWord = $this->phpWordProphecy->reveal();

        // Initialization + homepage + table of content
        $this->toolGeneratorProphecy->initializeDocument($phpWord)->shouldBeCalled();
        $this->toolGeneratorProphecy->addHomepage($phpWord, $title)->shouldBeCalled();
        $this->toolGeneratorProphecy->createContentSection($phpWord, $title)->shouldBeCalled()->willReturn($section);
        $this->toolGeneratorProphecy->addTableOfContent($section, 1)->shouldBeCalled();

        // Content
        $this->toolGeneratorProphecy->addSyntheticView($section, $tools)->shouldBeCalled();
        $this->toolGeneratorProphecy->addDetailedView($section, $tools)->shouldBeCalled();

        // Generation
        $this->toolGeneratorProphecy
            ->generateResponse($phpWord, $documentName)
            ->shouldBeCalled()
            ->willReturn($responseProphecy->reveal())
        ;

        $this->assertEquals(
            $responseProphecy->reveal(),
            $this->handler->generateRegistryToolReport($tools)
        );
    }

    /**
     * Test generateRegistryViolationReport.
     */
    public function testGenerateRegistryViolationReport()
    {
        $section          = new Section(1);
        $title            = 'Registre des violations';
        $documentName     = 'violations';
        $violations       = [];
        $responseProphecy = $this->prophesize(BinaryFileResponse::class);

        $phpWord = $this->phpWordProphecy->reveal();

        // Initialization + homepage + table of content
        $this->violationGeneratorProphecy->initializeDocument($phpWord)->shouldBeCalled();
        $this->violationGeneratorProphecy->addHomepage($phpWord, $title)->shouldBeCalled();
        $this->violationGeneratorProphecy->createContentSection($phpWord, $title)->shouldBeCalled()->willReturn($section);
        $this->violationGeneratorProphecy->addTableOfContent($section, 1)->shouldBeCalled();

        // Content
        $this->violationGeneratorProphecy->addSyntheticView($section, $violations)->shouldBeCalled();
        $this->violationGeneratorProphecy->addDetailedView($section, $violations)->shouldBeCalled();

        // Generation
        $this->violationGeneratorProphecy
            ->generateResponse($phpWord, $documentName)
            ->shouldBeCalled()
            ->willReturn($responseProphecy->reveal())
        ;

        $this->assertEquals(
            $responseProphecy->reveal(),
            $this->handler->generateRegistryViolationReport($violations)
        );
    }

    /**
     * Test generateRegistryMesurementReport.
     */
    public function testGenerateRegistryConformiteTraitementReport()
    {
        $section               = new Section(1);
        $title                 = 'Diagnostic de la conformité des traitements';
        $documentName          = 'conformite_des_traitements';
        $conformiteTraitements = [];
        $responseProphecy      = $this->prophesize(BinaryFileResponse::class);

        $phpWord = $this->phpWordProphecy->reveal();

        // Initialization + homepage + table of content
        $this->conformiteTraitementGeneratorProphecy->initializeDocument($phpWord)->shouldBeCalled();
        $this->conformiteTraitementGeneratorProphecy->addHomepage($phpWord, $title)->shouldBeCalled();
        $this->conformiteTraitementGeneratorProphecy->createContentSection($phpWord, $title)->shouldBeCalled()->willReturn($section);
        $this->conformiteTraitementGeneratorProphecy->addTableOfContent($section, 1)->shouldBeCalled();

        // Content
        $this->conformiteTraitementGeneratorProphecy->addSyntheticView($section, $conformiteTraitements)->shouldBeCalled();
        $this->conformiteTraitementGeneratorProphecy->addDetailedView($section, $conformiteTraitements)->shouldBeCalled();

        // Generation
        $this->conformiteTraitementGeneratorProphecy
            ->generateResponse($phpWord, $documentName)
            ->shouldBeCalled()
            ->willReturn($responseProphecy->reveal())
        ;

        $this->assertEquals(
            $responseProphecy->reveal(),
            $this->handler->generateRegistryConformiteTraitementReport($conformiteTraitements)
        );
    }
}
