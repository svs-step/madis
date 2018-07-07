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
use App\Domain\Reporting\Generator\Word\TreatmentGenerator;
use PhpOffice\PhpWord\PhpWord;
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
     * @var TreatmentGenerator
     */
    private $treatmentGenerator;

    /**
     * @var array
     */
    private $dpo;

    public function __construct(
        PhpWord $document,
        ContractorGenerator $contractorGenerator,
        TreatmentGenerator $treatmentGenerator,
        array $dpo
    ) {
        $this->document            = $document;
        $this->contractorGenerator = $contractorGenerator;
        $this->treatmentGenerator  = $treatmentGenerator;
        $this->dpo                 = $dpo;
    }

    /**
     * Generate contractor report.
     *
     * @param array $contractors contractors to use for generation
     *
     * @throws \PhpOffice\PhpWord\Exception\Exception
     *
     * @return Response The generated Word file
     */
    public function generateRegistryContractorReport(array $contractors = []): Response
    {
        $this->contractorGenerator->generateHeader($this->document, 'Registre des sous-traitants');
        $this->contractorGenerator->generateOverview($this->document, $contractors);
        $this->contractorGenerator->generateDetails($this->document, $contractors);

        return $this->contractorGenerator->generateResponse($this->document, 'sous-traitants');
    }

    /**
     * Generate treatment report.
     *
     * @param array $treatments treatments to use for generation
     *
     * @throws \PhpOffice\PhpWord\Exception\Exception
     *
     * @return Response The generated Word file
     */
    public function generateRegistryTreatmentReport(array $treatments = [])
    {
        $this->treatmentGenerator->generateHeader($this->document, 'Registre des traitements');
        $this->treatmentGenerator->generateOverview($this->document, $treatments);
        $this->treatmentGenerator->generateDetails($this->document, $treatments);

        return $this->treatmentGenerator->generateResponse($this->document, 'traitements');
    }
}
