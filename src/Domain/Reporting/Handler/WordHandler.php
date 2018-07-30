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

namespace App\Domain\Reporting\Handler;

use App\Domain\Reporting\Generator\Word\ContractorGenerator;
use App\Domain\Reporting\Generator\Word\MaturityGenerator;
use App\Domain\Reporting\Generator\Word\MesurementGenerator;
use App\Domain\Reporting\Generator\Word\OverviewGenerator;
use App\Domain\Reporting\Generator\Word\TreatmentGenerator;
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
     * @var TreatmentGenerator
     */
    private $treatmentGenerator;

    public function __construct(
        PhpWord $document,
        ContractorGenerator $contractorGenerator,
        MaturityGenerator $maturityGenerator,
        MesurementGenerator $mesurementGenerator,
        OverviewGenerator $overviewGenerator,
        TreatmentGenerator $treatmentGenerator
    ) {
        $this->document            = $document;
        $this->contractorGenerator = $contractorGenerator;
        $this->overviewGenerator   = $overviewGenerator;
        $this->maturityGenerator   = $maturityGenerator;
        $this->mesurementGenerator = $mesurementGenerator;
        $this->treatmentGenerator  = $treatmentGenerator;
    }

    /**
     * Generate overview report.
     *
     * @param array $treatments  Treatments used for overview report generation
     * @param array $contractors Contractors used for overview report generation
     * @param array $maturity    Surveys maturity used for overview report generation
     *
     * @throws \PhpOffice\PhpWord\Exception\Exception
     *
     * @return BinaryFileResponse The generated Word document
     */
    public function generateOverviewReport(
        array $treatments = [],
        array $contractors = [],
        array $maturity = []
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
        $this->overviewGenerator->generateRegistries($contentSection, $treatments, $contractors);
        $this->overviewGenerator->generateManagementSystemAndCompliance($contentSection, $maturity);

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
}
