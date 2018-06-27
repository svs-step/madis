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

namespace App\Domain\Reporting\Generator;

use App\Domain\User\Dictionary\ContactCivilityDictionary;
use App\Domain\User\Model as UserModel;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class WordGenerator
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
     * @var array
     */
    private $dpo;

    public function __construct(
        PhpWord $document,
        ContractorGenerator $contractorGenerator,
        array $dpo
    ) {
        $this->document            = $document;
        $this->contractorGenerator = $contractorGenerator;
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
    public function generateRegistryContractorReport(array $contractors): Response
    {
        $this->contractorGenerator->generateHeader($this->document);
        $this->contractorGenerator->generateOverview($this->document, $contractors);
        $this->contractorGenerator->generateDetails($this->document, $contractors);

        return $this->contractorGenerator->generateResponse($this->document, 'sous-traitant');
    }

    public function generateHeader(): void
    {
        $this->document->addTitleStyle(
            1,
            [
                'bold'  => true,
                'color' => '3c8dbc',
                'size'  => 20,
            ],
            [
                'alignment'  => \PhpOffice\PhpWord\SimpleType\Jc::CENTER,
                'spaceAfter' => 500,
            ]
        );

        $this->document->addTableStyle(
            'default',
            ['borderColor' => '006699', 'borderSize' => 6, 'cellMargin' => 100, 'width' => 100]
        );

        $this->document->addTitleStyle(
            2,
            ['bold'       => true, 'size' => 15],
            ['spaceAfter' => 200]
        );

        $section = $this->document->addSection();
        $section->addTitle('Bilan RGPD');
        $section->addText("Ce document va retracer l'ensemble des informations que vous avez pu saisir sur la plateforme.");
    }

    public function generateCollectivitySection(UserModel\Collectivity $collectivity)
    {
        $section = $this->document->addSection();

        $section->addTitle('Informations de la collectivité', 2);

        $table = $section->addTable('default');

        // Legal Manager
        $contact  = $collectivity->getLegalManager();
        $civility = ContactCivilityDictionary::getCivilities()[$contact->getCivility()];
        $row      = $table->addRow();
        $cell     = $row->addCell(
            5000,
            ['bgColor' => '3c8dbc', 'valign' => 'center']
        );
        $cell->addText(
            'Coordonnées du responsable de l’organisme',
            ['bold' => true, 'color' => 'ffffff']
        );

        $cell = $row->addCell(
            5000,
            ['valign' => 'center']
        );
        $cell->addText("{$civility} {$contact->getFullName()}", ['bold' => true]);
        $cell->addText($contact->getJob());
        $cell->addText($contact->getMail());
        $cell->addText($contact->getPhoneNumber());

        // DPO
        $dpo         = $collectivity->getReferent();
        $civility    = ContactCivilityDictionary::getCivilities()[$dpo->getCivility() ?? $this->dpo['civility']];
        $firstName   = $dpo->getFirstName() ?? $this->dpo['firstName'];
        $lastName    = $dpo->getFirstName() ?? $this->dpo['lastName'];
        $fullName    = "{$firstName} {$lastName}";
        $company     = $dpo->getFirstName() ? $collectivity->getName() : $this->dpo['company'];
        $job         = $dpo->getJob() ?? $this->dpo['job'];
        $mail        = $dpo->getMail() ?? $this->dpo['mail'];
        $phoneNumber = $dpo->getPhoneNumber() ?? $this->dpo['phoneNumber'];
        $row         = $table->addRow();
        $cell        = $row->addCell(
            5000,
            ['bgColor' => '3c8dbc', 'valign' => 'center']
        );
        $cell->addText(
            'Nom et coordonnées du délégué à la protection des données',
            ['bold' => true, 'color' => 'ffffff']
        );

        $cell = $row->addCell(
            5000,
            ['valign' => 'center']
        );
        $cell->addText("{$civility} {$fullName}", ['bold' => true]);
        $cell->addText($company);
        $cell->addText($job);
        $cell->addText($mail);
        $cell->addText($phoneNumber);
    }

    /**
     * Generate the response
     * - This response parse PhpWord to a Word document
     * - Prepare it in a BinaryFileResponse.
     *
     * @param string $documentName The document name to use
     *
     * @throws \PhpOffice\PhpWord\Exception\Exception
     *
     * @return BinaryFileResponse The response
     */
    public function generateResponse(string $documentName): BinaryFileResponse
    {
        // Save document as tmp OOXML file
        $objWriter = IOFactory::createWriter($this->document, 'Word2007');

        $fileName  = "{$documentName}.docx";
        $temp_file = \tempnam(\sys_get_temp_dir(), $fileName);

        $objWriter->save($temp_file);

        // Create response and return it
        $response = new BinaryFileResponse($temp_file);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $fileName
        );

        return $response;
    }
}
