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

namespace App\Domain\Reporting\Handler;

use App\Domain\Registry\Model\ConformiteOrganisation\Evaluation;
use App\Domain\Reporting\Generator\Word\ConformiteOrganisationGenerator;
use App\Domain\Reporting\Generator\Word\ConformiteTraitementGenerator;
use App\Domain\Reporting\Generator\Word\ContractorGenerator;
use App\Domain\Reporting\Generator\Word\MaturityGenerator;
use App\Domain\Reporting\Generator\Word\MesurementGenerator;
use App\Domain\Reporting\Generator\Word\OverviewGenerator;
use App\Domain\Reporting\Generator\Word\RequestGenerator;
use App\Domain\Reporting\Generator\Word\TreatmentGenerator;
use App\Domain\Reporting\Generator\Word\ViolationGenerator;
use PhpOffice\PhpWord\PhpWord;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class WordHandler
{
    /**
     * @var PhpWord
     */
    private $document;

    /**
     * @var ContractorGenerator
     */
    private $contractorGenerator;

    /**
     * @var OverviewGenerator
     */
    private $overviewGenerator;

    /**
     * @var MaturityGenerator
     */
    private $maturityGenerator;

    /**
     * @var MesurementGenerator
     */
    private $mesurementGenerator;

    /**
     * @var RequestGenerator
     */
    private $requestGenerator;

    /**
     * @var TreatmentGenerator
     */
    private $treatmentGenerator;

    /**
     * @var ViolationGenerator
     */
    private $violationGenerator;

    /**
     * @var ConformiteTraitementGenerator
     */
    private $conformiteTraitementGenerator;

    /**
     * @var ConformiteOrganisationGenerator
     */
    private $conformiteOrganisationGenerator;

    public function __construct(
        PhpWord $document,
        ContractorGenerator $contractorGenerator,
        OverviewGenerator $overviewGenerator,
        MaturityGenerator $maturityGenerator,
        MesurementGenerator $mesurementGenerator,
        RequestGenerator $requestGenerator,
        TreatmentGenerator $treatmentGenerator,
        ViolationGenerator $violationGenerator,
        ConformiteTraitementGenerator $conformiteTraitementGenerator,
        ConformiteOrganisationGenerator $conformiteOrganisationGenerator
    ) {
        $this->document                        = $document;
        $this->contractorGenerator             = $contractorGenerator;
        $this->overviewGenerator               = $overviewGenerator;
        $this->maturityGenerator               = $maturityGenerator;
        $this->mesurementGenerator             = $mesurementGenerator;
        $this->requestGenerator                = $requestGenerator;
        $this->treatmentGenerator              = $treatmentGenerator;
        $this->violationGenerator              = $violationGenerator;
        $this->conformiteTraitementGenerator   = $conformiteTraitementGenerator;
        $this->conformiteOrganisationGenerator = $conformiteOrganisationGenerator;
    }

    /**
     * Generate overview report.
     *
     * @param array $treatments  Treatments used for overview report generation
     * @param array $contractors Contractors used for overview report generation
     * @param array $mesurements Mesurements used for overview report generation
     * @param array $maturity    Surveys maturity used for overview report generation
     * @param array $requests    Requests used for overview report generation
     * @param array $violations  Violations used for overview report generation
     *
     * @throws \PhpOffice\PhpWord\Exception\Exception
     *
     * @return BinaryFileResponse The generated Word document
     */
    public function generateOverviewReport(
        array $treatments = [],
        array $contractors = [],
        array $mesurements = [],
        array $maturity = [],
        array $requests = [],
        array $violations = [],
        array $conformiteTraitements = []
    ): BinaryFileResponse {
        $title = 'Bilan de gestion des données à caractère personnel';

        // Initialize document
        $this->overviewGenerator->initializeDocument($this->document);

        // Begin generation
        $this->overviewGenerator->addHomepage($this->document, $title);

        // Section which will get whole content
        $contentSection = $this->overviewGenerator->createContentSection($this->document, $title);

        // Table of content
        $this->overviewGenerator->addTableOfContent($contentSection, 9);

        // Content
        $this->overviewGenerator->generateObjectPart($contentSection);
        $this->overviewGenerator->generateOrganismIntroductionPart($contentSection);
        $this->overviewGenerator->generateRegistries($contentSection, $treatments, $contractors, $requests, $violations);
        $this->overviewGenerator->generateManagementSystemAndCompliance($contentSection, $maturity, $conformiteTraitements, $mesurements);
        $this->overviewGenerator->generateContinuousImprovements($contentSection);
        $this->overviewGenerator->generateAnnexeMention($contentSection, $treatments);

        return $this->overviewGenerator->generateResponse($this->document, 'bilan');
    }

    /**
     * Generate contractor report.
     *
     * @param array $contractors contractors to use for generation
     *
     * @throws \PhpOffice\PhpWord\Exception\Exception
     * @throws \Exception
     *
     * @return Response The generated Word file
     */
    public function generateRegistryContractorReport(array $contractors = []): Response
    {
        $title = 'Registre des sous-traitants';
        // Initialize document
        $this->contractorGenerator->initializeDocument($this->document);

        // Begin generation
        $this->contractorGenerator->addHomepage($this->document, $title);

        // Section which will get whole content
        $contentSection = $this->contractorGenerator->createContentSection($this->document, $title);

        // Table of content
        $this->contractorGenerator->addTableOfContent($contentSection, 1);

        // Content
        $this->contractorGenerator->addSyntheticView($contentSection, $contractors);
        $this->contractorGenerator->addDetailedView($contentSection, $contractors);

        return $this->contractorGenerator->generateResponse($this->document, 'sous_traitants');
    }

    /**
     * Generate maturity report.
     *
     * @param array $maturityList list of maturity to use for generation. First (old) if exists and second (new)
     *
     * @throws \PhpOffice\PhpWord\Exception\Exception
     * @throws \Exception
     *
     * @return Response The generated Word file
     */
    public function generateMaturitySurveyReport(array $maturityList = []): Response
    {
        $title = 'Indice de maturité';
        // Initialize document
        $this->maturityGenerator->initializeDocument($this->document);

        // Begin generation
        $this->maturityGenerator->addHomepage($this->document, $title);

        // Section which will get whole content
        $contentSection = $this->maturityGenerator->createContentSection($this->document, $title);

        // Table of content
        $this->maturityGenerator->addTableOfContent($contentSection, 1);

        // Content
        $this->maturityGenerator->addSyntheticView($contentSection, $maturityList);
        $this->maturityGenerator->addDetailedView($contentSection, $maturityList);

        return $this->maturityGenerator->generateResponse($this->document, 'indice_de_maturite');
    }

    /**
     * Generate mesurement report.
     *
     * @param array $mesurements mesurements to use for generation
     *
     * @throws \PhpOffice\PhpWord\Exception\Exception
     * @throws \Exception
     *
     * @return Response The generated Word file
     */
    public function generateRegistryMesurementReport(array $mesurements = []): Response
    {
        $title = 'Registre des actions de protection';
        // Initialize document
        $this->mesurementGenerator->initializeDocument($this->document);

        // Begin generation
        $this->mesurementGenerator->addHomepage($this->document, $title);

        // Section which will get whole content
        $contentSection = $this->mesurementGenerator->createContentSection($this->document, $title);

        // Table of content
        $this->mesurementGenerator->addTableOfContent($contentSection, 1);

        // Content
        $this->mesurementGenerator->addSyntheticView($contentSection, $mesurements);
        $this->mesurementGenerator->addDetailedView($contentSection, $mesurements);

        return $this->mesurementGenerator->generateResponse($this->document, 'actions_de_protection');
    }

    /**
     * Generate request report.
     *
     * @param array $requests requests to use for generation
     *
     * @throws \PhpOffice\PhpWord\Exception\Exception
     * @throws \Exception
     *
     * @return Response The generated Word file
     */
    public function generateRegistryRequestReport(array $requests = []): Response
    {
        $title = 'Registre des demandes des personnes concernées';
        // Initialize document
        $this->requestGenerator->initializeDocument($this->document);

        // Begin generation
        $this->requestGenerator->addHomepage($this->document, $title);

        // Section which will get whole content
        $contentSection = $this->requestGenerator->createContentSection($this->document, $title);

        // Table of content
        $this->requestGenerator->addTableOfContent($contentSection, 1);

        // Content
        $this->requestGenerator->addSyntheticView($contentSection, $requests);
        $this->requestGenerator->addDetailedView($contentSection, $requests);

        return $this->requestGenerator->generateResponse($this->document, 'demandes_des_personnes_concernees');
    }

    /**
     * Generate treatment report.
     *
     * @param array $treatments treatments to use for generation
     *
     * @throws \PhpOffice\PhpWord\Exception\Exception
     * @throws \Exception
     *
     * @return Response The generated Word file
     */
    public function generateRegistryTreatmentReport(array $treatments = [])
    {
        $title = 'Registre des traitements';

        // Initialize document
        $this->treatmentGenerator->initializeDocument($this->document);

        // Begin generation
        $this->treatmentGenerator->addHomepage($this->document, $title);

        // Section which will get whole content
        $contentSection = $this->treatmentGenerator->createContentSection($this->document, $title);

        // Table of content
        $this->treatmentGenerator->addTableOfContent($contentSection, 1);

        // Content
        $this->treatmentGenerator->addSyntheticView($contentSection, $treatments);
        $this->treatmentGenerator->addDetailedView($contentSection, $treatments);

        return $this->treatmentGenerator->generateResponse($this->document, 'traitements');
    }

    /**
     * Generate violation report.
     *
     * @param array $treatments treatments to use for generation
     *
     * @throws \PhpOffice\PhpWord\Exception\Exception
     * @throws \Exception
     *
     * @return Response The generated Word file
     */
    public function generateRegistryViolationReport(array $treatments = [])
    {
        $title = 'Registre des violations';

        // Initialize document
        $this->violationGenerator->initializeDocument($this->document);

        // Begin generation
        $this->violationGenerator->addHomepage($this->document, $title);

        // Section which will get whole content
        $contentSection = $this->violationGenerator->createContentSection($this->document, $title);

        // Table of content
        $this->violationGenerator->addTableOfContent($contentSection, 1);

        // Content
        $this->violationGenerator->addSyntheticView($contentSection, $treatments);
        $this->violationGenerator->addDetailedView($contentSection, $treatments);

        return $this->violationGenerator->generateResponse($this->document, 'violations');
    }

    /**
     * Generate conformiteTraitement report.
     *
     * @param array $conformiteTraitements conformiteTraitement to use for generation
     *
     * @throws \PhpOffice\PhpWord\Exception\Exception
     * @throws \Exception
     *
     * @return Response The generated Word file
     */
    public function generateRegistryConformiteTraitementReport(array $conformiteTraitements = []): Response
    {
        $title = 'Diagnostic de la conformité des traitements';
        // Initialize document
        $this->conformiteTraitementGenerator->initializeDocument($this->document);

        // Begin generation
        $this->conformiteTraitementGenerator->addHomepage($this->document, $title);

        // Section which will get whole content
        $contentSection = $this->conformiteTraitementGenerator->createContentSection($this->document, $title);

        // Table of content
        $this->conformiteTraitementGenerator->addTableOfContent($contentSection, 1);

        // Content
        $this->conformiteTraitementGenerator->addSyntheticView($contentSection, $conformiteTraitements);
        $this->conformiteTraitementGenerator->addDetailedView($contentSection, $conformiteTraitements);

        return $this->conformiteTraitementGenerator->generateResponse($this->document, 'conformite_des_traitements');
    }

    public function generateRegistryConformiteOrganisationReport(Evaluation $evaluation): Response
    {
        $title = 'Diagnostic de la conformite de l\'organisation';

        $this->conformiteOrganisationGenerator->initializeDocument($this->document);

        /* Basic generation */
        $this->conformiteOrganisationGenerator->addHomepage($this->document, $title);

        $contentSection = $this->conformiteOrganisationGenerator->createContentSection($this->document, $title);

        /* Table of content */
        $this->conformiteOrganisationGenerator->addTableOfContent($contentSection, 1);

        /* Content */
        $this->conformiteOrganisationGenerator->addSyntheticView($contentSection, \iterable_to_array($evaluation->getConformites()));
        $this->conformiteOrganisationGenerator->addDetailedView($contentSection, [$evaluation]);

        return $this->conformiteOrganisationGenerator->generateResponse($this->document, 'conformite_des_organisations');
    }
}
