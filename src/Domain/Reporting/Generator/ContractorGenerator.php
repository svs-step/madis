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

use App\Application\Symfony\Security\UserProvider;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\Style;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class ContractorGenerator
{
    const DATE_TIME_FORMAT = 'd/m/Y à H:i';
    const DATE_TIME_ZONE   = 'Europe/Paris';

    /**
     * @var UserProvider
     */
    private $userProvier;

    /**
     * @var array
     */
    private $tableStyle;

    /**
     * @var array
     */
    private $cellHeadStyle;

    /**
     * @var array
     */
    private $textHeadStyle;

    public function __construct(
        UserProvider $userProvider
    ) {
        $this->userProvier = $userProvider;
    }

    public function generateHeader(PhpWord $document): void
    {
        $document->addTitleStyle(
            2,
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
        $document->addTitleStyle(
            3,
            [
                'bold' => true,
                'size' => 16,
            ],
            [
                'spaceAfter' => 250,
            ]
        );

        $this->tableStyle = [
            'borderColor' => '006699',
            'borderSize'  => 6,
            'cellMargin'  => 100,
            'unit'        => Style\Table::WIDTH_PERCENT,
            'width'       => 100 * 50,
        ];

        $this->cellHeadStyle = [
            'bgColor' => '3c8dbc',
        ];

        $this->textHeadStyle = [
            'bold'  => true,
            'color' => 'ffffff',
        ];

        $currentDateTime = new \DateTimeImmutable('now', new \DateTimeZone(self::DATE_TIME_ZONE));
        $section         = $document->addSection();
        $section->addText(
            'Sous-traitants',
            [
                'bold'  => true,
                'color' => '3c8dbc',
                'size'  => 40,
            ],
            [
                'alignment'   => Jc::CENTER,
                'spaceBefore' => 5000,
            ]
        );
        $section->addText(
            $this->userProvier->getAuthenticatedUser()->getCollectivity(),
            [
                'bold'   => true,
                'italic' => true,
                'size'   => 20,
            ],
            [
                'alignment'   => Jc::CENTER,
                'spaceBefore' => 100,
            ]
        );
        $section->addText(
            "Édition du {$currentDateTime->format(self::DATE_TIME_FORMAT)}",
            [
                'size' => 15,
            ],
            [
                'alignment'   => Jc::CENTER,
                'spaceBefore' => 3000,
            ]
        );
    }

    public function generateOverview(PhpWord $document, array $contractors): void
    {
        $section = $document->addSection();

        $section->addTitle('Liste des sous-traitants', 2);
        $table = $section->addTable($this->tableStyle);

        $table->addRow();
        $cell = $table->addCell(null, $this->cellHeadStyle);
        $cell->addText('Nom', $this->textHeadStyle);
        $cell = $table->addCell(null, $this->cellHeadStyle);
        $cell->addText('Référent', $this->textHeadStyle);
        $cell = $table->addCell(null, $this->cellHeadStyle);
        $cell->addText('Clauses contractuelles vérifiées', $this->textHeadStyle);
        $cell = $table->addCell(null, $this->cellHeadStyle);
        $cell->addText('Conforme RGPD', $this->textHeadStyle);

        /*
         * @var Contractor
         */
        foreach ($contractors as $contractor) {
            $table->addRow();
            $cell = $table->addCell();
            $cell->addText($contractor->getName());
            $cell = $table->addCell();
            $cell->addText($contractor->getReferent());
            $cell = $table->addCell();
            $cell->addText($contractor->isContractualClausesVerified() ? 'Oui' : 'Non');
            $cell = $table->addCell();
            $cell->addText($contractor->isConform() ? 'Oui' : 'Non');
        }
    }

    public function generateDetails(PhpWord $document, array $contractors): void
    {
        foreach ($contractors as $contractor) {
            $section = $document->addSection();
            $section->addTitle($contractor->getName(), 2);

            $section->addTitle('Informations générales', 3);
            $table = $section->addTable($this->tableStyle);
            $table->addRow();
            $cell = $table->addCell(null, $this->cellHeadStyle);
            $cell->addText('Personne référente', $this->textHeadStyle);
            $cell = $table->addCell();
            $cell->addText($contractor->getReferent());
            $table->addRow();
            $cell = $table->addCell(null, $this->cellHeadStyle);
            $cell->addText('Clauses contractuelles vérifiées', $this->textHeadStyle);
            $cell = $table->addCell();
            $cell->addText($contractor->isContractualClausesVerified() ? 'Oui' : 'Non');
            $table->addRow();
            $cell = $table->addCell(null, $this->cellHeadStyle);
            $cell->addText('Conforme RGPD', $this->textHeadStyle);
            $cell = $table->addCell();
            $cell->addText($contractor->isConform() ? 'Oui' : 'Non');
            $table->addRow();
            $cell = $table->addCell(null, $this->cellHeadStyle);
            $cell->addText('Autres inforamtions', $this->textHeadStyle);
            $cell = $table->addCell();
            $cell->addText($contractor->getOtherInformations());

            $section->addTitle('Adresse', 3);
            $table = $section->addTable($this->tableStyle);
            $table->addRow();
            $cell = $table->addCell(null, $this->cellHeadStyle);
            $cell->addText('Adresse', $this->textHeadStyle);
            $cell = $table->addCell();
            $cell->addText($contractor->getAddress()->getLineOne());
            if (!\is_null($contractor->getAddress()->getLineTwo())) {
                $cell->addText($contractor->getAddress()->getLineTwo());
            }
            $cell->addText($contractor->getAddress()->getZipCode());
            $cell->addText($contractor->getAddress()->getCity());
            $table->addRow();
            $cell = $table->addCell(null, $this->cellHeadStyle);
            $cell->addText('Email', $this->textHeadStyle);
            $cell = $table->addCell();
            $cell->addText($contractor->getAddress()->getMail());
            $table->addRow();
            $cell = $table->addCell(null, $this->cellHeadStyle);
            $cell->addText('Numéro de téléphone', $this->textHeadStyle);
            $cell = $table->addCell();
            $cell->addText($contractor->getAddress()->getPhoneNumber());

            $section->addTitle('Historique', 3);
            $table = $section->addTable($this->tableStyle);
            $table->addRow();
            $cell = $table->addCell(null, $this->cellHeadStyle);
            $cell->addText('Créateur', $this->textHeadStyle);
            $cell = $table->addCell();
            $cell->addText($contractor->getCreator());
            $table->addRow();
            $cell = $table->addCell(null, $this->cellHeadStyle);
            $cell->addText('Date de création', $this->textHeadStyle);
            $cell = $table->addCell();
            $cell->addText($contractor->getCreatedAt()->format(self::DATE_TIME_FORMAT));
            $table->addRow();
            $cell = $table->addCell(null, $this->cellHeadStyle);
            $cell->addText('Dernière mise à jour', $this->textHeadStyle);
            $cell = $table->addCell();
            $cell->addText($contractor->getUpdatedAt()->format(self::DATE_TIME_FORMAT));
        }
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
    public function generateResponse(PhpWord $document, string $documentName): BinaryFileResponse
    {
        // Save document as tmp OOXML file
        $objWriter = IOFactory::createWriter($document, 'Word2007');

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
